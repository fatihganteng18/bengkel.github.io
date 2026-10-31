<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - Planet Ban</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-white font-sans text-slate-800 antialiased" x-data="planetBanApp" x-effect="document.body.classList.toggle('overflow-hidden', cartDrawerOpen || bookingModalOpen)" @keydown.escape.window="closeOverlays()">

    <!-- Notifikasi Flash Sukses -->
    @if(session('success'))
    <div class="bg-blue-600 text-white text-center py-3 px-4 font-bold shadow-md sticky top-0 z-50 flex justify-between items-center">
        <span>✨ {{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="text-white font-bold ml-4 text-xl">&times;</button>
    </div>
    @endif

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
                <a href="/home#katalog" class="hover:text-blue-600 transition">Sparepart</a>
                <a href="/home#toko" class="hover:text-blue-600 transition">Lokasi Toko</a>
                <a href="{{ route('booking.status') }}" class="text-blue-900 font-bold hover:underline">Cek Status Pesanan</a>
            </nav>

            <div class="flex items-center space-x-4">
                <!-- Ikon Keranjang Belanja -->
                <button type="button" @click="cartDrawerOpen = true" class="relative rounded-xl p-2 text-gray-700 transition hover:bg-blue-50 hover:text-blue-900" title="Keranjang Belanja" :aria-label="'Keranjang, ' + cartCount + ' produk'">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span x-show="cartCount > 0" x-text="cartCount" class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white"></span>
                </button>

                @auth
                    <a href="{{ route('account.dashboard') }}" class="rounded-xl p-2 text-gray-700 transition hover:bg-blue-50 hover:text-blue-900" title="Akun saya" aria-label="Akun saya">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl p-2 text-gray-700 transition hover:bg-blue-50 hover:text-blue-900" title="Masuk ke akun" aria-label="Masuk ke akun">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Breadcrumb Navigasi -->
    <div class="container mx-auto px-4 md:px-12 py-4 text-xs text-gray-500 flex items-center gap-2">
        <a href="/home" class="hover:underline">Katalog Produk</a>
        <span>/</span>
        <span class="text-gray-800 font-bold uppercase">{{ $product->name }}</span>
    </div>

    <!-- Konten Detail Produk -->
    <main class="container mx-auto px-4 md:px-12 py-6 flex-grow max-w-7xl">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            
            <!-- SISI KIRI: Gambar Produk -->
            <div class="space-y-4">
                <div class="relative flex h-[320px] items-center justify-center overflow-hidden rounded-3xl border border-slate-100 bg-slate-50 p-8 shadow-sm sm:h-[420px]">
                    <span class="absolute top-4 left-4 bg-red-600 text-white text-[10px] font-black px-3 py-1 rounded uppercase tracking-wider shadow">
                        Promo Resmi
                    </span>
                    @if($product->image)
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="h-full w-full object-contain">
                    @else
                        <span class="text-8xl sm:text-9xl" aria-hidden="true">🛵</span>
                    @endif
                </div>
            </div>

            <!-- SISI KANAN: Informasi & Tombol Pemesanan / Pembayaran -->
            <div class="space-y-6">
                <div>
                    <span class="text-xs font-bold text-blue-900 uppercase tracking-widest bg-blue-50 px-3 py-1 rounded-md inline-block mb-3">
                        Kategori: {{ $product->category }}
                    </span>
                    <h1 class="text-2xl md:text-3xl font-black text-gray-900 leading-snug mb-3">
                        {{ $product->name }}
                    </h1>
                    
                    <div class="flex items-center gap-4 text-xs text-gray-500 mb-6">
                        <span>Belum Ada Ulasan</span>
                        <span>•</span>
                        <span>Stok Tersedia: <strong class="text-gray-800">{{ $product->stock }} Pcs</strong></span>
                        <span>•</span>
                        <span>SKU: 09{{ $product->id }}</span>
                    </div>

                    <!-- Harga Produk -->
                    <div class="text-3xl md:text-4xl font-black text-gray-900 mb-6">
                        @if ($product->has_discount)
                            <span class="mb-2 inline-flex rounded-full bg-red-600 px-3 py-1 text-xs font-black uppercase tracking-wider text-white">Hemat {{ $product->discount_percentage }}%</span>
                            <span class="block text-lg font-semibold text-slate-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        @endif
                        <span class="block">Rp {{ number_format($product->effective_price, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="rounded-2xl border border-blue-100 bg-blue-50/60 p-4 text-sm text-blue-950">
                    Harga dan ketersediaan produk akan dikonfirmasi kembali oleh outlet pilihan Anda.
                </div>

                <!-- Tombol Aksi: Pesan / Pembayaran & Tambah Keranjang -->
                <div class="space-y-3 pt-2">
                    <!-- Tombol Pesan / Pembayaran Langsung -->
                    <button type="button" @click="openBookingModal('{{ $product->id }}')" class="block w-full rounded-2xl bg-blue-950 px-6 py-4 text-center text-base font-extrabold text-white shadow-md transition hover:bg-blue-900 hover:shadow-lg">
                        Pilih & Booking
                    </button>

                    <!-- Tombol Tambah ke Keranjang -->
                    <button
                        type="button"
                        @click="addToCartFromButton($el)"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}"
                        data-product-price="{{ $product->effective_price }}"
                        data-product-image="{{ $product->image }}"
                        data-product-stock="{{ $product->stock }}"
                        :disabled="{{ $product->stock < 1 ? 'true' : 'false' }}"
                        class="block w-full rounded-2xl bg-yellow-400 px-6 py-3.5 text-center text-sm font-bold text-blue-950 shadow transition hover:bg-yellow-300 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        {{ $product->stock > 0 ? 'Tambah ke Keranjang' : 'Stok Habis' }}
                    </button>

                    <a href="https://wa.me/628170006055?text={{ urlencode('Halo Planet Ban, saya tertarik dengan produk ' . $product->name) }}" target="_blank" rel="noopener noreferrer" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3.5 px-6 rounded-2xl shadow transition text-center block text-sm">
                        💬 Tanya CS via WhatsApp
                    </a>
                </div>

                <!-- Deskripsi Produk -->
                <div class="border-t border-gray-200 pt-6 space-y-4">
                    <h3 class="font-black text-gray-900 text-base">Deskripsi</h3>
                    <div class="text-gray-600 text-sm leading-relaxed space-y-3">
                        <p>{{ $product->description }}</p>
                        <p class="font-semibold text-gray-800">Keunggulan produk dan servis di Planet Ban:</p>
                        <ul class="list-disc pl-5 space-y-1 text-xs">
                            <li>Komponen berkualitas tinggi dan teruji resmi.</li>
                            <li>Performa kendaraan menjadi lebih optimal dan bertenaga.</li>
                            <li>Pemasangan langsung ditangani oleh teknisi profesional di outlet terdekat.</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Produk Terkait -->
        @if($relatedProducts->count() > 0)
        <div class="mt-24 border-t border-gray-200 pt-12">
            <h3 class="text-xl font-black text-gray-900 mb-6">Produk Pilihan Lainnya</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $rel)
                <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-xs hover:shadow-xl transition flex flex-col justify-between">
                    <div>
                        <div class="h-36 bg-gray-50 rounded-xl mb-4 flex items-center justify-center text-4xl">🛵</div>
                        <span class="text-[10px] text-blue-900 font-bold uppercase tracking-wider block mb-1">{{ $rel->category }}</span>
                        <h4 class="font-extrabold text-gray-900 text-sm mb-2 line-clamp-2">{{ $rel->name }}</h4>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
                        <span class="font-black text-blue-900 text-sm">Rp {{ number_format($rel->effective_price, 0, ',', '.') }}</span>
                        <a href="{{ route('product.show', $rel->id) }}" class="text-xs font-bold bg-blue-50 text-blue-900 px-3 py-2 rounded-xl hover:bg-blue-100">Detail</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </main>

    <x-cart-drawer />

    <!-- Modal Form Pemesanan & Pembayaran -->
    <div x-show="bookingModalOpen" x-transition.opacity @click.self="closeOverlays()" class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" style="display: none;">
        <div x-transition:enter="transform transition ease-out duration-300"
             x-transition:enter-start="translate-y-4 scale-95 opacity-0"
             x-transition:enter-end="translate-y-0 scale-100 opacity-100"
             x-transition:leave="transform transition ease-in duration-200"
             x-transition:leave-start="translate-y-0 scale-100 opacity-100"
             x-transition:leave-end="translate-y-4 scale-95 opacity-0"
             class="relative max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-3xl bg-white p-6 shadow-2xl sm:p-8">
            <button type="button" @click="closeOverlays()" :disabled="isLoading" class="absolute right-5 top-5 text-2xl font-bold text-gray-400 transition hover:text-gray-700 disabled:opacity-50" aria-label="Tutup form booking">&times;</button>
            
            <h3 class="text-2xl font-black text-gray-900 mb-1">Form Booking Produk & Servis</h3>
            <p class="text-xs text-gray-500 mb-6">Isi detail untuk mengatur jadwal pemasangan produk di outlet.</p>

            <form action="{{ route('booking.store') }}" method="POST" @submit.prevent="submitForm($event)" class="space-y-4">
                @csrf
                <input type="hidden" name="product_id" :value="selectedProductId || '{{ $product->id }}'">
                
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" name="customer_name" required placeholder="Nama Anda" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-900 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">No. WhatsApp / HP</label>
                    <input type="tel" name="customer_phone" required placeholder="081234567890" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-900 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Pilih Outlet Cabang</label>
                    <select name="store_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-900 focus:outline-none">
                        <option value="">-- Pilih Cabang Outlet --</option>
                        @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }} ({{ $store->city }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tanggal Kunjungan</label>
                        <input type="date" name="booking_date" required min="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-900 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jam</label>
                        <select name="booking_time" required class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-900 focus:outline-none">
                            <option value="09:00">09:00 WIB</option>
                            <option value="13:00">13:00 WIB</option>
                            <option value="16:00">16:00 WIB</option>
                        </select>
                    </div>
                </div>

                @include('components.payment-method-options')

                <div class="pt-4 flex justify-end gap-3">
                    <button type="button" @click="closeOverlays()" :disabled="isLoading" class="rounded-xl border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-100 disabled:opacity-50">Batal</button>
                    <button type="submit" :disabled="isLoading" class="flex items-center justify-center gap-2 rounded-xl bg-blue-950 px-6 py-2.5 text-sm font-bold text-white shadow transition hover:bg-blue-900 disabled:cursor-wait disabled:opacity-70">
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
</body>
</html>