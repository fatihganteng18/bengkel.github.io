<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Motorcycle;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanetBanController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $stores = Store::all();
        $motorcycles = Motorcycle::all();

        return view('planet-ban', compact('products', 'stores', 'motorcycles'));
    }

    public function showProduct($id)
    {
        $product = Product::findOrFail($id);
        $stores = Store::all();

        $relatedProducts = Product::where('category', $product->category)
            ->where('id', '!=', $id)
            ->take(4)
            ->get();

        return view('product-detail', compact('product', 'stores', 'relatedProducts'));
    }

    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'store_id' => 'required|exists:stores,id',
            'product_id' => 'nullable|exists:products,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => ['required', 'in:09:00,11:00,13:00,15:00,16:00,17:00'],
            'payment_method' => [
                'required',
                'in:outlet_cash,outlet_qris,bank_bca,bank_mandiri,ewallet_gopay,ewallet_ovo,ewallet_dana',
            ],
        ]);

        $booking = DB::transaction(function () use ($validated, $request): Booking {
            $booking = Booking::create([
                'user_id' => $request->user()?->id,
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'store_id' => $validated['store_id'],
                'product_id' => $validated['product_id'] ?? null,
                'booking_date' => $validated['booking_date'],
                'booking_time' => $validated['booking_time'],
                'status' => 'pending',
            ]);

            $product = isset($validated['product_id'])
                ? Product::findOrFail($validated['product_id'])
                : null;

            Payment::create([
                'booking_id' => $booking->id,
                'method' => $validated['payment_method'],
                'amount' => $product?->effective_price,
                'status' => 'pending',
            ]);

            return $booking;
        });

        return redirect()->route('booking.status', ['phone' => $booking->customer_phone])
            ->with('success', 'Booking tersimpan. Pembayaran berstatus menunggu konfirmasi; belum ada pembayaran online yang diproses.');
    }

    public function bookingStatus(Request $request)
    {
        $booking = null;
        if ($request->has('phone')) {
            $booking = Booking::with(['store', 'product', 'payment'])
                ->where('customer_phone', $request->phone)
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
}
