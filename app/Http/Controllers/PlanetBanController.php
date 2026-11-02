<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Motorcycle;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlanetBanController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $stores = Store::all();
        $motorcycles = Motorcycle::all();
        $bookingItems = $this->restoreBookingItems();

        return view('planet-ban', compact('products', 'stores', 'motorcycles', 'bookingItems'));
    }

    public function showProduct($id)
    {
        $product = Product::findOrFail($id);
        $stores = Store::all();

        preg_match('/(\d{2,3})\s*\/\s*(\d{2})\s*[- ]?\s*(\d{2})/', $product->name, $tireSizeParts);
        $tireSize = isset($tireSizeParts[3])
            ? "{$tireSizeParts[1]}/{$tireSizeParts[2]}-{$tireSizeParts[3]}"
            : null;
        $compatibleMotorcycles = $tireSize
            ? Motorcycle::query()
                ->where('front_tire_size', $tireSize)
                ->orWhere('rear_tire_size', $tireSize)
                ->orderBy('brand')
                ->orderBy('model_name')
                ->get()
            : collect();
        $galleryImages = collect($product->gallery_images ?? [])
            ->prepend($product->image)
            ->filter(fn ($image): bool => is_string($image) && trim($image) !== '')
            ->unique()
            ->values();
        $completedBookings = Booking::query()
            ->where(function ($query): void {
                $query->where('status', 'completed')
                    ->orWhereHas('payment', fn ($paymentQuery) => $paymentQuery->where('status', 'paid'));
            })
            ->where(function ($query) use ($product): void {
                $query->where('product_id', $product->id)
                    ->orWhereHas('items', fn ($itemQuery) => $itemQuery->where('product_id', $product->id));
            })
            ->count();
        $soldCount = max((int) $product->sold_count, $completedBookings);
        $bookingItems = $this->restoreBookingItems();

        $relatedProducts = Product::where('category', $product->category)
            ->where('id', '!=', $id)
            ->take(4)
            ->get();

        return view('product-detail', compact(
            'product',
            'stores',
            'relatedProducts',
            'tireSize',
            'compatibleMotorcycles',
            'galleryImages',
            'soldCount',
            'bookingItems',
        ));
    }

    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'store_id' => 'required|exists:stores,id',
            'product_id' => 'nullable|exists:products,id',
            'products' => 'sometimes|array|min:1',
            'products.*.id' => 'required|integer|distinct|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => ['required', 'in:09:00,11:00,13:00,15:00,16:00,17:00'],
            'payment_method' => [
                'required',
                'in:outlet_cash,outlet_qris,bank_bca,bank_mandiri,ewallet_gopay,ewallet_ovo,ewallet_dana',
            ],
        ]);

        $booking = DB::transaction(function () use ($validated, $request): Booking {
            $requestedItems = $validated['products'] ?? (
                isset($validated['product_id'])
                    ? [['id' => $validated['product_id'], 'quantity' => 1]]
                    : []
            );
            $products = collect();

            if ($requestedItems !== []) {
                $products = Product::query()
                    ->whereIn('id', collect($requestedItems)->pluck('id'))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                foreach ($requestedItems as $index => $item) {
                    $product = $products->get($item['id']);

                    if (! $product || $item['quantity'] > $product->stock) {
                        throw ValidationException::withMessages([
                            "products.{$index}.quantity" => 'Stok '.($product?->name ?? 'produk').' tidak mencukupi.',
                        ]);
                    }
                }
            }

            $booking = Booking::create([
                'user_id' => $request->user()?->id,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'store_id' => $validated['store_id'],
                'product_id' => count($requestedItems) === 1 ? $requestedItems[0]['id'] : null,
                'booking_date' => $validated['booking_date'],
                'booking_time' => $validated['booking_time'],
                'status' => 'pending',
            ]);

            $amount = 0;
            foreach ($requestedItems as $item) {
                $product = $products->get($item['id']);
                $booking->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->effective_price,
                    'quantity' => $item['quantity'],
                ]);
                $amount += $product->effective_price * $item['quantity'];
            }

            Payment::create([
                'booking_id' => $booking->id,
                'method' => $validated['payment_method'],
                'amount' => $requestedItems === [] ? null : $amount,
                'status' => 'pending',
            ]);

            return $booking;
        });

        return redirect()->route('booking.status', [
            'booking_id' => $booking->id,
            'phone' => $booking->customer_phone,
        ])
            ->with('success', 'Booking tersimpan. Pembayaran berstatus menunggu konfirmasi; belum ada pembayaran online yang diproses.');
    }

    public function bookingStatus(Request $request)
    {
        $booking = null;
        if ($request->filled('phone')) {
            $booking = Booking::with(['store', 'product', 'items.product', 'payment', 'serviceQueue'])
                ->where('customer_phone', $request->string('phone')->toString())
                ->when($request->filled('booking_id'), fn ($query) => $query->whereKey($request->integer('booking_id')))
                ->latest()
                ->first();
        }

        return view('booking-status', compact('booking'));
    }

    public function storeLocations()
    {
        $stores = Store::all();

        return view('stores-location', compact('stores'));
    }

    public function aboutUs()
    {
        return view('footer.about');
    }

    public function faq()
    {
        return view('footer.faq');
    }

    public function blog()
    {
        return view('footer.blog');
    }

    public function career()
    {
        return view('footer.career');
    }

    public function privacyPolicy()
    {
        return view('footer.privacy');
    }

    public function copyrightPolicy()
    {
        return view('footer.copyright');
    }

    public function terms()
    {
        return view('footer.terms');
    }

    private function restoreBookingItems(): array
    {
        $oldItems = old('products');

        if (! is_array($oldItems) && old('product_id')) {
            $oldItems = [['id' => old('product_id'), 'quantity' => 1]];
        }

        $items = collect(is_array($oldItems) ? $oldItems : [])
            ->filter(fn ($item): bool => is_array($item) && isset($item['id']));
        $products = Product::query()
            ->whereIn('id', $items->pluck('id'))
            ->get()
            ->keyBy('id');

        return $items
            ->map(function ($item) use ($products): ?array {
                $product = $products->get($item['id']);

                if (! $product) {
                    return null;
                }

                return [
                    'id' => (string) $product->id,
                    'name' => $product->name,
                    'price' => $product->effective_price,
                    'quantity' => max(1, (int) ($item['quantity'] ?? 1)),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}
