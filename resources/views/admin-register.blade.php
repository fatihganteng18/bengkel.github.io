<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokasi Toko Planet Ban Seluruh Indonesia</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen font-sans" x-data="{ searchCity: '' }">

    <!-- Navbar Utama -->
    <header class="bg-white shadow-xs sticky top-0 z-40 border-b border-gray-100">
        <div class="container mx-auto px-4 md:px-12 py-3 flex justify-between items-center">
            <a href="/home" class="flex items-center gap-2">
                <div class="bg-blue-900 text-white font-black italic px-3 py-1 rounded-lg tracking-wider text-lg shadow">
                    PLANET <span class="text-yellow-400">BAN</span>
                </div>
            </a>
            
            <nav class="hidden lg:flex items-center space-x-8 font-semibold text-sm text-gray-700">
                <a href="/home" class="hover:text-blue-600 transition">Servis</a>
                <a href="/home#katalog" class="hover:text-blue-600 transition">Ban Motor</a>
                <a href="/home#katalog" class="hover:text-blue-600 transition">Oli Motor</a>
                <a href="{{ route('stores.index') }}" class="text-blue-900 font-bold">Lokasi Toko</a>
                <a href="{{ route('booking.status') }}" class="hover:text-blue-600 transition">Cek Status Pesanan</a>
            </nav>

            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.login') }}" class="text-gray-700 hover:text-blue-900 transition p-2" title="Akun">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </a>
            </div>
        </div>
    </header>

    <section class="bg-gradient-to-r from-blue-900 to-blue-950 text-white py-12 text-center shadow-inner">
        <div class="container mx-auto px-4">
            <h1 class="text-3xl md:text-4xl font-black tracking-tight">Lokasi Toko</h1>
            <p class="text-xs md:text-sm text-blue-200 mt-2">Temukan jaringan outlet resmi Planet Ban terdekat di kota Anda</p>
        </div>
    </section>

    <main class="container mx-auto px-4 md:px-12 py-8 flex-grow max-w-7xl space-y-8">
        
        <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-xs grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Cari Kota / Cabang</label>
                <input type="text" x-model="searchCity" placeholder="Ketik nama kota atau cabang..." class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-900 focus:outline-none">
            </div>
            <div class="flex items-end pt-5 md:pt-0">
                <button class="w-full bg-blue-50 text-blue-900 hover:bg-blue-100 font-bold py-2.5 px-4 rounded-xl text-sm transition flex items-center justify-center gap-2">
                    <span>📍</span> Gunakan Lokasi Saat Ini
                </button>
            </div>
        </div>

        <!-- Daftar Kartu Cabang Toko -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($stores as $store)
            <div x-show="searchCity === '' || '{{ strtolower($store->name . ' ' . $store->city . ' ' . $store->address) }}'.includes(searchCity.toLowerCase())"
                 class="bg-white p-6 rounded-2xl border border-gray-200 shadow-xs hover:shadow-lg transition flex flex-col justify-between">
                <div>
                    <h3 class="font-extrabold text-gray-900 text-base mb-2">🏢 {{ $store->name }}</h3>
                    <p class="text-xs text-gray-600 mb-3 leading-relaxed">📍 {{ $store->address }}, {{ $store->city }}</p>
                </div>
                <div class="space-y-3 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-600 font-semibold">📞 Telp/WA: <span class="text-blue-900 font-bold">{{ $store->phone }}</span></p>
                    <a href="/home" class="w-full bg-blue-900 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded-xl text-xs text-center block transition shadow">
                        Pilih & Booking di Sini &rarr;
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12 text-gray-400">
                Belum ada data toko tersedia.
            </div>
            @endforelse
        </div>
    </main>

    <footer class="bg-gray-900 text-white text-center py-8 mt-16 text-xs text-gray-400">
        &copy; 2026 Planet Ban. Hak Cipta Dilindungi.
    </footer>
</body>
</html>