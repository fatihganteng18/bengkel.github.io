<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ServiceQueue;
use App\Models\Store;
use App\Services\ServiceQueueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CashierPanelController extends Controller
{
    public function index(): View
    {
        $today = now()->toDateString();
        $bookings = Booking::query()
            ->with(['store', 'items.product', 'product', 'payment', 'serviceQueue'])
            ->where('booking_type', Booking::TYPE_SERVICE)
            ->whereIn('status', ['pending', 'confirmed'])
            ->latest()
            ->limit(100)
            ->get();
        $queues = ServiceQueue::query()
            ->with(['store', 'booking.payment', 'booking.items.product'])
            ->whereDate('queue_date', '>=', $today)
            ->where(function ($query) use ($today): void {
                $query->whereIn('status', [ServiceQueue::STATUS_WAITING, ServiceQueue::STATUS_IN_SERVICE])
                    ->orWhere(function ($completed) use ($today): void {
                        $completed->whereDate('queue_date', $today)
                            ->where('status', ServiceQueue::STATUS_COMPLETED);
                    });
            })
            ->orderBy('queue_date')
            ->orderByRaw("CASE status WHEN 'in_service' THEN 0 WHEN 'waiting' THEN 1 ELSE 2 END")
            ->orderBy('queue_number')
            ->get();

        return view('cashier.dashboard', [
            'bookings' => $bookings,
            'queues' => $queues,
            'pendingPayments' => Payment::query()->where('status', 'pending')->count(),
            'waitingCount' => $queues->where('status', ServiceQueue::STATUS_WAITING)->count(),
            'inServiceCount' => $queues->where('status', ServiceQueue::STATUS_IN_SERVICE)->count(),
            'stores' => Store::query()->orderBy('name')->get(),
            'posProducts' => Product::query()
                ->where('stock', '>', 0)
                ->orderBy('category')
                ->orderBy('name')
                ->get()
                ->map(fn (Product $product): array => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category_label,
                    'price' => $product->effective_price,
                    'stock' => $product->stock,
                ])
                ->values(),
        ]);
    }

    public function storePointOfSale(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+() -]{8,20}$/'],
            'store_id' => ['required', 'exists:stores,id'],
            'products' => ['required', 'array', 'min:1', 'max:30'],
            'products.*.id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'payment_method' => [
                'required',
                'in:outlet_cash,outlet_qris,bank_bca,bank_mandiri,ewallet_gopay,ewallet_ovo,ewallet_dana',
            ],
        ]);

        $booking = DB::transaction(function () use ($validated): Booking {
            $requestedItems = collect($validated['products']);
            $products = Product::query()
                ->whereIn('id', $requestedItems->pluck('id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');
            $amount = 0;

            foreach ($requestedItems as $index => $item) {
                $product = $products->get($item['id']);

                if (! $product || $item['quantity'] > $product->stock) {
                    throw ValidationException::withMessages([
                        "products.{$index}.quantity" => 'Stok '.($product?->name ?? 'produk').' tidak mencukupi.',
                    ]);
                }

                $amount += $product->effective_price * $item['quantity'];
            }

            $booking = Booking::create([
                'customer_name' => filled($validated['customer_name'] ?? null)
                    ? $validated['customer_name']
                    : 'Pelanggan umum',
                'customer_phone' => $validated['customer_phone'] ?? '-',
                'store_id' => $validated['store_id'],
                'product_id' => $requestedItems->count() === 1 ? $requestedItems->first()['id'] : null,
                'booking_type' => Booking::TYPE_COUNTER_SALE,
                'booking_date' => now()->toDateString(),
                'booking_time' => now()->format('H:i'),
                'status' => 'confirmed',
            ]);

            foreach ($requestedItems as $item) {
                $product = $products->get($item['id']);
                $quantity = (int) $item['quantity'];

                $booking->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->effective_price,
                    'quantity' => $quantity,
                ]);
                $product->decrement('stock', $quantity);
                $product->increment('sold_count', $quantity);
            }

            Payment::create([
                'booking_id' => $booking->id,
                'method' => $validated['payment_method'],
                'amount' => $amount,
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            return $booking;
        });

        return redirect()
            ->route('cashir.bookings.receipt', $booking)
            ->with('success', 'Transaksi kasir berhasil disimpan dan pembayaran tercatat lunas.');
    }

    public function updateBooking(
        Request $request,
        Booking $booking,
        ServiceQueueService $serviceQueueService,
    ): RedirectResponse {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed'],
            'payment_status' => ['nullable', 'in:pending,paid,failed'],
        ]);

        DB::transaction(function () use ($booking, $validated, $serviceQueueService): void {
            $booking = Booking::query()->lockForUpdate()->findOrFail($booking->id);
            $booking->update(['status' => $validated['status']]);

            if (isset($validated['payment_status']) && $booking->payment) {
                $booking->payment->update([
                    'status' => $validated['payment_status'],
                    'paid_at' => $validated['payment_status'] === 'paid' ? now() : null,
                ]);
            }

            if (in_array($validated['status'], ['confirmed', 'completed'], true)) {
                $serviceQueueService->ensureForConfirmedBooking($booking->fresh());
            }
        });

        return back()->with('success', 'Pesanan berhasil diperbarui.');
    }

    public function updateQueueStatus(Request $request, ServiceQueue $serviceQueue): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:waiting,in_service,completed'],
        ]);

        DB::transaction(function () use ($serviceQueue, $validated): void {
            $queue = ServiceQueue::query()->lockForUpdate()->findOrFail($serviceQueue->id);
            $nextStatus = $validated['status'];
            if ($nextStatus === ServiceQueue::STATUS_IN_SERVICE
                && $queue->queue_date->toDateString() > now()->toDateString()) {
                throw ValidationException::withMessages([
                    'status' => 'Antrean hanya dapat dipanggil pada tanggal kunjungan.',
                ]);
            }

            $queue->update([
                'status' => $nextStatus,
                'called_at' => $nextStatus === ServiceQueue::STATUS_IN_SERVICE
                    ? ($queue->called_at ?? now())
                    : $queue->called_at,
                'completed_at' => $nextStatus === ServiceQueue::STATUS_COMPLETED ? now() : null,
            ]);

            if ($nextStatus === ServiceQueue::STATUS_COMPLETED) {
                $queue->booking()->update(['status' => 'completed']);
            } elseif ($queue->booking->status === 'completed') {
                $queue->booking()->update(['status' => 'confirmed']);
            }
        });

        return back()->with('success', 'Status antrean '.$serviceQueue->fresh()->queue_code.' berhasil diperbarui.');
    }

    public function receipt(Booking $booking): View
    {
        $booking->load(['store', 'product', 'items', 'payment', 'serviceQueue']);

        return view('cashier.receipt', compact('booking'));
    }
}
