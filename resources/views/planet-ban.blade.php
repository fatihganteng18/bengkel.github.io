<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planet Ban | Ganti Ban, Oli, dan Servis Motor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased" x-data="planetBanApp" x-effect="document.body.classList.toggle('overflow-hidden', cartDrawerOpen || bookingModalOpen)" @keydown.escape.window="closeOverlays()">

    <!-- Notifikasi Flash Sukses -->
    @if(session('success'))
    <div class="bg-blue-600 text-white text-center py-3 px-4 font-bold shadow-md sticky top-0 z-50 flex justify-between items-center">
        <span>✨ {{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="text-white font-bold ml-4 text-xl">&times;</button>
    </div>
    @endif

<!-- Navbar Utama ala Planet Ban -->
<header class="sticky top-0 z-40 border-b border-slate-100 bg-white/95 shadow-sm backdrop-blur" x-data="{ dropdownOpen: null, accountDropdown: false }">
    <div class="container mx-auto px-4 md:px-8 py-3 flex justify-between items-center">
        
        <!-- Logo Planet Ban -->
        <a href="/home" class="flex items-center gap-2">
            <div class="bg-blue-900 text-white font-black italic px-3 py-1 rounded-lg tracking-wider text-lg shadow">
                PLANET <span class="text-yellow-400">BAN</span>
            </div>
        </a>
        
        <!-- Navigasi Menu dengan Dropdown -->
        <nav class="hidden lg:flex items-center space-x-6 font-semibold text-sm text-gray-700">
            <a href="#beranda" class="hover:text-blue-600 transition">Servis</a>
            
            <!-- Dropdown Ban Motor -->
            <div class="relative" @mouseleave="dropdownOpen = null">
                <button @mouseover="dropdownOpen = 'ban'" @click="dropdownOpen = dropdownOpen === 'ban' ? null : 'ban'" class="flex items-center gap-1 hover:text-blue-600 transition py-2">
                    <span>Ban Motor</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="dropdownOpen === 'ban'" x-transition class="absolute top-full left-0 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                    <button @click="selectedCategory = 'tire'; dropdownOpen = null" class="w-full text-left px-4 py-2 hover:bg-blue-50 text-xs font-bold text-gray-700 hover:text-blue-900">Ban Motor Matic</button>
                    <button @click="selectedCategory = 'tire'; dropdownOpen = null" class="w-full text-left px-4 py-2 hover:bg-blue-50 text-xs font-bold text-gray-700 hover:text-blue-900">Ban Motor Bebek</button>
                    <button @click="selectedCategory = 'tire'; dropdownOpen = null" class="w-full text-left px-4 py-2 hover:bg-blue-50 text-xs font-bold text-gray-700 hover:text-blue-900">Ban Motor Sport</button>
                    <button @click="selectedCategory = 'tire'; dropdownOpen = null" class="w-full text-left px-4 py-2 hover:bg-blue-50 text-xs font-bold text-gray-700 hover:text-blue-900">Ban Motor Big Matic</button>
                </div>
            </div>

            <!-- Dropdown Oli Motor -->
            <div class="relative" @mouseleave="dropdownOpen = null">
                <button @mouseover="dropdownOpen = 'oli'" @click="dropdownOpen = dropdownOpen === 'oli' ? null : 'oli'" class="flex items-center gap-1 hover:text-blue-600 transition py-2">
                    <span>Oli Motor</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="dropdownOpen === 'oli'" x-transition class="absolute top-full left-0 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                    <button @click="selectedCategory = 'oil'; dropdownOpen = null" class="w-full text-left px-4 py-2 hover:bg-blue-50 text-xs font-bold text-gray-700 hover:text-blue-900">Oli Matic</button>
                    <button @click="selectedCategory = 'oil'; dropdownOpen = null" class="w-full text-left px-4 py-2 hover:bg-blue-50 text-xs font-bold text-gray-700 hover:text-blue-900">Oli Bebek & Sport</button>
                </div>
            </div>

            <!-- Dropdown Sparepart -->
            <div class="relative" @mouseleave="dropdownOpen = null">
                <button @mouseover="dropdownOpen = 'sparepart'" @click="dropdownOpen = dropdownOpen === 'sparepart' ? null : 'sparepart'" class="flex items-center gap-1 hover:text-blue-600 transition py-2">
                    <span>Sparepart</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="dropdownOpen === 'sparepart'" x-transition class="absolute top-full left-0 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50">
                    <button @click="selectedCategory = 'sparepart'; dropdownOpen = null" class="w-full text-left px-4 py-2 hover:bg-blue-50 text-xs font-bold text-gray-700 hover:text-blue-900">Aki Motor</button>
                    <button @click="selectedCategory = 'sparepart'; dropdownOpen = null" class="w-full text-left px-4 py-2 hover:bg-blue-50 text-xs font-bold text-gray-700 hover:text-blue-900">Kampas Rem / V-Belt</button>
                </div>
            </div>

            <a href="{{ route('stores.index') }}" class="hover:text-blue-600 transition">Lokasi Toko</a>
            <a href="#katalog" class="hover:text-blue-600 transition">Promo</a>
            <a href="{{ route('booking.status') }}" class="hover:text-blue-600 transition">Buku Servis</a>
        </nav>

        <!-- Menu Kanan (Ikon Search, Keranjang, dan Profil Akun) -->
        <div class="flex items-center space-x-3">
            <!-- Tombol Ikon Pencarian -->
            <button @click="focusProductSearch()" class="rounded-xl p-2 text-gray-700 transition hover:bg-blue-50 hover:text-blue-900" title="Cari Produk" aria-label="Cari produk">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </button>

            <!-- Ikon Keranjang Belanja -->
            <button type="button" @click="cartDrawerOpen = true" class="relative rounded-xl p-2 text-gray-700 transition hover:bg-blue-50 hover:text-blue-900" title="Keranjang" :aria-label="'Keranjang, ' + cartCount + ' produk'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span x-show="cartCount > 0" x-text="cartCount" class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white"></span>
            </button>

            <!-- Dropdown Akun / Profil Pengguna (Ikon Orang) -->
            <div class="relative">
                <button @click="accountDropdown = !accountDropdown" class="text-gray-700 hover:text-blue-950 p-2 focus:outline-none flex items-center" title="Akun Saya">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </button>

                <!-- Pop-up Menu Akun (Login & Daftar Akun) -->
                <div x-show="accountDropdown" 
                     @click.away="accountDropdown = false"
                     x-transition
                     class="absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-2xl border border-gray-100 p-5 z-50"
                     style="display: none;">
                    
                     @auth
                         <h4 class="mb-1 font-black text-gray-900 text-base">{{ Auth::user()->name }}</h4>
                         <p class="mb-4 truncate text-xs text-gray-500">{{ Auth::user()->email }}</p>
                         <div class="space-y-3">
                             <a href="{{ route('account.dashboard') }}" class="block w-full rounded-xl bg-blue-900 py-3 text-center text-sm font-bold text-white shadow transition hover:bg-blue-800">
                                 Akun & Riwayat Booking
                             </a>
                             <form action="{{ route('logout') }}" method="POST">
                                 @csrf
                                 <button type="submit" class="block w-full rounded-xl border border-gray-200 bg-white py-3 text-sm font-bold text-gray-700 transition hover:bg-gray-50">
                                     Keluar
                                 </button>
                             </form>
                         </div>
                     @else
                         <h4 class="mb-4 font-black text-gray-900 text-base">Akun</h4>
                         <div class="space-y-3">
                             <a href="{{ route('login') }}" class="block w-full rounded-xl bg-blue-900 py-3 text-center text-sm font-bold text-white shadow transition hover:bg-blue-800">
                                 Masuk
                             </a>
                             <a href="{{ route('register') }}" class="block w-full rounded-xl border border-blue-900 bg-white py-3 text-center text-sm font-bold text-blue-900 transition hover:bg-gray-50">
                                 Daftar Akun
                             </a>
                         </div>
                     @endauth
                </div>
            </div>

            <!-- Tombol Menu Mobile (Garis Tiga) -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" :aria-expanded="mobileMenuOpen" aria-label="Buka menu navigasi" class="rounded-xl p-1 text-gray-700 transition hover:bg-slate-100 lg:hidden">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>
    <nav x-show="mobileMenuOpen" x-transition class="border-t border-slate-100 bg-white px-4 py-4 shadow-lg lg:hidden">
        <div class="mx-auto flex max-w-7xl flex-col gap-1 text-sm font-semibold text-slate-700">
            <a @click="mobileMenuOpen = false" href="#beranda" class="rounded-xl px-4 py-3 hover:bg-blue-50 hover:text-blue-900">Servis</a>
            <a @click="mobileMenuOpen = false" href="#katalog" class="rounded-xl px-4 py-3 hover:bg-blue-50 hover:text-blue-900">Katalog Produk</a>
            <a @click="mobileMenuOpen = false" href="#toko" class="rounded-xl px-4 py-3 hover:bg-blue-50 hover:text-blue-900">Lokasi Toko</a>
            <a @click="openBookingModal()" href="#beranda" class="rounded-xl px-4 py-3 hover:bg-blue-50 hover:text-blue-900">Booking Servis</a>
            <a @click="mobileMenuOpen = false" href="{{ route('booking.status') }}" class="rounded-xl px-4 py-3 hover:bg-blue-50 hover:text-blue-900">Cek Status Booking</a>
        </div>
    </nav>
</header>

    <!-- Hero Section -->
    <section id="beranda" class="relative bg-gradient-to-r from-blue-900 to-blue-950 text-white py-16 md:py-24 overflow-hidden">
        <div class="container mx-auto px-4 md:px-8 relative z-10 text-center max-w-4xl">
            <span class="bg-yellow-400 text-blue-950 text-xs font-black px-3 py-1 rounded-full uppercase tracking-widest inline-block mb-4 shadow">
                #RasaMesinBaru
            </span>
            <h2 class="text-3xl md:text-5xl font-black mb-6 leading-tight tracking-tight">
                Servis Motor #RasaMesinBaru Hingga ke Bagian Vital
            </h2>
            <p class="text-base md:text-lg text-blue-100 mb-8 max-w-2xl mx-auto">
                Mesin bersih total & hemat BBM, tarikan jadi lebih enteng, serta emisi turun lebih dari 70%.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <button @click="openBookingModal(null, null)" class="bg-yellow-400 hover:bg-yellow-300 text-blue-950 font-extrabold px-8 py-3.5 rounded-full shadow-lg transition transform hover:scale-105">
                    Booking Servis Sekarang
                </button>
                <a href="#katalog" class="bg-white/10 hover:bg-white/20 text-white border border-white/30 font-bold px-8 py-3.5 rounded-full backdrop-blur transition">
                    Lihat Katalog Produk
                </a>
            </div>
        </div>
    </section>

    <!-- Katalog & Produk Section -->
    <main class="container mx-auto flex-grow px-4 py-12 md:px-8" id="katalog">
        
        <!-- Judul Halaman ala Planet Ban -->
        <h2 class="text-2xl md:text-3xl font-black text-gray-900 mb-6">Harga Ban & Komponen Motor</h2>

        <!-- Slider Brand Logo Khas Planet Ban -->
        <div class="bg-white p-4 rounded-2xl shadow-xs border border-gray-200 mb-8 flex items-center justify-between gap-4 overflow-x-auto">
            <template x-for="brand in ['ASPIRA', 'PLANETO', 'MICHELIN', 'IRC', 'PIRELLI', 'PRESA', 'SWALLOW', 'DUNLOP', 'KENDA', 'FDR']">
                <button @click="searchProduct = brand" 
                        class="px-4 py-3 border border-gray-200 rounded-xl font-bold text-xs hover:border-blue-900 hover:text-blue-900 transition whitespace-nowrap bg-gray-50/50">
                    <span x-text="brand"></span>
                </button>
            </template>
        </div>

        <!-- Layout Utama: Sidebar Kiri (Filter) & Kanan (Grid Produk) -->
        <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-5">
            
            <!-- SIDEBAR FILTER KIRI -->
            <div class="bg-white p-6 rounded-3xl border border-gray-200 shadow-xs space-y-6">
                <div class="flex justify-between items-center border-b border-gray-100 pb-4">
                    <h3 class="font-black text-gray-900 text-lg">Filter Produk</h3>
                    <button @click="selectedCategory = 'all'; searchProduct = ''" class="text-xs font-bold text-blue-900 hover:underline">Atur Ulang</button>
                </div>

                <!-- Kategori Filter -->
                <div>
                    <h4 class="text-xs font-extrabold text-gray-400 uppercase tracking-wider mb-3">Kategori</h4>
                    <div class="space-y-2 text-sm font-semibold text-gray-700">
                        <button @click="selectedCategory = 'all'" class="block w-full text-left py-1 hover:text-blue-900 transition" :class="selectedCategory === 'all' ? 'text-blue-900 font-bold' : ''">📂 Semua Kategori</button>
                        <button @click="selectedCategory = 'tire'" class="block w-full text-left py-1 hover:text-blue-900 transition" :class="selectedCategory === 'tire' ? 'text-blue-900 font-bold' : ''">🛞 Ban Motor</button>
                        <button @click="selectedCategory = 'oil'" class="block w-full text-left py-1 hover:text-blue-900 transition" :class="selectedCategory === 'oil' ? 'text-blue-900 font-bold' : ''">🛢️ Oli Mesin</button>
                        <button @click="selectedCategory = 'sparepart'" class="block w-full text-left py-1 hover:text-blue-900 transition" :class="selectedCategory === 'sparepart' ? 'text-blue-900 font-bold' : ''">⚙️ Sparepart / Aki</button>
                    </div>
                </div>

                <!-- Cari Cepat Nama -->
                <div>
                    <h4 class="text-xs font-extrabold text-gray-400 uppercase tracking-wider mb-2">Cari Berdasarkan Nama</h4>
                    <input type="text" x-model="searchProduct" placeholder="Ketik nama ban/merek..." 
                           class="w-full px-3.5 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-900">
                </div>
            </div>

            <!-- KANAN: KATALOG PRODUK -->
            <div class="space-y-6 lg:col-span-4">
                
                <!-- Bar Pencarian & Info Jumlah Data -->
                <div class="bg-white p-4 rounded-2xl border border-gray-200 flex flex-col sm:flex-row justify-between items-center gap-4 shadow-xs">
                    <span class="text-xs font-bold text-gray-500">Menampilkan produk berkualitas terbaik</span>
                    <input type="text" x-model="searchProduct" data-product-search placeholder="Cari cepat produk..." 
                           class="w-full rounded-xl border border-gray-300 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-900 sm:w-64">
                </div>

                <h3 class="text-xl font-black text-gray-900">Katalog Produk & Servis</h3>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        
        @foreach($products as $product)
        <!-- Kartu Produk -->
        <div x-show="(selectedCategory === 'all' || selectedCategory === '{{ $product->category }}') && ($el.dataset.searchText || '').includes(searchProduct.toLowerCase())"
             data-search-text="{{ strtolower($product->name . ' ' . ($product->description ?? '')) }}"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             class="group flex flex-col justify-between rounded-3xl border border-slate-100 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl sm:p-6">
            
            <div>
                <!-- Badge Diskon -->
                @if ($product->has_discount)
                    <div class="mb-3">
                        <span class="inline-flex items-center gap-1 rounded-full bg-red-600 px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-white shadow-sm">
                            Hemat {{ $product->discount_percentage }}%
                        </span>
                    </div>
                @else
                    <div class="mb-3 h-6" aria-hidden="true"></div>
                @endif

                <!-- Gambar / Ikon Produk -->
                <div class="mb-4 flex h-44 items-center justify-center overflow-hidden rounded-2xl bg-slate-50 p-4 text-5xl">
                    @if($product->image)
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="h-full w-full object-contain transition duration-300 group-hover:scale-105">
                    @else
                        <span aria-hidden="true">🛵</span>
                    @endif
                </div>

                <!-- Kategori -->
                <span class="text-[10px] font-extrabold text-blue-900 uppercase tracking-widest bg-blue-50 px-2.5 py-1 rounded-md inline-block mb-2">
                    {{ $product->category }}
                </span>

                <!-- Nama Produk -->
                <h4 class="mb-1 line-clamp-2 min-h-10 text-sm font-black text-gray-900 transition group-hover:text-blue-900">
                    {{ $product->name }}
                </h4>

                <!-- Deskripsi -->
                <p class="text-xs text-gray-500 mb-4 line-clamp-2">{{ $product->description ?? 'Komponen dan suku cadang berkualitas tinggi.' }}</p>
            </div>

            <!-- Bagian Harga & Tombol Aksi -->
            <div class="mt-5 border-t border-slate-100 pt-4">
                <div class="mb-3 flex items-center justify-between gap-2">
                    <span class="flex flex-col">
                        @if ($product->has_discount)
                            <span class="text-xs text-slate-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        @endif
                        <span class="text-base font-black text-blue-900">Rp {{ number_format($product->effective_price, 0, ',', '.') }}</span>
                    </span>
                    <span class="text-xs font-medium text-slate-500">{{ $product->stock > 0 ? 'Stok ' . $product->stock : 'Stok habis' }}</span>
                </div>

                <button
                    type="button"
                    @click="openBookingModal($el.dataset.productId)"
                    data-product-id="{{ $product->id }}"
                    class="w-full rounded-xl bg-yellow-400 py-3 text-sm font-extrabold text-blue-950 shadow-sm transition hover:bg-yellow-300 hover:shadow-md"
                >
                    Pilih & Booking
                </button>
                <button
                    type="button"
                    @click="addToCartFromButton($el)"
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->name }}"
                    data-product-price="{{ $product->effective_price }}"
                    data-product-image="{{ $product->image }}"
                    data-product-stock="{{ $product->stock }}"
                    :disabled="{{ $product->stock < 1 ? 'true' : 'false' }}"
                    class="mt-2 w-full rounded-xl border border-blue-950/15 py-3 text-sm font-bold text-blue-950 transition hover:border-blue-950 hover:bg-blue-50 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <span>{{ $product->stock > 0 ? 'Tambah ke Keranjang' : 'Stok Habis' }}</span>
                </button>
            </div>
        </div>
        <!-- End Kartu Produk -->
        @endforeach

    </div>
            </div>
        </div>

        <!-- Daftar Toko Cabang -->
        <div class="mt-20 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-200 pb-4" id="toko">
            <div>
                <h3 class="text-2xl font-black text-gray-900">Cabang Planet Ban Terdekat</h3>
                <p class="text-sm text-gray-500">Temukan outlet resmi terdekat dari lokasi Anda</p>
            </div>
            <input type="text" x-model="searchStore" placeholder="🔍 Cari kota / nama toko..." 
                   class="px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-900 w-full md:w-72">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($stores as $store)
            <div x-show="searchStore === '' || $el.dataset.storeSearch.includes(searchStore.toLowerCase())"
                 data-store-search="{{ strtolower($store->name . ' ' . $store->city) }}"
                 x-transition
                 class="bg-white p-6 rounded-2xl shadow-xs border border-gray-200 flex flex-col justify-between">
                <div>
                    <h4 class="text-lg font-bold text-gray-900 mb-2">🏢 {{ $store->name }}</h4>
                    <p class="text-gray-600 text-sm mb-1">📍 {{ $store->address }}, {{ $store->city }}</p>
                    <p class="text-gray-600 text-sm">📞 Telp: <span class="font-bold">{{ $store->phone }}</span></p>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 flex justify-end">
                    <button @click="openBookingModalWithStore('{{ $store->id }}')" 
                            class="text-xs font-bold text-blue-900 hover:underline bg-blue-50 px-3.5 py-2 rounded-lg">
                        Pilih Lokasi Ini &rarr;
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-6 text-gray-400">
                Belum ada data toko.
            </div>
            @endforelse
        </div>
    </main>

    <x-cart-drawer />

    <!-- Modal Form Booking -->
    <div x-show="bookingModalOpen" 
         x-transition.opacity
         @click.self="closeOverlays()"
         class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
         style="display: none;">
        <div x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-y-4 scale-95 opacity-0"
             x-transition:enter-end="translate-y-0 scale-100 opacity-100"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-y-0 scale-100 opacity-100"
             x-transition:leave-end="translate-y-4 scale-95 opacity-0"
             class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-3xl bg-white p-6 shadow-2xl sm:p-8">
            <button type="button" @click="closeOverlays()" :disabled="isLoading" class="absolute right-5 top-5 text-2xl font-bold text-gray-400 transition hover:text-gray-700 disabled:opacity-50" aria-label="Tutup form booking">&times;</button>
            
            <h3 class="text-2xl font-black text-gray-900 mb-1">Form Booking Servis & Produk</h3>
            <p class="text-xs text-gray-500 mb-6">Isi detail data Anda untuk konfirmasi jadwal kunjungan ke outlet.</p>

            <form action="{{ route('booking.store') }}" method="POST" @submit.prevent="submitForm($event)" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" name="customer_name" required placeholder="Contoh: Rian Pratama" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-900 focus:outline-none smooth-transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. WhatsApp / HP</label>
                    <input type="tel" name="customer_phone" required placeholder="081234567890" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-900 focus:outline-none smooth-transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih Outlet Cabang</label>
                    <select name="store_id" x-model="selectedStoreId" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-900 focus:outline-none smooth-transition">
                        <option value="">-- Pilih Lokasi Outlet --</option>
                        @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }} ({{ $store->city }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih Produk (Opsional)</label>
                    <select name="product_id" x-model="selectedProductId" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-900 focus:outline-none smooth-transition">
                        <option value="">-- Hanya Servis / Tanpa Produk --</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} - Rp {{ number_format($product->effective_price, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal Kunjungan</label>
                        <input type="date" name="booking_date" required min="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-900 focus:outline-none smooth-transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jam</label>
                        <select name="booking_time" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-900 focus:outline-none smooth-transition">
                            <option value="09:00">09:00 WIB</option>
                            <option value="11:00">11:00 WIB</option>
                            <option value="13:00">13:00 WIB</option>
                            <option value="15:00">15:00 WIB</option>
                            <option value="17:00">17:00 WIB</option>
                        </select>
                    </div>
                </div>

                @include('components.payment-method-options')

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" @click="closeOverlays()" :disabled="isLoading" class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-100 disabled:opacity-50">Batal</button>
                    
                    <button type="submit" :disabled="isLoading" class="flex items-center justify-center gap-2 rounded-xl bg-blue-950 px-6 py-2.5 text-sm font-bold text-white shadow-lg transition hover:bg-blue-900 disabled:cursor-wait disabled:opacity-70">
                        <svg x-show="isLoading" class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="isLoading ? 'Mengirim...' : 'Konfirmasi Booking'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    @include('components.site-footer')

    <div class="fixed bottom-6 right-6 z-50">
        <a href="https://wa.me/628170006055" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 rounded-full border border-gray-200 bg-white px-4 py-3 text-gray-900 shadow-2xl transition hover:scale-105 hover:bg-gray-100">
            <span class="text-2xl" aria-hidden="true">🟢</span>
            <span class="text-left">
                <span class="block text-[10px] font-bold text-gray-500">Layanan Pelanggan</span>
                <span class="text-xs font-black">0817-000-6055</span>
            </span>
        </a>
    </div>

</body>
</html>