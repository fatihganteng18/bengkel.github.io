<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planet Ban - Cek Status Booking</title>
    @if ($booking?->payment?->uses_demo_qr && $booking->payment->status === 'pending')
        <meta http-equiv="refresh" content="15">
    @endif
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
                    @if ($booking->serviceQueue)
                        <p class="mb-3 rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-bold text-blue-900">
                            Antrean servis: {{ $booking->serviceQueue->queue_code }} ·
                            {{ $booking->serviceQueue->status === 'in_service' ? 'Sedang dikerjakan' : ($booking->serviceQueue->status === 'completed' ? 'Selesai' : 'Menunggu') }}
                        </p>
                    @endif

                    <div class="bg-white p-3 rounded-xl border border-gray-200 text-xs space-y-1">
                        <div>
                            <strong>Produk:</strong>
                            @forelse($booking->items as $item)
                                <span class="block">{{ $item->product_name }} × {{ $item->quantity }} — Rp {{ number_format((float) $item->unit_price * $item->quantity, 0, ',', '.') }}</span>
                            @empty
                                <span>{{ $booking->product->name ?? 'Hanya Servis / Tanpa Produk' }}</span>
                            @endforelse
                        </div>
                        <p>📅 <strong>Jadwal:</strong> {{ $booking->booking_date }} (Jam {{ $booking->booking_time }})</p>
                        @if ($booking->payment)
                            <hr class="my-2 border-gray-100">
                            <p><strong>Pembayaran:</strong> {{ $booking->payment->method_label }}</p>
                            <p><strong>Status pembayaran:</strong> {{ ucfirst($booking->payment->status) }}</p>
                            @if ($booking->payment->amount !== null)
                                <p><strong>Total produk:</strong> Rp {{ number_format((float) $booking->payment->amount, 0, ',', '.') }}</p>
                            @else
                                <p class="text-gray-500">Nilai servis dikonfirmasi oleh outlet.</p>
                            @endif

                            @if ($booking->payment->uses_demo_qr)
                                <section class="mt-4 rounded-xl border border-amber-300 bg-amber-50 p-4 text-center">
                                    <h3 class="text-sm font-extrabold text-amber-950">QR pembayaran contoh</h3>
                                    <p class="mt-1 text-xs leading-relaxed text-amber-900">QR ini hanya mockup untuk demo dan tidak dapat digunakan untuk membayar. Pembayaran QRIS / e-wallet sebenarnya belum terhubung.</p>
                                    <div class="mx-auto mt-4 flex h-48 w-48 items-center justify-center rounded-lg border-4 border-white bg-white p-3 shadow-sm" role="img" aria-label="QR tiruan, hanya contoh dan tidak dapat dipindai">
                                        <svg viewBox="0 0 160 160" class="h-full w-full" aria-hidden="true">
                                            <rect width="160" height="160" fill="white"/>
                                            <g fill="#111827">
                                                <path d="M8 8h48v48H8zm8 8v32h32V16zm8 8h16v16H24zM104 8h48v48h-48zm8 8v32h32V16zm8 8h16v16h-16zM8 104h48v48H8zm8 8v32h32v-32zm8 8h16v16H24z"/>
                                                <path d="M64 8h8v8h-8zm16 0h8v16h-8zm16 8h8v8h-8zM64 32h16v8H64zm24 8h8v16h-8zm16-8h8v16h-8zm16 0h8v8h-8zm16 16h8v8h-8zM64 56h8v16h-8zm16 8h16v8H80zm24 8h8v16h-8zm16-8h8v16h-8zm16 8h8v8h-8zM8 64h8v8H8zm16 0h16v8H24zm24 0h8v16h-8zm-32 16h8v8h-8zm16 0h16v8H32zm32 0h8v8h-8zm16 8h8v8h-8zm16 0h8v16h-8zm24-8h8v8h-8zm16 8h8v16h-8zm-72 8h8v8h-8zm16 8h8v8h-8zm16 8h8v8h-8zm16 0h16v8h-16zm24 8h8v8h-8zm-48 16h8v8h-8zm16-8h8v16h-8zm16 8h8v8h-8zm16 0h8v16h-8z"/>
                                            </g>
                                            <rect x="57" y="67" width="48" height="26" rx="4" fill="white" stroke="#f59e0b" stroke-width="2"/>
                                            <text x="81" y="84" text-anchor="middle" fill="#92400e" font-size="12" font-weight="900">DEMO</text>
                                        </svg>
                                    </div>
                                    <p class="mt-3 text-xs font-semibold text-amber-950">Jangan transfer atau memindai QR ini.</p>
                                </section>
                            @endif

                            @if ($booking->payment->status === 'paid')
                                <p role="status" class="mt-3 rounded-lg border border-green-200 bg-green-50 p-3 text-xs font-semibold text-green-800">
                                    Pembayaran telah dikonfirmasi oleh admin{{ $booking->payment->paid_at ? ' pada '.$booking->payment->paid_at->format('d/m/Y H:i').' WIB' : '' }}.
                                </p>
                            @elseif ($booking->payment->status === 'failed')
                                <p role="status" class="mt-3 rounded-lg border border-red-200 bg-red-50 p-3 text-xs font-semibold text-red-800">Pembayaran belum berhasil. Silakan hubungi outlet untuk bantuan.</p>
                            @elseif ($booking->payment->uses_demo_qr)
                                <p role="status" class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs font-semibold text-amber-900">Menunggu konfirmasi pembayaran oleh admin. Halaman ini diperbarui otomatis setiap 15 detik.</p>
                            @else
                                <p role="status" class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs font-semibold text-amber-900">Pembayaran menunggu konfirmasi admin / outlet.</p>
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