<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk #{{ $booking->id }} | Planet Ban</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 text-slate-900 sm:p-8">
    <main class="mx-auto max-w-lg rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
        <header class="border-b border-dashed border-slate-300 pb-5 text-center">
            <p class="text-xl font-black italic">PLANET BAN</p>
            <p class="mt-1 text-xs text-slate-500">{{ $booking->store->name }} · {{ $booking->store->address }}</p>
            <h1 class="mt-5 text-lg font-black">{{ $booking->booking_type === \App\Models\Booking::TYPE_COUNTER_SALE ? 'STRUK TRANSAKSI KASIR' : 'STRUK PEMESANAN' }}</h1>
            <p class="mt-1 text-xs text-slate-500">Booking #{{ $booking->id }} · {{ $booking->created_at->format('d/m/Y H:i') }}</p>
            @if ($booking->serviceQueue)
                <p class="mt-3 text-3xl font-black text-blue-950">{{ $booking->serviceQueue->queue_code }}</p>
            @endif
        </header>

        <section class="space-y-1 border-b border-dashed border-slate-300 py-4 text-sm">
            <p><span class="font-bold">Pelanggan:</span> {{ $booking->customer_name }}</p>
            <p><span class="font-bold">WhatsApp:</span> {{ $booking->customer_phone }}</p>
            @if ($booking->booking_type === \App\Models\Booking::TYPE_COUNTER_SALE)
                <p><span class="font-bold">Waktu transaksi:</span> {{ $booking->created_at->format('d/m/Y H:i') }}</p>
            @else
                <p><span class="font-bold">Kunjungan:</span> {{ $booking->booking_date }} · {{ $booking->booking_time }}</p>
                <p><span class="font-bold">Status booking:</span> {{ ucfirst($booking->status) }}</p>
            @endif
        </section>

        <section class="space-y-3 border-b border-dashed border-slate-300 py-4 text-sm">
            @forelse ($booking->items as $item)
                <div class="flex justify-between gap-4">
                    <span>{{ $item->product_name }} × {{ $item->quantity }}</span>
                    <span class="shrink-0">Rp {{ number_format((float) $item->unit_price * $item->quantity, 0, ',', '.') }}</span>
                </div>
            @empty
                <p>Servis umum</p>
            @endforelse
        </section>

        <section class="space-y-2 py-4 text-sm">
            <div class="flex justify-between gap-4 font-black">
                <span>Total produk</span>
                <span>{{ $booking->payment?->amount !== null ? 'Rp '.number_format((float) $booking->payment->amount, 0, ',', '.') : 'Dikonfirmasi di outlet' }}</span>
            </div>
            <p><span class="font-bold">Metode bayar:</span> {{ $booking->payment?->method_label ?? '-' }}</p>
            <p><span class="font-bold">Status pembayaran:</span> {{ match ($booking->payment?->status) {'paid' => 'Lunas', 'failed' => 'Gagal', 'pending' => 'Menunggu konfirmasi', default => '-'} }}</p>
        </section>

        <p class="border-t border-dashed border-slate-300 pt-4 text-center text-xs text-slate-500">Terima kasih telah mempercayakan perawatan kendaraan Anda kepada Planet Ban.</p>
        <div class="no-print mt-6 flex gap-3">
            <button type="button" onclick="window.print()" class="flex-1 rounded-xl bg-slate-950 px-4 py-3 text-sm font-bold text-white hover:bg-slate-800">Cetak struk</button>
            <a href="{{ route('cashir.dashboard') }}" class="rounded-xl border border-slate-300 px-4 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50">Kembali</a>
        </div>
    </main>
</body>
</html>
