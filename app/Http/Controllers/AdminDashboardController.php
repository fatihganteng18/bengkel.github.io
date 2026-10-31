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
        $monthlyCounts = collect(range(0, 5))
            ->mapWithKeys(fn (int $offset) => [$firstMonth->copy()->addMonths($offset)->format('Y-m') => [
                'transactions' => 0,
            ]]);

        Payment::query()
            ->where('created_at', '>=', $firstMonth)
            ->get(['created_at'])
            ->each(function (Payment $payment) use ($monthlyCounts): void {
                $month = Carbon::parse($payment->created_at)->format('Y-m');

                if (! $monthlyCounts->has($month)) {
                    return;
                }

                $counts = $monthlyCounts->get($month);
                $counts['transactions']++;
                $monthlyCounts->put($month, $counts);
            });

        $bookings = Booking::query();
        $bestSellers = Product::query()
            ->whereHas('bookings')
            ->withCount('bookings')
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();
        $lowStockProducts = Product::query()
            ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
            ->orderBy('stock')
            ->limit(8)
            ->get();

        return view('admin.dashboard', [
            'chartLabels' => $monthlyCounts->keys()->map(
                fn (string $month) => Carbon::createFromFormat('Y-m', $month)->translatedFormat('M Y')
            )->values(),
            'transactionCounts' => $monthlyCounts->pluck('transactions')->values(),
            'bestSellers' => $bestSellers,
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
