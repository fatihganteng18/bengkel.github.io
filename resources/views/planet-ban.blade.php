<!DOCTYPE html>
<html lang="id" class="scroll-smooth theme-light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f8fafc">
    <title>Planet Ban | Bengkel Motor, Ban & Servis Premium</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased transition-colors duration-300 dark:bg-slate-900 dark:text-slate-100"
    x-data="planetBanApp"
    x-init="@if($errors->any()) bookingModalOpen = true @endif"
    x-effect="document.body.classList.toggle('overflow-hidden', cartDrawerOpen || bookingModalOpen)"
    @keydown.escape.window="closeOverlays()"
>
    @if(session('success'))
        <div class="relative z-[80] flex items-center justify-center gap-3 bg-emerald-400 px-4 py-3 text-center text-sm font-bold text-slate-950">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-xl leading-none" aria-label="Tutup notifikasi">&times;</button>
        </div>
    @endif

    <header class="site-nav sticky top-0 z-40">
        <div class="mx-auto flex h-[76px] max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="brand-mark flex shrink-0 items-center gap-2" aria-label="Planet Ban, beranda">
                <span class="brand-symbol">P</span>
                <span class="text-lg font-black italic tracking-tight text-white">PLANET<span class="text-yellow-300">BAN</span></span>
            </a>

            <nav class="hidden items-center gap-8 text-sm font-semibold text-slate-600 dark:text-slate-300 lg:flex" aria-label="Navigasi utama">
                <a class="nav-link" href="#layanan" x-text="t('navServices')">Layanan</a>
                <a class="nav-link" href="#katalog" x-text="t('navCatalog')">Katalog</a>
                <a class="nav-link" href="#toko" x-text="t('navStores')">Lokasi outlet</a>
                <a class="nav-link" href="{{ route('booking.status') }}" x-text="t('navBookingStatus')">Cek booking</a>
            </nav>

            <div class="flex items-center gap-2">
                <div class="relative block">
                    <button type="button" @click="languageMenuOpen = !languageMenuOpen" @click.outside="languageMenuOpen = false" :aria-expanded="languageMenuOpen" aria-label="Pilih bahasa" class="locale-trigger inline-flex h-10 items-center gap-2 rounded-full border border-slate-200 px-3 text-xs font-extrabold text-slate-700 transition hover:bg-slate-100 dark:border-white/10 dark:text-slate-200 dark:hover:bg-white/5">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M3 12h18M12 3a15 15 0 010 18m0-18a15 15 0 000 18"/></svg>
                        <span x-text="locale.toUpperCase()">ID</span>
                        <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div x-cloak x-show="languageMenuOpen" x-transition class="locale-menu absolute right-0 top-full z-50 mt-2 w-40 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-xl dark:border-white/10 dark:bg-slate-900">
                        <button type="button" @click="setLanguage('id')" class="locale-option" :class="locale === 'id' && 'locale-option-active'"><span>🇮🇩</span> Bahasa Indonesia</button>
                        <button type="button" @click="setLanguage('en')" class="locale-option" :class="locale === 'en' && 'locale-option-active'"><span>🇬🇧</span> English</button>
                    </div>
                </div>
                <button type="button" @click="toggleTheme()" :aria-label="isDark ? t('switchLight') : t('switchDark')" :aria-pressed="isDark" class="theme-toggle inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-slate-700 transition hover:bg-slate-100 dark:border-white/10 dark:text-yellow-200 dark:hover:bg-white/5">
                    <svg x-show="isDark" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path stroke-linecap="round" d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/></svg>
                    <svg x-show="!isDark" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.9 13A8.5 8.5 0 1111 3.1 6.7 6.7 0 0020.9 13z"/></svg>
                </button>
                @include('components.account-menu')
                <button
                    type="button"
                    @click="cartDrawerOpen = true"
                    class="cart-trigger relative inline-flex h-11 items-center gap-2 rounded-full border border-slate-200 bg-white px-4 text-sm font-bold text-slate-800 transition hover:border-cyan-300/40 hover:bg-cyan-50 active:scale-95 dark:border-white/10 dark:bg-white/[0.06] dark:text-white dark:hover:bg-cyan-300/10"
                    :aria-label="t('cart') + ', ' + cartCount"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.2 11.1a2 2 0 002 1.6h8.7a2 2 0 001.9-1.4L22 8H6M10 20a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/></svg>
                    <span class="hidden sm:inline" x-text="t('cart')">Keranjang</span>
                    <span x-show="cartCount > 0" x-text="cartCount" x-transition class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-yellow-300 px-1 text-[10px] font-black text-slate-950"></span>
                </button>
                <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" :aria-expanded="mobileMenuOpen" :aria-label="t('openNavigation')" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-slate-200 text-slate-700 dark:border-white/10 dark:text-white lg:hidden">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
            </div>
        </div>
        <nav x-cloak x-show="mobileMenuOpen" x-transition class="mobile-nav border-t border-white/10 px-5 py-4 lg:hidden" aria-label="Navigasi mobile">
            <div class="mx-auto flex max-w-7xl flex-col gap-1">
                <a @click="mobileMenuOpen = false" href="#layanan" class="mobile-nav-link" x-text="t('navServices')">Layanan</a>
                <a @click="mobileMenuOpen = false" href="#katalog" class="mobile-nav-link" x-text="t('mobileCatalog')">Katalog produk</a>
                <a @click="mobileMenuOpen = false" href="#toko" class="mobile-nav-link" x-text="t('navStores')">Lokasi outlet</a>
                <a @click="mobileMenuOpen = false" href="{{ route('booking.status') }}" class="mobile-nav-link" x-text="t('mobileBookingStatus')">Cek status booking</a>
                <button @click="openBookingModal()" class="mobile-nav-link text-left" x-text="t('bookingService')">Booking servis</button>
            </div>
        </nav>
    </header>

    <main>
        <section id="beranda" class="hero-shell relative isolate overflow-hidden">
            <div class="hero-grid absolute inset-0 -z-10" aria-hidden="true"></div>
            <div class="hero-glow hero-glow-blue absolute -right-24 top-0 -z-10" aria-hidden="true"></div>
            <div class="hero-glow hero-glow-yellow absolute -bottom-48 left-[-8rem] -z-10" aria-hidden="true"></div>
            <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 pb-20 pt-16 sm:px-6 md:pb-24 md:pt-24 lg:grid-cols-[1.05fr_.95fr] lg:px-8 lg:py-24">
                <div class="hero-copy">
                    <div class="mb-7 inline-flex items-center gap-2 rounded-full border border-emerald-300/20 bg-emerald-300/[0.08] px-3.5 py-2 text-xs font-bold tracking-wide text-emerald-700 dark:text-emerald-200">
                        <span class="status-pulse h-2 w-2 rounded-full bg-emerald-300"></span>
                        <span x-text="t('openToday')">BUKA HARI INI</span> <span class="text-emerald-500/60 dark:text-emerald-100/60">·</span> <span x-text="t('serviceAvailable')">Layanan tersedia</span>
                    </div>
                    <p class="mb-4 text-xs font-extrabold uppercase tracking-[.28em] text-cyan-700 dark:text-cyan-300" x-text="t('heroEyebrow')">Your ride, refined.</p>
                    <h1 class="hero-title max-w-3xl text-5xl font-black leading-[1.02] tracking-[-.055em] text-slate-950 dark:text-white sm:text-6xl lg:text-[4.5rem]">
                        <span x-text="t('heroTitle')">Performa motor,</span><br>
                        <span class="gradient-shimmer" x-text="t('heroSubtitle')">tanpa kompromi.</span>
                    </h1>
                    <p class="hero-description mt-6 max-w-xl text-base leading-7 text-slate-600 dark:text-slate-300 sm:text-lg" x-text="t('heroDescription')">Perawatan presisi, produk terjamin, dan mekanik ahli. Semua yang motor Anda butuhkan, dalam satu pit stop.</p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        <button type="button" @click="openBookingModal()" class="action-button action-button-primary group">
                            <span x-text="t('bookNow')">Booking servis sekarang</span>
                            <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                        </button>
                        <a href="#katalog" class="action-button action-button-glass group">
                            <span x-text="t('browseProducts')">Jelajahi produk</span>
                            <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                        </a>
                    </div>
                    <div x-data="statsCounter" class="hero-stats mt-12 grid grid-cols-3 gap-3 border-t border-slate-200 pt-7 dark:border-white/10 sm:gap-6">
                        <div class="stat-card rounded-xl p-2 transition hover:-translate-y-1 hover:bg-white/70 hover:shadow-lg dark:hover:bg-white/[0.06]"><p class="stat-value text-xl font-black text-slate-900 dark:text-white sm:text-2xl"><span x-text="formatNumber(counters[0].value)"></span><span class="text-cyan-600 dark:text-cyan-300">+</span></p><p class="stat-label mt-1 text-[10px] text-slate-500 dark:text-slate-400 sm:text-xs" x-text="t('happyCustomers')"></p></div>
                        <div class="stat-card rounded-xl p-2 transition hover:-translate-y-1 hover:bg-white/70 hover:shadow-lg dark:hover:bg-white/[0.06]"><p class="stat-value text-xl font-black text-slate-900 dark:text-white sm:text-2xl"><span x-text="formatNumber(counters[1].value)"></span><span class="text-cyan-600 dark:text-cyan-300">+</span></p><p class="stat-label mt-1 text-[10px] text-slate-500 dark:text-slate-400 sm:text-xs" x-text="t('branches')"></p></div>
                        <div class="stat-card rounded-xl p-2 transition hover:-translate-y-1 hover:bg-white/70 hover:shadow-lg dark:hover:bg-white/[0.06]"><p class="stat-value text-xl font-black text-slate-900 dark:text-white sm:text-2xl"><span x-text="formatNumber(counters[2].value)"></span><span class="text-cyan-600 dark:text-cyan-300">+</span></p><p class="stat-label mt-1 text-[10px] text-slate-500 dark:text-slate-400 sm:text-xs" x-text="t('completedServices')"></p></div>
                    </div>
                </div>

                <div class="hero-visual relative mx-auto w-full max-w-[610px]">
                    <div class="hero-image-frame image-shine group relative overflow-hidden rounded-[2rem] border border-white/15 bg-slate-800/50 shadow-2xl">
                        <img
                            src="https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=1400&q=85"
                            alt="Motor siap untuk perjalanan dengan perawatan profesional"
                            class="hero-image h-[390px] w-full object-cover sm:h-[480px]"
                            fetchpriority="high"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-[#090d13]/90 via-[#090d13]/10 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-4 p-6 sm:p-8">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-[.22em] text-yellow-300">Pit stop premium</span>
                                <p class="mt-2 text-2xl font-black text-white sm:text-3xl">Ride further.<br>Worry less.</p>
                            </div>
                            <div class="hidden rounded-2xl border border-white/20 bg-black/30 p-4 text-right backdrop-blur-xl sm:block">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-300">Standar layanan</p>
                                <p class="mt-1 text-lg font-black text-white">Planet<span class="text-yellow-300">Care</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="floating-note trust-badge absolute -bottom-7 -left-2 rounded-2xl border border-white/15 bg-slate-900/80 p-4 shadow-xl backdrop-blur-xl sm:-left-8 sm:p-5">
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-cyan-300/10 text-cyan-300">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3 4.5 6v5.5c0 4.2 3.2 7.8 7.5 9.5 4.3-1.7 7.5-5.3 7.5-9.5V6L12 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/></svg>
                            </span>
                            <div><p class="text-sm font-extrabold text-white" x-text="t('officialWarranty')"></p><p class="mt-1 text-xs text-slate-400" x-text="t('originalProducts')"></p></div>
                        </div>
                    </div>
                    <div class="floating-note trust-badge absolute -right-3 top-8 hidden rounded-2xl border border-cyan-300/20 bg-slate-900/80 px-4 py-3 shadow-xl backdrop-blur-xl sm:block">
                        <span class="block text-xs font-bold text-cyan-200" x-text="'● ' + t('freeConsultation')"></span>
                        <span class="mt-1 block text-[10px] text-slate-300" x-text="t('chatWithExperts')"></span>
                    </div>
                </div>
            </div>
        </section>

        <section id="layanan" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-24">
            <div class="mb-10 flex flex-col justify-between gap-5 md:flex-row md:items-end">
                <div>
                    <p class="section-kicker" x-text="t('serviceIntro')">Dibuat untuk perjalanan Anda</p>
                    <h2 class="section-title mt-3"><span x-text="t('serviceHeadline')">Satu pit stop.</span><br><span class="muted-heading text-slate-500" x-text="t('serviceHeadlineMuted')">Banyak beresnya.</span></h2>
                </div>
                <p class="secondary-copy max-w-md text-sm leading-6 text-slate-600 dark:text-slate-400" x-text="t('serviceDescription')">Servis dan kebutuhan motor dengan standar yang konsisten, ditangani oleh teknisi profesional di outlet terdekat.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="#katalog" @click="selectedCategory = 'tire'" class="service-card group">
                    <span class="service-icon text-yellow-300"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="3.5"/><path stroke-linecap="round" d="M12 3.5v5m0 7v5m8.5-8.5h-5m-7 0h-5"/></svg></span>
                    <h3 x-text="t('tires')">Ban motor</h3><p x-text="t('tiresDescription')">Grip maksimal, siap segala kondisi jalan.</p>
                    <span class="service-arrow"><span x-text="t('seeOptions')">Lihat pilihan</span> <span class="transition-transform group-hover:translate-x-1">→</span></span>
                </a>
                <a href="#katalog" @click="selectedCategory = 'oil'" class="service-card group">
                    <span class="service-icon text-cyan-300"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.5s6 6.4 6 11a6 6 0 01-12 0c0-4.6 6-11 6-11z"/><path stroke-linecap="round" d="M9 15.5a3 3 0 003 3"/></svg></span>
                    <h3 x-text="t('oils')">Oli & pelumas</h3><p x-text="t('oilsDescription')">Mesin halus, performa tetap optimal.</p>
                    <span class="service-arrow"><span x-text="t('seeOptions')">Lihat pilihan</span> <span class="transition-transform group-hover:translate-x-1">→</span></span>
                </a>
                <a href="#katalog" @click="selectedCategory = 'sparepart'" class="service-card group">
                    <span class="service-icon text-violet-300"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a5 5 0 00-6.5 6.5L3.8 17.2a2.1 2.1 0 003 3l4.4-4.4a5 5 0 006.5-6.5l-3 3-3.5-3.5 3.5-3.5z"/></svg></span>
                    <h3 x-text="t('spareparts')">Sparepart</h3><p x-text="t('sparepartsDescription')">Komponen pilihan untuk kenyamanan berkendara.</p>
                    <span class="service-arrow"><span x-text="t('seeOptions')">Lihat pilihan</span> <span class="transition-transform group-hover:translate-x-1">→</span></span>
                </a>
                <button type="button" @click="openBookingModal()" class="service-card group text-left">
                    <span class="service-icon text-emerald-300"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 18.5h16M6 16l2.2-5.5a2 2 0 011.9-1.3h3.8a2 2 0 011.9 1.3L18 16M8 16h.01M16 16h.01M8 7V5m8 2V5M5 9h14"/></svg></span>
                    <h3 x-text="t('serviceBooking')">Booking servis</h3><p x-text="t('serviceBookingDescription')">Pilih jadwal dan outlet, tanpa antre lama.</p>
                    <span class="service-arrow"><span x-text="t('setSchedule')">Atur jadwal</span> <span class="transition-transform group-hover:translate-x-1">→</span></span>
                </button>
            </div>
        </section>

        <section id="katalog" class="catalog-section border-y border-white/[0.06] py-20 lg:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-10 flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
                    <div>
                        <p class="section-kicker" x-text="t('catalogIntro')">Pilihan untuk motor Anda</p>
                        <h2 class="section-title mt-3"><span x-text="t('catalogHeadline')">Upgrade perjalanan.</span><br><span class="muted-heading text-slate-500" x-text="t('catalogHeadlineMuted')">Mulai dari sini.</span></h2>
                    </div>
                    <label class="search-box flex w-full items-center gap-3 rounded-2xl border border-white/10 bg-white/[0.04] px-4 py-3.5 lg:max-w-sm">
                        <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path stroke-linecap="round" d="m16 16 5 5"/></svg>
                        <input type="search" x-model="searchProduct" data-product-search :placeholder="t('searchProducts')" class="w-full bg-transparent text-sm text-slate-900 outline-none placeholder:text-slate-500 dark:text-white dark:placeholder:text-slate-500">
                    </label>
                </div>

                <div class="mb-8 flex flex-wrap gap-2" aria-label="Filter kategori produk">
                    <button @click="selectedCategory = 'all'" class="filter-chip" :class="selectedCategory === 'all' && 'filter-chip-active'" x-text="t('allProducts')">Semua produk</button>
                    <button @click="selectedCategory = 'tire'" class="filter-chip" :class="selectedCategory === 'tire' && 'filter-chip-active'" x-text="t('tires')">Ban motor</button>
                    <button @click="selectedCategory = 'oil'" class="filter-chip" :class="selectedCategory === 'oil' && 'filter-chip-active'" x-text="t('oils')">Oli mesin</button>
                    <button @click="selectedCategory = 'sparepart'" class="filter-chip" :class="selectedCategory === 'sparepart' && 'filter-chip-active'" x-text="t('spareparts')">Sparepart</button>
                    <button @click="selectedCategory = 'service'" class="filter-chip" :class="selectedCategory === 'service' && 'filter-chip-active'" x-text="t('serviceCategory')">Layanan servis</button>
                    <button @click="selectedCategory = 'all'; searchProduct = ''" class="ml-auto px-3 py-2 text-xs font-bold text-slate-500 transition hover:text-slate-900 dark:text-slate-400 dark:hover:text-white" x-text="t('resetFilters')"></button>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @forelse($products as $product)
                        <article
                            x-show="(selectedCategory === 'all' || selectedCategory === '{{ $product->category }}') && ($el.dataset.searchText || '').includes(searchProduct.toLowerCase())"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="translate-y-3 scale-[.98] opacity-0"
                            x-transition:enter-end="translate-y-0 scale-100 opacity-100"
                            data-search-text="{{ strtolower($product->name . ' ' . ($product->description ?? '') . ' ' . $product->category) }}"
                            class="product-card group flex flex-col"
                        >
                            <div class="relative">
                                <a
                                    href="{{ route('product.show', $product) }}"
                                    class="product-visual image-shine relative flex h-56 items-center justify-center overflow-hidden rounded-2xl bg-[#111923] p-5"
                                    aria-label="Lihat detail {{ $product->name }}"
                                >
                                    @if($product->image)
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" loading="lazy" class="product-image h-full w-full object-contain">
                                    @else
                                        <span class="text-6xl" aria-hidden="true">{{ $product->category === 'oil' ? '🛢️' : ($product->category === 'tire' ? '🛞' : '⚙️') }}</span>
                                    @endif
                                    <span class="absolute bottom-3 left-3 rounded-full border border-white/10 bg-slate-950/60 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-slate-200 backdrop-blur" x-text="t('{{ $product->category === 'tire' ? 'tires' : ($product->category === 'oil' ? 'oils' : ($product->category === 'service' ? 'serviceCategory' : 'spareparts')) }}')"></span>
                                </a>
                                @if($product->has_discount)
                                    <span class="absolute right-3 top-3 rounded-full bg-yellow-300 px-3 py-1.5 text-[10px] font-black uppercase tracking-wide text-slate-950">Hemat {{ $product->discount_percentage }}%</span>
                                @elseif($product->stock > 0 && $product->stock <= 5)
                                    <span class="absolute right-3 top-3 rounded-full bg-rose-400 px-3 py-1.5 text-[10px] font-black uppercase tracking-wide text-slate-950" x-text="t('limitedStock')"></span>
                                @endif
                            </div>
                            <div class="flex flex-1 flex-col p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <h3 class="line-clamp-2 text-sm font-extrabold leading-5 text-white">
                                        <a href="{{ route('product.show', $product) }}" class="transition hover:text-cyan-300 focus-visible:rounded-sm" aria-label="Lihat deskripsi {{ $product->name }}">{{ $product->name }}</a>
                                    </h3>
                                    <span class="mt-0.5 h-2 w-2 shrink-0 rounded-full {{ $product->stock > 0 ? 'bg-emerald-300' : 'bg-rose-400' }}"></span>
                                </div>
                                <p class="mt-2 line-clamp-2 min-h-10 text-xs leading-5 text-slate-400">{{ $product->description ?? 'Komponen berkualitas untuk performa motor yang lebih optimal.' }}</p>
                                <div class="mt-auto border-t border-white/[0.08] pt-4">
                                    <div class="mb-4 flex items-end justify-between gap-2">
                                        <div>
                                            @if($product->has_discount)
                                                <p class="text-[11px] text-slate-500 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                            @endif
                                            <p class="text-base font-black text-yellow-300">Rp {{ number_format($product->effective_price, 0, ',', '.') }}</p>
                                        </div>
                                        <span class="text-[10px] font-semibold {{ $product->stock > 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-500 dark:text-rose-300' }}"><template x-if="{{ $product->stock > 0 ? 'true' : 'false' }}"><span><span x-text="t('stockLeft')"></span> {{ $product->stock }}</span></template><template x-if="{{ $product->stock < 1 ? 'true' : 'false' }}"><span x-text="t('outOfStock')"></span></template></span>
                                    </div>
                                    <button
                                        type="button"
                                        @click="openBookingModal($el.dataset.productId)"
                                        data-product-id="{{ $product->id }}"
                                        class="action-button action-button-card group/btn"
                                    >
                                        <span x-text="t('bookProduct')"></span>
                                        <svg class="h-4 w-4 transition-transform duration-300 group-hover/btn:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                                    </button>
                                    <a
                                        href="{{ route('product.show', $product) }}"
                                        class="mt-2 block w-full rounded-xl border border-cyan-300/30 py-2.5 text-center text-xs font-bold text-cyan-800 transition hover:border-cyan-400 hover:bg-cyan-50 active:scale-[.98] dark:text-cyan-200 dark:hover:bg-cyan-300/[.06]"
                                    >{{ app()->getLocale() === 'en' ? 'View product details' : 'Lihat deskripsi produk' }}</a>
                                    <button
                                        type="button"
                                        @click="addToCartFromButton($el)"
                                        data-product-id="{{ $product->id }}"
                                        data-product-name="{{ $product->name }}"
                                        data-product-price="{{ $product->effective_price }}"
                                        data-product-image="{{ $product->image }}"
                                        data-product-stock="{{ $product->stock }}"
                                        :disabled="{{ $product->stock < 1 ? 'true' : 'false' }}"
                                        class="mt-2 w-full rounded-xl border border-white/10 py-2.5 text-xs font-bold text-slate-300 transition hover:border-cyan-300/40 hover:bg-cyan-300/[0.06] hover:text-cyan-100 active:scale-[.98] disabled:cursor-not-allowed disabled:opacity-40"
                                    ><span x-text="t('addToCart')"></span></button>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-3xl border border-white/10 bg-white/[0.03] px-6 py-16 text-center">
                            <p class="text-lg font-bold text-slate-900 dark:text-white" x-text="t('productsPreparing')"></p>
                            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400" x-text="t('productsPreparingDescription')"></p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <section id="toko" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-24">
            <div class="mb-8 flex flex-col justify-between gap-5 md:flex-row md:items-end">
                <div><p class="section-kicker" x-text="t('nearby')"></p><h2 class="section-title mt-3"><span x-text="t('findPitstop')"></span><br><span class="muted-heading text-slate-500" x-text="t('nearest')"></span></h2></div>
                <input type="search" x-model="searchStore" :placeholder="t('searchStores')" class="search-box w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none placeholder:text-slate-500 focus:border-cyan-500 dark:border-white/10 dark:bg-white/[0.04] dark:text-white dark:placeholder:text-slate-500 md:max-w-xs">
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                @forelse($stores as $store)
                    <article
                        x-show="searchStore === '' || $el.dataset.storeSearch.includes(searchStore.toLowerCase())"
                        data-store-search="{{ strtolower($store->name . ' ' . $store->city . ' ' . $store->address) }}"
                        class="store-card flex flex-col justify-between gap-5 rounded-2xl border border-white/[0.08] bg-white/[0.03] p-5 sm:flex-row sm:items-center sm:p-6"
                    >
                        <div class="flex gap-4">
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-cyan-300/10 text-cyan-200">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 4l9 6.5v9a.5.5 0 01-.5.5h-17a.5.5 0 01-.5-.5v-9z"/><path stroke-linecap="round" d="M9 20v-6h6v6"/></svg>
                            </span>
                            <div>
                                <h3 class="font-extrabold text-white">{{ $store->name }}</h3>
                                <p class="mt-1 text-xs leading-5 text-slate-400">{{ $store->address }}, {{ $store->city }}</p>
                                <p class="mt-2 text-xs font-semibold text-slate-300">{{ $store->phone }}</p>
                            </div>
                        </div>
                        <button type="button" @click="openBookingModalWithStore('{{ $store->id }}')" class="inline-flex shrink-0 items-center justify-center gap-2 rounded-full border border-cyan-300/20 px-4 py-2.5 text-xs font-bold text-cyan-800 transition hover:border-cyan-300/50 hover:bg-cyan-300/10 active:scale-95 dark:text-cyan-100">
                        <span x-text="t('bookHere')"></span> <span aria-hidden="true">→</span>
                        </button>
                    </article>
                @empty
                    <div class="col-span-full rounded-2xl border border-white/10 p-8 text-center text-sm text-slate-400">Data outlet belum tersedia.</div>
                @endforelse
            </div>
        </section>

        <section class="mx-4 mb-20 overflow-hidden rounded-[2rem] border border-yellow-200/20 bg-gradient-to-r from-yellow-300 to-amber-400 sm:mx-6 lg:mx-auto lg:mb-24 lg:max-w-7xl">
            <div class="flex flex-col items-start justify-between gap-6 px-6 py-10 sm:px-10 md:flex-row md:items-center md:px-14 md:py-12">
                <div><p class="text-xs font-black uppercase tracking-[.2em] text-slate-800/70" x-text="t('readyToRide')"></p><h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl" x-text="t('bikeStartsHere')"></h2><p class="mt-3 max-w-xl text-sm text-slate-800/75" x-text="t('planVisit')"></p></div>
                <button type="button" @click="openBookingModal()" class="action-button action-button-dark group shrink-0">
                    <span x-text="t('bookingNow')"></span><svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </button>
            </div>
        </section>
    </main>

    <x-cart-drawer />

    <div x-cloak x-show="bookingModalOpen" x-transition.opacity @click.self="closeOverlays()" class="booking-overlay fixed inset-0 z-[70] flex items-center justify-center p-4" role="presentation">
        <section
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-y-4 scale-95 opacity-0"
            x-transition:enter-end="translate-y-0 scale-100 opacity-100"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-y-0 scale-100 opacity-100"
            x-transition:leave-end="translate-y-4 scale-95 opacity-0"
            class="booking-modal relative max-h-[92vh] w-full max-w-xl overflow-y-auto rounded-3xl border border-white/15 bg-slate-900/90 p-6 shadow-2xl backdrop-blur-2xl sm:p-8"
            role="dialog"
            aria-modal="true"
            aria-labelledby="booking-title"
        >
            <button type="button" @click="closeOverlays()" :disabled="isLoading" class="absolute right-5 top-5 flex h-9 w-9 items-center justify-center rounded-full border border-white/10 text-xl text-slate-300 transition hover:bg-white/10 hover:text-white disabled:opacity-50" :aria-label="t('closeBooking')">&times;</button>
            <p class="section-kicker" x-text="t('bookingEyebrow')"></p>
            <h2 id="booking-title" class="mt-2 pr-10 text-2xl font-black text-white" x-text="t('bookingTitle')"></h2>
            <p class="mb-6 mt-2 text-sm text-slate-400" x-text="t('bookingDescription')"></p>

            <form action="{{ route('booking.store') }}" method="POST" @submit.prevent="submitForm($event)" class="booking-form space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="form-field"><span x-text="t('fullName')">Nama lengkap</span><input type="text" name="customer_name" value="{{ old('customer_name', Auth::user()?->name) }}" required maxlength="255" :placeholder="t('namePlaceholder')" placeholder="Nama Anda"></label>
                    <label class="form-field"><span x-text="t('phone')">No. WhatsApp / HP</span><input type="tel" name="customer_phone" value="{{ old('customer_phone', Auth::user()?->phone) }}" required maxlength="20" :placeholder="t('phonePlaceholder')" placeholder="081234567890"></label>
                </div>
                <label class="form-field"><span x-text="t('chooseStore')"></span>
                    <select name="store_id" x-model="selectedStoreId" required>
                        <option value="" x-text="t('chooseStorePlaceholder')"></option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }} ({{ $store->city }})</option>
                        @endforeach
                    </select>
                </label>
                <label class="form-field"><span><span x-text="t('chooseProduct')"></span> <span class="font-normal normal-case text-slate-500" x-text="t('optional')"></span></span>
                    <select name="product_id" x-model="selectedProductId">
                        <option value="" x-text="t('serviceOnly')"></option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} — Rp {{ number_format($product->effective_price, 0, ',', '.') }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="form-field"><span x-text="t('visitDate')"></span><input type="date" name="booking_date" required min="{{ date('Y-m-d') }}" value="{{ old('booking_date') }}"></label>
                    <label class="form-field"><span x-text="t('visitTime')"></span>
                        <select name="booking_time" required>
                            @foreach(['09:00', '11:00', '13:00', '15:00', '17:00'] as $time)
                                <option value="{{ $time }}" @selected(old('booking_time', '09:00') === $time)>{{ $time }} WIB</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                @include('components.payment-method-options')
                @if($errors->any())
                    <div role="alert" class="rounded-xl border border-rose-300/20 bg-rose-300/10 px-4 py-3 text-sm text-rose-200">
                        <p class="font-bold">Periksa kembali data booking Anda.</p>
                        <ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                <div class="flex flex-col-reverse gap-3 border-t border-white/10 pt-5 sm:flex-row sm:justify-end">
                    <button type="button" @click="closeOverlays()" :disabled="isLoading" class="rounded-xl border border-white/15 px-5 py-3 text-sm font-bold text-slate-300 transition hover:bg-white/[0.06] disabled:opacity-50" x-text="t('cancel')"></button>
                    <button type="submit" :disabled="isLoading" class="action-button action-button-primary group justify-center disabled:cursor-wait disabled:opacity-60">
                        <svg x-show="isLoading" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <span x-text="isLoading ? t('sending') : t('confirmBooking')"></span>
                        <svg x-show="!isLoading" class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </button>
                </div>
            </form>
        </section>
    </div>

    @include('components.site-footer')

    <aside x-cloak x-show="liveNotificationVisible" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="translate-y-3 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-2 opacity-0" class="live-notification fixed bottom-5 left-5 z-30 flex max-w-[min(21rem,calc(100vw-2.5rem))] items-center gap-3 rounded-2xl border border-slate-200 bg-white/90 p-3.5 shadow-xl backdrop-blur-xl dark:border-white/10 dark:bg-slate-900/90 sm:bottom-7 sm:left-7" role="status" aria-live="polite">
        <span class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-300/10 dark:text-emerald-200">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12.5 9.5 17 19 7.5"/></svg>
            <span class="absolute right-0 top-0 h-2.5 w-2.5 rounded-full border-2 border-white bg-emerald-400 dark:border-slate-900"></span>
        </span>
        <span class="min-w-0"><span class="block text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400"><span x-text="t('liveActivity')"></span><span class="ml-1 font-medium normal-case" x-text="'· ' + t('liveDemo')"></span></span><span class="mt-1 block text-xs font-bold leading-4 text-slate-800 dark:text-slate-100" x-text="liveNotification"></span></span>
    </aside>

    <a href="https://wa.me/628170006055" target="_blank" rel="noopener noreferrer" class="support-float fixed bottom-5 right-5 z-30 flex items-center gap-3 rounded-full border border-white/15 bg-slate-900/80 px-4 py-3 shadow-xl backdrop-blur-xl transition hover:-translate-y-1 hover:border-emerald-300/40 sm:bottom-7 sm:right-7">
        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-300/10 text-emerald-300">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.5 3.5A11.8 11.8 0 0012.1 0C5.5 0 .1 5.3 0 11.9c0 2.1.6 4.2 1.7 6L0 24l6.3-1.6a12 12 0 005.8 1.5h.1c6.6 0 12-5.3 12-11.9a11.8 11.8 0 00-3.7-8.5zM12.1 21.9a10 10 0 01-5.1-1.4l-.4-.2-3.8 1 1-3.7-.3-.4a9.9 9.9 0 01-1.5-5.3A10.1 10.1 0 0112.1 2a10 10 0 017.1 2.9 9.9 9.9 0 012.9 7.1 10.1 10.1 0 01-10 9.9zm5.5-7.5c-.3-.1-1.7-.8-2-.9-.3-.1-.5-.1-.7.2-.2.3-.8.9-1 .9-.2.2-.4.2-.7.1-.3-.2-1.2-.4-2.3-1.4-.8-.7-1.4-1.6-1.6-1.9-.2-.3 0-.5.1-.6l.5-.6.3-.5c.1-.2 0-.4 0-.5l-.9-2.1c-.2-.6-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.4s1 2.8 1.1 3c.1.2 2 3.1 4.8 4.3.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.6-.1 1.7-.7 1.9-1.3.2-.6.2-1.1.2-1.2-.1-.2-.3-.3-.6-.5z"/></svg>
        </span>
        <span class="hidden text-left sm:block"><span class="block text-[10px] font-semibold text-slate-400">Butuh bantuan?</span><span class="text-xs font-extrabold text-white">Chat WhatsApp</span></span>
    </a>
</body>
</html>
