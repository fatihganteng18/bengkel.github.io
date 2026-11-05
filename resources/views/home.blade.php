<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arisa Knalpot - Knalpot Mobil & Workshop Performance</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Inter & Icons -->
    <script src="https://unpkg.com/@lucide/web"></script>
</head>
<body class="bg-gray-100 text-gray-900 antialiased">

    <!-- Top Announcement Bar -->
    <div class="bg-brand-red text-white text-xs py-2 px-4 text-center font-bold tracking-wide">
        🔥 GRATIS PASANG BAN & NIKMATI DISKON OLI HINGGA 20% DI OUTLET TERDEKAT!
    </div>

    <!-- Main Navigation -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="bg-brand-red text-white font-black italic text-2xl px-3 py-1 rounded-lg transform -skew-x-12">
                    Arisa<span class="text-brand-yellow"> Knalpot</span>
                </div>
            </a>

            <!-- Nav Links -->
            <div class="hidden md:flex items-center space-x-8 font-semibold text-sm">
                <a href="#fitur-cari" class="text-gray-700 hover:text-brand-red transition">Cari Ban Sesuai Motor</a>
                <a href="#produk" class="text-gray-700 hover:text-brand-red transition">Katalog Produk</a>
                <a href="#outlet" class="text-gray-700 hover:text-brand-red transition">Lokasi Outlet</a>
                <a href="#booking" class="text-gray-700 hover:text-brand-red transition">Booking Servis</a>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-4">
                <a href="#" class="relative p-2 text-gray-700 hover:text-brand-red">
                    <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                    <span class="absolute top-1 right-1 bg-brand-red text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold">2</span>
                </a>
                <a href="{{ route('login') }}" class="bg-brand-dark hover:bg-gray-800 text-white text-sm font-bold px-5 py-2.5 rounded-xl transition shadow-md flex items-center gap-2">
                    <i data-lucide="user" class="w-4 h-4"></i> Masuk / Daftar
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section with Search Engine -->
    <section id="fitur-cari" class="relative bg-gradient-to-br from-gray-900 via-brand-dark to-black text-white py-16 lg:py-24 overflow-hidden">
        <!-- Background Decorative Elements -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-red opacity-20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-brand-yellow opacity-10 rounded-full blur-3xl"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Left Headline -->
            <div class="lg:col-span-6 space-y-6">
                <span class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-brand-yellow px-3 py-1 rounded-full text-xs font-semibold backdrop-blur-md">
                    <i data-lucide="shield-check" class="w-4 h-4"></i> 100% Produk Original & Bergaransi
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight">
                    Cari Ban & Sparepart <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-yellow to-yellow-500">Presisi</span> Untuk Motor Anda.
                </h1>
                <p class="text-gray-400 text-base sm:text-lg">
                    Tidak usah bingung dengan ukuran ban! Cukup pilih jenis motor Anda, kami tampilkan ban terbaik yang dijamin pas dan siap dipasang di toko.
                </p>
                
                <!-- Trust Badges -->
                <div class="pt-4 grid grid-cols-3 gap-4 border-t border-gray-800">
                    <div>
                        <div class="text-2xl font-black text-white">1.000+</div>
                        <div class="text-xs text-gray-400">Outlet Tersebar</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-white">100%</div>
                        <div class="text-xs text-gray-400">Ban Tubeless Asli</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-white">Free</div>
                        <div class="text-xs text-gray-400">Jasa Pasang & Nitrogen</div>
                    </div>
                </div>
            </div>

            <!-- Right Interactive Quick Search Box -->
            <div class="lg:col-span-6">
                <div class="bg-white text-gray-900 p-6 sm:p-8 rounded-3xl shadow-2xl border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-extrabold text-gray-900">Pencarian Cepat Ban</h3>
                            <p class="text-xs text-gray-500">Pilih spesifikasi kendaraan Anda</p>
                        </div>
                        <div class="p-3 bg-red-50 text-brand-red rounded-2xl">
                            <i data-lucide="bike" class="w-6 h-6"></i>
                        </div>
                    </div>

                    <form action="{{ route('tires.search') }}" method="POST" class="space-y-4">
                        @csrf
                        <!-- Step 1: Merk -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">1. Merk Motor</label>
                            <select id="brand-select" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm font-semibold focus:ring-2 focus:ring-brand-red focus:outline-none">
                                <option value="">-- Pilih Merk (Honda, Yamaha, dll) --</option>
                                @foreach($motorcycles as $moto)
                                    <option value="{{ $moto->brand }}">{{ $moto->brand }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Step 2: Model -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">2. Tipe / Model Motor</label>
                            <select name="motorcycle_id" id="model-select" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm font-semibold focus:ring-2 focus:ring-brand-red focus:outline-none">
                                <option value="">-- Pilih Merk Terlebih Dahulu --</option>
                            </select>
                        </div>

                        <!-- Step 3: Posisi -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-2">3. Posisi Ban</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer hover:bg-gray-50 transition border-gray-200 has-[:checked]:border-brand-red has-[:checked]:bg-red-50">
                                    <input type="radio" name="position" value="front" checked class="accent-brand-red">
                                    <span class="text-xs font-bold">Ban Depan</span>
                                </label>
                                <label class="flex items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer hover:bg-gray-50 transition border-gray-200 has-[:checked]:border-brand-red has-[:checked]:bg-red-50">
                                    <input type="radio" name="position" value="rear" class="accent-brand-red">
                                    <span class="text-xs font-bold">Ban Belakang</span>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-brand-red hover:bg-red-700 text-white font-extrabold py-4 rounded-xl shadow-lg shadow-red-500/30 transition transform active:scale-[0.98] flex items-center justify-center gap-2 text-base">
                            <i data-lucide="search" class="w-5 h-5"></i> Temukan Ban Yang Cocok
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </section>

    <!-- Catalog Section -->
    <section id="produk" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <span class="text-brand-red text-xs font-extrabold tracking-widest uppercase">Katalog Terlaris</span>
                <h2 class="text-3xl font-black text-gray-900 mt-1">Ban & Sparepart Rekomendasi</h2>
            </div>
            <a href="{{ route('home') }}#produk" class="text-sm font-bold text-brand-red hover:underline flex items-center gap-1 mt-4 md:mt-0">
                Lihat Semua Produk <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($featuredProducts as $prod)
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <!-- Product Image Container -->
                    <div class="relative bg-gray-50 rounded-xl overflow-hidden mb-4 p-4 flex items-center justify-center h-48">
                        <span class="absolute top-3 left-3 bg-brand-yellow text-gray-900 text-[10px] font-extrabold px-2.5 py-1 rounded-md uppercase tracking-wider">
                            {{ $prod->category }}
                        </span>
                        <img src="{{ $prod->image ?? 'https://images.unsplash.com/photo-1578844251758-2f71da64c96f?w=400&q=80' }}" alt="{{ $prod->name }}" class="max-h-full object-contain group-hover:scale-105 transition duration-300">
                    </div>
                    
                    <h3 class="font-bold text-gray-900 text-base line-clamp-2 group-hover:text-brand-red transition">{{ $prod->name }}</h3>
                    <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $prod->description }}</p>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-gray-400 block font-semibold">Harga Mulai</span>
                        <span class="text-lg font-black text-brand-red">Rp {{ number_format($prod->price, 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('product.show', $prod->id) }}" class="p-3 bg-brand-dark hover:bg-brand-red text-white rounded-xl transition shadow-md">
                        <i data-lucide="calendar" class="w-5 h-5"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Interactive Script for Lucide Icons & Dynamic Dropdown -->
    <script>
        // Init Lucide Icons
        lucide.createIcons();

        // Dynamic Motor Model Fetcher
        document.getElementById('brand-select').addEventListener('change', function() {
            let brand = this.value;
            let modelSelect = document.getElementById('model-select');
            modelSelect.innerHTML = '<option value="">Memuat data...</option>';

            if(brand) {
                fetch(`/get-models?brand=${brand}`)
                    .then(response => response.json())
                    .then(data => {
                        modelSelect.innerHTML = '<option value="">-- Pilih Tipe Motor --</option>';
                        data.forEach(item => {
                            modelSelect.innerHTML += `<option value="${item.id}">${item.model_name} (Depan: ${item.front_tire_size} | Belakang: ${item.rear_tire_size})</option>`;
                        });
                    });
            } else {
                modelSelect.innerHTML = '<option value="">-- Pilih Merk Terlebih Dahulu --</option>';
            }
        });
    </script>
</body>
</html>