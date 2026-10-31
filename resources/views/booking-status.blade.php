<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planet Ban - Cek Status Booking</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen font-sans">

    <header class="bg-white shadow-xs border-b border-gray-100 py-4 px-6 flex justify-between items-center">
        <a href="/home" class="bg-blue-900 text-white font-black italic px-3 py-1 rounded-lg text-lg">
            PLANET <span class="text-yellow-400">BAN</span>
        </a>
        <a href="/home" class="text-sm font-bold text-blue-900 hover:underline">&larr; Kembali ke Beranda</a>
    </header>

    <main class="container mx-auto px-4 py-12 flex-grow max-w-xl">
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-md border border-gray-200">
            <h2 class="text-2xl font-black text-gray-900 mb-2">Cek Status Booking Servis</h2>
            <p class="text-xs text-gray-500 mb-6">Masukkan nomor HP/WhatsApp yang Anda gunakan saat melakukan booking.</p>

            @if (session('success'))
                <div role="status" class="mb-5 rounded-xl border border-green-200 bg-green-50 p-3 text-sm font-semibold text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('booking.status') }}" method="GET" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nomor WhatsApp / HP</label>
                    <input type="tel" name="phone" value="{{ request('phone') }}" required placeholder="Contoh: 081234567890" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-900 focus:outline-none">
                </div>
                <button type="submit" class="w-full bg-blue-900 hover:bg-blue-800 text-white font-bold py-2.5 rounded-xl text-sm shadow">
                    Cari Pesanan
                </button>
            </form>

            @if(request()->has('phone'))
                <hr class="my-6 border-gray-200">

                @if($booking)
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-xs font-bold text-gray-400">ID Booking: #{{ $booking->id }}</span>
                        <span class="text-xs font-black px-3 py-1 rounded-full uppercase
                            {{ $booking->status === 'completed' ? 'bg-green-100 text-green-800' : ($booking->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                            Status: {{ $booking->status }}
                        </span>
                    </div>

                    <h4 class="font-bold text-gray-900 text-lg mb-1">{{ $booking->customer_name }}</h4>
                    <p class="text-xs text-gray-600 mb-3">📍 Outlet: <span class="font-semibold">{{ $booking->store->name ?? '-' }}</span></p>

                    <div class="bg-white p-3 rounded-xl border border-gray-200 text-xs space-y-1">
                        <p>📦 <strong>Produk:</strong> {{ $booking->product->name ?? 'Hanya Servis / Tanpa Produk' }}</p>
                        <p>📅 <strong>Jadwal:</strong> {{ $booking->booking_date }} (Jam {{ $booking->booking_time }})</p>
                        @if ($booking->payment)
                            <hr class="my-2 border-gray-100">
                            <p><strong>Pembayaran:</strong> {{ $booking->payment->method_label }}</p>
                            <p><strong>Status pembayaran:</strong> {{ ucfirst($booking->payment->status) }}</p>
                            @if ($booking->payment->amount !== null)
                                <p><strong>Nilai produk:</strong> Rp {{ number_format((float) $booking->payment->amount, 0, ',', '.') }}</p>
                            @else
                                <p class="text-gray-500">Nilai servis dikonfirmasi oleh outlet.</p>
                            @endif
                        @endif
                    </div>
                </div>
                @else
                <div class="bg-red-50 text-red-700 p-4 rounded-xl text-xs font-semibold text-center border border-red-200">
                    Data booking dengan nomor HP tersebut tidak ditemukan.
                </div>
                @endif
            @endif
        </div>
    </main>

    @include('components.site-footer')
</body>
</html>