<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Booking | Planet Ban</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-800 flex flex-col min-h-screen font-sans">

    @if(session('success'))
    <div class="bg-green-600 text-white text-center py-3 px-4 font-bold shadow-md">
        {{ session('success') }}
    </div>
    @endif

    <header class="bg-blue-900 text-white shadow py-4 px-6 md:px-8 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <span class="bg-yellow-400 text-blue-950 font-black px-2.5 py-1 rounded text-sm">ADMIN</span>
            <h1 class="font-bold text-lg">Kelola Data Booking Planet Ban</h1>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="text-xs font-bold bg-white/10 hover:bg-white/20 px-4 py-2 rounded-xl transition">
                Dashboard
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs font-bold bg-white/10 hover:bg-white/20 px-4 py-2 rounded-xl transition">
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <main class="container mx-auto px-4 md:px-8 py-8 flex-grow">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h3 class="font-extrabold text-xl text-gray-900">Daftar Masuk Pesanan Servis & Produk</h3>
                    <p class="text-xs text-gray-500">Kelola dan update status konfirmasi pelanggan di sini.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b border-gray-200">
                            <th class="py-4 px-6">ID & Pelanggan</th>
                            <th class="py-4 px-6">Outlet Cabang</th>
                            <th class="py-4 px-6">Produk / Layanan</th>
                            <th class="py-4 px-6">Jadwal</th>
                            <th class="py-4 px-6">Status</th>
                            <th class="py-4 px-6">Pembayaran</th>
                            <th class="py-4 px-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($bookings as $b)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-4 px-6">
                                <span class="text-xs text-gray-400 block">#{{ $b->id }}</span>
                                <span class="font-bold text-gray-900 block">{{ $b->customer_name }}</span>
                                <span class="text-xs text-gray-500">📞 {{ $b->customer_phone }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-semibold text-gray-800">{{ $b->store->name ?? '-' }}</span>
                                <span class="text-xs text-gray-500 block">{{ $b->store->city ?? '' }}</span>
                            </td>
                            <td class="py-4 px-6">
                                @forelse($b->items as $item)
                                    <span class="block font-medium text-gray-800">{{ $item->product_name }} <span class="text-xs text-gray-500">× {{ $item->quantity }}</span></span>
                                @empty
                                    <span class="font-medium text-gray-800">{{ $b->product->name ?? 'Servis Umum / Tanpa Produk' }}</span>
                                @endforelse
                            </td>
                            <td class="py-4 px-6 text-xs">
                                <span class="font-bold block text-gray-900">📅 {{ $b->booking_date }}</span>
                                <span class="text-gray-500">⏰ Jam {{ $b->booking_time }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-xs font-black px-3 py-1 rounded-full uppercase
                                    {{ $b->status === 'completed' ? 'bg-green-100 text-green-800' : ($b->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $b->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs">
                                @if ($b->payment)
                                    <span class="block font-bold text-gray-800">{{ $b->payment->method_label }}</span>
                                    <span class="mt-1 inline-block rounded-full px-2.5 py-1 font-bold uppercase
                                        {{ $b->payment->status === 'paid' ? 'bg-green-100 text-green-800' : ($b->payment->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $b->payment->status }}
                                    </span>
                                    @if ($b->payment->amount !== null)
                                        <span class="mt-1 block text-gray-500">Total produk: Rp {{ number_format((float) $b->payment->amount, 0, ',', '.') }}</span>
                                    @else
                                        <span class="mt-1 block text-gray-500">Nilai servis dikonfirmasi outlet</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">Belum ada data pembayaran</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <form action="{{ route('admin.bookings.update', $b->id) }}" method="POST" class="flex justify-center gap-2">
                                    @csrf
                                    <div class="flex min-w-36 flex-col gap-2">
                                        <label class="sr-only" for="booking-status-{{ $b->id }}">Status booking</label>
                                        <select id="booking-status-{{ $b->id }}" name="status" class="text-xs border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-blue-900">
                                            <option value="pending" {{ $b->status == 'pending' ? 'selected' : '' }}>Booking: Pending</option>
                                            <option value="confirmed" {{ $b->status == 'confirmed' ? 'selected' : '' }}>Booking: Confirmed</option>
                                            <option value="completed" {{ $b->status == 'completed' ? 'selected' : '' }}>Booking: Completed</option>
                                        </select>
                                        @if ($b->payment)
                                            <label class="sr-only" for="payment-status-{{ $b->id }}">Status pembayaran</label>
                                            <select id="payment-status-{{ $b->id }}" name="payment_status" class="text-xs border border-gray-300 rounded-lg px-2 py-1 focus:ring-2 focus:ring-blue-900">
                                                <option value="pending" {{ $b->payment->status === 'pending' ? 'selected' : '' }}>Pembayaran: Pending</option>
                                                <option value="paid" {{ $b->payment->status === 'paid' ? 'selected' : '' }}>Pembayaran: Lunas</option>
                                                <option value="failed" {{ $b->payment->status === 'failed' ? 'selected' : '' }}>Pembayaran: Gagal</option>
                                            </select>
                                        @endif
                                        <button type="submit" class="bg-blue-900 hover:bg-blue-800 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow">
                                            Simpan status
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-400 text-sm">Belum ada data booking masuk.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 text-center py-6 text-xs text-gray-500 mt-12">
        &copy; 2026 Planet Ban Clone - Modul Admin Dashboard.
    </footer>
</body>
</html>