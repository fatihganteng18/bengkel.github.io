<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    private const LOW_STOCK_THRESHOLD = 5;

    public function index(): View
    {
        $firstMonth = now()->startOfMonth()->subMonths(5);
        $monthlyMetrics = collect(range(0, 5))
            ->mapWithKeys(fn (int $offset) => [$firstMonth->copy()->addMonths($offset)->format('Y-m') => [
                'transactions' => 0,
                'revenue' => 0.0,
            ]]);

        Payment::query()
            ->where('created_at', '>=', $firstMonth)
            ->get(['created_at'])
            ->each(function (Payment $payment) use ($monthlyMetrics): void {
                $month = Carbon::parse($payment->created_at)->format('Y-m');

                if (! $monthlyMetrics->has($month)) {
                    return;
                }

                $metrics = $monthlyMetrics->get($month);
                $metrics['transactions']++;
                $monthlyMetrics->put($month, $metrics);
            });

        Payment::query()
            ->where('status', 'paid')
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', $firstMonth)
            ->get(['paid_at', 'amount'])
            ->each(function (Payment $payment) use ($monthlyMetrics): void {
                $month = Carbon::parse($payment->paid_at)->format('Y-m');

                if (! $monthlyMetrics->has($month)) {
                    return;
                }

                $metrics = $monthlyMetrics->get($month);
                $metrics['revenue'] += (float) ($payment->amount ?? 0);
                $monthlyMetrics->put($month, $metrics);
            });

        $bookings = Booking::query();
        $bestSellers = Product::query()
            ->whereHas('bookings', fn ($query) => $query
                ->where(function ($query): void {
                    $query->where('status', 'completed')
                        ->orWhereHas('payment', fn ($paymentQuery) => $paymentQuery->where('status', 'paid'));
                }))
            ->withCount(['bookings' => fn ($query) => $query
                ->where(function ($query): void {
                    $query->where('status', 'completed')
                        ->orWhereHas('payment', fn ($paymentQuery) => $paymentQuery->where('status', 'paid'));
                })])
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();
        $stockProducts = Product::query()
            ->orderBy('stock')
            ->orderBy('name')
            ->limit(8)
            ->get(['name', 'stock']);
        $lowStockProducts = Product::query()
            ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
            ->orderBy('stock')
            ->limit(8)
            ->get();

        return view('admin.dashboard', [
            'chartLabels' => $monthlyMetrics->keys()->map(
                fn (string $month) => Carbon::createFromFormat('Y-m', $month)->translatedFormat('M Y')
            )->values(),
            'transactionCounts' => $monthlyMetrics->pluck('transactions')->values(),
            'monthlyRevenue' => $monthlyMetrics->pluck('revenue')->map(fn (float $amount) => round($amount, 2))->values(),
            'revenueTotal' => $monthlyMetrics->sum('revenue'),
            'bestSellers' => $bestSellers,
            'stockProducts' => $stockProducts,
            'lowStockProducts' => $lowStockProducts,
            'totalBookings' => (clone $bookings)->count(),
            'pendingBookings' => (clone $bookings)->where('status', 'pending')->count(),
            'pendingPayments' => Payment::query()->where('status', 'pending')->count(),
            'lowStockCount' => Product::query()->where('stock', '<=', self::LOW_STOCK_THRESHOLD)->count(),
            'lowStockThreshold' => self::LOW_STOCK_THRESHOLD,
        ]);
    }

    public function bookings(): View
    {
        $bookings = Booking::with(['store', 'product', 'payment'])->latest()->get();

        return view('admin-bookings', compact('bookings'));
    }

    public function updateBookingStatus(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,completed'],
            'payment_status' => ['sometimes', 'required', 'in:pending,paid,failed'],
        ]);

        DB::transaction(function () use ($booking, $validated): void {
            $booking->update(['status' => $validated['status']]);

            if (isset($validated['payment_status']) && $booking->payment) {
                $booking->payment->update([
                    'status' => $validated['payment_status'],
                    'paid_at' => $validated['payment_status'] === 'paid' ? now() : null,
                ]);
            }
        });

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }
}
