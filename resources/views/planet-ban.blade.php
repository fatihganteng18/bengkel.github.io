<!DOCTYPE html>
<html lang="id" class="scroll-smooth dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#090d13">
    <title>Arisa Knalpot | Knalpot Mobil & Workshop Performance</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased transition-colors duration-300 dark:bg-slate-900 dark:text-slate-100"
    x-data="planetBanApp"
    data-authenticated="{{ Auth::check() ? 'true' : 'false' }}"
    data-login-url="{{ route('login') }}"
    data-register-url="{{ route('register') }}"
    x-init="@if($errors->any()) bookingModalOpen = true; bookingItems = @js($bookingItems); selectedStoreId = @js((string) old('store_id', '')) @endif"
    x-effect="document.body.classList.toggle('overflow-hidden', cartDrawerOpen || bookingModalOpen)"
    @keydown.escape.window="closeOverlays()"
>
    @if(session('success'))
        <div class="relative z-[80] flex items-center justify-center gap-3 bg-emerald-400 px-4 py-3 text-center text-sm font-bold text-slate-950">
            <span>{{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-xl leading-none" aria-label="Tutup notifikasi">&times;</button>
        </div>
    @endif

    <header class="site-nav relative z-40">
        <div class="site-header-inner mx-auto flex h-[76px] max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="brand-mark flex shrink-0 items-center gap-2" aria-label="Arisa Knalpot, beranda">
                <span class="brand-symbol">E</span>
                <span class="text-lg font-black italic tracking-tight text-white">Arisa<span class="text-lime-300"> Knalpot</span></span>
            </a>

            <nav class="hidden items-center gap-8 text-sm font-semibold text-slate-600 dark:text-slate-300 lg:flex" aria-label="Navigasi utama">
                <a class="nav-link" href="#layanan" x-text="t('navServices')">Layanan</a>
                <a class="nav-link" href="#katalog" x-text="t('navCatalog')">Katalog</a>
                <a class="nav-link" href="#toko" x-text="t('navStores')">Lokasi outlet</a>
                <a class="nav-link" href="{{ route('booking.status') }}" x-text="t('navBookingStatus')">Cek booking</a>
            </nav>

            <div class="site-header-actions flex items-center gap-2">
                <div class="site-language-control relative block">
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
                <div class="site-account-control">
                    @include('components.account-menu')
                </div>
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
                <div class="mt-2 grid grid-cols-2 gap-2 border-t border-white/10 pt-3 sm:grid-cols-3">
                    @guest
                        <a href="{{ route('login') }}" class="mobile-nav-link rounded-xl border border-white/10 text-center" x-text="t('login')">Masuk</a>
                        <a href="{{ route('register') }}" class="mobile-nav-link rounded-xl bg-yellow-300 text-center font-extrabold text-slate-950" x-text="t('register')">Daftar</a>
                    @else
                        <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : (Auth::user()->role === 'cashier' ? route('cashir.dashboard') : route('account.dashboard')) }}" class="mobile-nav-link rounded-xl border border-white/10 text-center" x-text="t('{{ Auth::user()->role === 'admin' ? 'adminDashboard' : (Auth::user()->role === 'cashier' ? 'cashierDashboard' : 'myAccount') }}')">Akun saya</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="mobile-nav-link w-full rounded-xl border border-white/10 text-center" x-text="t('logout')">Keluar</button>
                        </form>
                    @endguest
                    <button type="button" @click="setLanguage('id')" class="mobile-nav-link rounded-xl border border-white/10 text-center" :aria-pressed="locale === 'id'">🇮🇩 Bahasa Indonesia</button>
                    <button type="button" @click="setLanguage('en')" class="mobile-nav-link rounded-xl border border-white/10 text-center" :aria-pressed="locale === 'en'">🇬🇧 English</button>
                </div>
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
                    <p class="mb-4 text-xs font-extrabold uppercase tracking-[.28em] text-cyan-700 dark:text-cyan-300" x-text="t('heroEyebrow')">ARISA KNALPOT · BUILT FOR YOUR VIBE</p>
                    <h1 class="hero-title max-w-3xl text-5xl font-black leading-[1.02] tracking-[-.055em] text-slate-950 dark:text-white sm:text-6xl lg:text-[4.5rem]">
                        <span x-text="t('heroTitle')">Auto ganteng,</span><br>
                        <span class="gradient-shimmer" x-text="t('heroSubtitle')">suara punya karakter.</span>
                    </h1>
                    <p class="hero-description mt-6 max-w-xl text-base leading-7 text-slate-600 dark:text-slate-300 sm:text-lg" x-text="t('heroDescription')">Knalpot custom, welding presisi, dan setup suara yang pas. Perform maksimal buat daily drive sampai weekend run.</p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        <button type="button" @click="openBookingModal()" class="action-button action-button-primary group">
                            <span x-text="t('bookNow')">Booking workshop</span>
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
                            src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1400&q=85"
                            alt="Mobil performance dengan setup otomotif modern"
                            class="hero-image h-[390px] w-full object-cover sm:h-[480px]"
                            fetchpriority="high"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-[#090d13]/90 via-[#090d13]/10 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-4 p-6 sm:p-8">
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-[.22em] text-lime-300">Custom exhaust workshop</span>
                                <p class="mt-2 text-2xl font-black text-white sm:text-3xl">Sound different.<br>Drive your vibe.</p>
                            </div>
                            <div class="hidden rounded-2xl border border-white/20 bg-black/30 p-4 text-right backdrop-blur-xl sm:block">
                                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-300">Standar layanan</p>
                                <p class="mt-1 text-lg font-black text-white">TIG<span class="text-lime-300"> Argon</span></p>
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
                    <p class="section-kicker" x-text="t('serviceIntro')">BUILT IN THE WORKSHOP</p>
                    <h2 class="section-title mt-3"><span x-text="t('serviceHeadline')">Setup sesuai mesin.</span><br><span class="muted-heading text-slate-500" x-text="t('serviceHeadlineMuted')">Sound sesuai karakter.</span></h2>
                </div>
                <p class="secondary-copy max-w-md text-sm leading-6 text-slate-600 dark:text-slate-400" x-text="t('serviceDescription')">Dari penggantian frontpipe sampai custom valvetronic, setiap setup dirancang buat perform maksimal dan tetap nyaman dipakai.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="#katalog" class="service-card group">
                    <span class="service-icon text-yellow-300"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="8.5"/><circle cx="12" cy="12" r="3.5"/><path stroke-linecap="round" d="M12 3.5v5m0 7v5m8.5-8.5h-5m-7 0h-5"/></svg></span>
                    <h3 x-text="t('tires')">Full system</h3><p x-text="t('tiresDescription')">Flow optimal, vibes racing, siap daily.</p>
                    <span class="service-arrow"><span x-text="t('seeOptions')">Lihat pilihan</span> <span class="transition-transform group-hover:translate-x-1">→</span></span>
                </a>
                <a href="#katalog" class="service-card group">
                    <span class="service-icon text-cyan-300"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.5s6 6.4 6 11a6 6 0 01-12 0c0-4.6 6-11 6-11z"/><path stroke-linecap="round" d="M9 15.5a3 3 0 003 3"/></svg></span>
                    <h3 x-text="t('oils')">Silencer & resonator</h3><p x-text="t('oilsDescription')">Bass bulat, adem, atau silent mode.</p>
                    <span class="service-arrow"><span x-text="t('seeOptions')">Lihat pilihan</span> <span class="transition-transform group-hover:translate-x-1">→</span></span>
                </a>
                <a href="#katalog" class="service-card group">
                    <span class="service-icon text-violet-300"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.7 6.3a5 5 0 00-6.5 6.5L3.8 17.2a2.1 2.1 0 003 3l4.4-4.4a5 5 0 006.5-6.5l-3 3-3.5-3.5 3.5-3.5z"/></svg></span>
                    <h3 x-text="t('spareparts')">Header custom</h3><p x-text="t('sparepartsDescription')">Welding TIG Argon, fitment presisi.</p>
                    <span class="service-arrow"><span x-text="t('seeOptions')">Lihat pilihan</span> <span class="transition-transform group-hover:translate-x-1">→</span></span>
                </a>
                <button type="button" @click="openBookingModal()" class="service-card group text-left">
                    <span class="service-icon text-emerald-300"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 18.5h16M6 16l2.2-5.5a2 2 0 011.9-1.3h3.8a2 2 0 011.9 1.3L18 16M8 16h.01M16 16h.01M8 7V5m8 2V5M5 9h14"/></svg></span>
                    <h3 x-text="t('serviceBooking')">Booking servis</h3><p x-text="t('serviceBookingDescription')">Pilih jadwal dan outlet, tanpa antre lama.</p>
                    <span class="service-arrow"><span x-text="t('setSchedule')">Atur jadwal</span> <span class="transition-transform group-hover:translate-x-1">→</span></span>
                </button>
            </div>
        </section>

        <section id="katalog" class="catalog-section scroll-mt-24 border-y border-slate-200 bg-slate-50 py-12 lg:py-16">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-7">
                    <p class="section-kicker">ARISA KNALPOT · SHOP BY SOUND</p>
                    <h2 class="section-title mt-3">Anti dengerin suara standar.<br><span class="gradient-shimmer">Build your own vibe.</span></h2>
                </div>
                <div class="mb-6">
                    <p class="mb-3 text-xs font-black uppercase tracking-[.2em] text-cyan-300">11 ways to sound like you</p>
                    <div class="flex gap-2 overflow-x-auto pb-2" aria-label="Filter kategori knalpot">
                        <a href="{{ route('home', request()->except(['page', 'sub_category'])) }}#katalog" @class(['brand-tile shrink-0 rounded-full border px-4 py-2 text-xs font-bold transition', 'brand-tile-active border-cyan-300 bg-slate-800' => empty($filters['sub_category']), 'border-slate-700 bg-slate-900 text-slate-200' => ! empty($filters['sub_category'])])>Semua setup</a>
                        @foreach($catalogSubCategories as $subCategory)
                            <a href="{{ route('home', array_merge(request()->except('page'), ['sub_category' => $subCategory])) }}#katalog" @class(['brand-tile shrink-0 rounded-full border px-4 py-2 text-xs font-bold transition', 'brand-tile-active border-cyan-300 bg-slate-800' => ($filters['sub_category'] ?? '') === $subCategory, 'border-slate-700 bg-slate-900 text-slate-200' => ($filters['sub_category'] ?? '') !== $subCategory])>{{ $subCategory }}</a>
                        @endforeach
                    </div>
                </div>

                <form method="GET" action="{{ route('home') }}" class="catalog-filter-form">
                    @if(! empty($filters['sub_category']))
                        <input type="hidden" name="sub_category" value="{{ $filters['sub_category'] }}">
                    @endif
                <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center">
                    <label class="catalog-search flex h-11 w-full items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 md:max-w-[350px]">
                        <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8"/><path stroke-linecap="round" d="m16 16 5 5"/></svg>
                        <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" data-product-search placeholder="Cari knalpot, diameter, atau karakter suara..." class="w-full bg-transparent text-xs text-slate-800 outline-none placeholder:text-slate-400">
                    </label>
                    <p class="text-xs text-slate-500" aria-live="polite">
                        Menampilkan <strong class="font-bold text-slate-800">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</strong>
                        dari {{ $products->total() }} Data
                    </p>
                    <label class="ml-auto flex items-center gap-2 text-xs text-slate-500">
                        <span>Urutkan</span>
                        <select name="sort" class="h-10 min-w-36 rounded-lg border border-slate-200 bg-white px-3 text-xs text-slate-600 outline-none focus:border-blue-700 focus:ring-2 focus:ring-blue-100">
                            <option value="relevance" @selected(($filters['sort'] ?? 'relevance') === 'relevance')>Pilih</option>
                            <option value="price-asc" @selected(($filters['sort'] ?? '') === 'price-asc')>Harga terendah</option>
                            <option value="price-desc" @selected(($filters['sort'] ?? '') === 'price-desc')>Harga tertinggi</option>
                            <option value="name" @selected(($filters['sort'] ?? '') === 'name')>Nama produk</option>
                        </select>
                    </label>
                </div>

                <div class="grid items-start gap-5 md:grid-cols-[235px_minmax(0,1fr)]">
                    <aside class="catalog-filter-panel overflow-hidden rounded-xl border border-slate-200 bg-white" aria-label="Filter produk">
                        <div class="border-b border-slate-200 px-4 py-3">
                            <div class="mb-2 flex items-center justify-between">
                                <h2 class="text-sm font-extrabold text-slate-900">Filter setup</h2>
                                <a href="{{ route('home') }}#katalog" class="text-[11px] font-semibold text-blue-700 hover:underline">Reset</a>
                            </div>
                            <div class="space-y-2">
                                <label class="flex cursor-pointer items-center gap-2 text-xs text-slate-700">
                                    <input type="radio" name="category" value="all" @checked(($filters['category'] ?? 'all') === 'all')>
                                    Semua setup
                                </label>
                                <label class="flex cursor-pointer items-center gap-2 text-xs text-slate-700">
                                    <input type="radio" name="category" value="exhaust" @checked(($filters['category'] ?? '') === 'exhaust')>
                                    Knalpot mobil
                                </label>
                            </div>
                        </div>

                        <div class="border-b border-slate-200 px-4 py-3">
                            <h3 class="mb-2 text-xs font-bold text-slate-800">Harga</h3>
                            <div class="flex items-center gap-2">
                                <label class="catalog-price-input flex min-w-0 flex-1 items-center gap-1 rounded-lg border border-slate-300 px-2">
                                    <span class="text-[10px] text-slate-400">Rp</span>
                                    <input type="number" name="min_price" min="0" value="{{ $filters['min_price'] ?? '' }}" placeholder="Min" class="w-full min-w-0 py-2 text-[10px] text-slate-700 outline-none">
                                </label>
                                <span class="text-xs text-slate-400">–</span>
                                <label class="catalog-price-input flex min-w-0 flex-1 items-center gap-1 rounded-lg border border-slate-300 px-2">
                                    <span class="text-[10px] text-slate-400">Rp</span>
                                    <input type="number" name="max_price" min="0" value="{{ $filters['max_price'] ?? '' }}" placeholder="Maks" class="w-full min-w-0 py-2 text-[10px] text-slate-700 outline-none">
                                </label>
                            </div>
                        </div>

                        <div class="border-b border-slate-200 px-4 py-3">
                            <label for="catalog-brand" class="mb-2 block text-xs font-bold text-slate-800">Merek</label>
                            <input id="catalog-brand" type="search" name="brand" value="{{ $filters['brand'] ?? '' }}" placeholder="Contoh: Apex" class="h-9 w-full rounded-lg border border-slate-200 bg-white px-2 text-[11px] text-slate-600">
                        </div>

                        <details class="catalog-filter-accordion border-b border-slate-200 px-4 py-3">
                            <summary>Material</summary>
                            <select name="material" class="mt-3 h-9 w-full rounded-lg border border-slate-200 bg-white px-2 text-[11px] text-slate-600">
                                <option value="">Semua material</option>
                                @foreach(['Stainless Steel', 'Galvanis', 'Karbon'] as $material)
                                    <option value="{{ $material }}" @selected(($filters['material'] ?? '') === $material)>{{ $material }}</option>
                                @endforeach
                            </select>
                        </details>
                        <details class="catalog-filter-accordion border-b border-slate-200 px-4 py-3">
                            <summary>Inlet / diameter</summary>
                            <select name="inlet_size" class="mt-3 h-9 w-full rounded-lg border border-slate-200 bg-white px-2 text-[11px] text-slate-600">
                                <option value="">Semua diameter</option>
                                @foreach(['1.5 inch', '2.0 inch', '2.25 inch', '2.5 inch', '3.0 inch'] as $inlet)
                                    <option value="{{ $inlet }}" @selected(($filters['inlet_size'] ?? '') === $inlet)>{{ $inlet }}</option>
                                @endforeach
                            </select>
                        </details>
                        <details class="catalog-filter-accordion border-b border-slate-200 px-4 py-3">
                            <summary>Karakter suara</summary>
                            <select name="sound_type" class="mt-3 h-9 w-full rounded-lg border border-slate-200 bg-white px-2 text-[11px] text-slate-600">
                                <option value="">Semua karakter</option>
                                @foreach(['Suara Halus Standart', 'Bass Bulat / Adem', 'Gahar', 'Silent'] as $sound)
                                    <option value="{{ $sound }}" @selected(($filters['sound_type'] ?? '') === $sound)>{{ $sound }}</option>
                                @endforeach
                            </select>
                        </details>
                        <details class="catalog-filter-accordion border-b border-slate-200 px-4 py-3">
                            <summary>Kapasitas mesin</summary>
                            <select name="engine_cc" class="mt-3 h-9 w-full rounded-lg border border-slate-200 bg-white px-2 text-[11px] text-slate-600">
                                <option value="">Semua kapasitas</option>
                                @foreach(['1.0cc - 1.6cc', '1.8cc - 2.0cc', '2.5cc - 3.5cc'] as $engineRange)
                                    <option value="{{ $engineRange }}" @selected(($filters['engine_cc'] ?? '') === $engineRange)>{{ $engineRange }}</option>
                                @endforeach
                            </select>
                        </details>
                        <div class="p-3">
                            <button type="submit" class="w-full rounded-full bg-blue-700 px-4 py-3 text-xs font-extrabold text-white shadow-sm transition hover:bg-blue-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-700">Tampilkan</button>
                        </div>
                    </aside>

                    <div class="grid grid-cols-2 gap-3 lg:grid-cols-3 2xl:grid-cols-4">
                    @forelse($products as $product)
                        <article
                            class="catalog-product-card group overflow-hidden rounded-xl border border-slate-200 bg-white transition duration-200 hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-lg"
                        >
                            <div class="relative flex h-32 items-center justify-center bg-white p-3 sm:h-40">
                                <a
                                    href="{{ route('product.show', $product) }}"
                                    class="flex h-full w-full items-center justify-center"
                                    aria-label="Lihat detail {{ $product->name }}"
                                >
                                    @if($product->image)
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/products/exhaust-sample.svg') }}'" class="h-full w-full object-contain drop-shadow-md">
                                    @else
                                        <span class="text-5xl" aria-hidden="true">⌁</span>
                                    @endif
                                </a>
                                @if($product->has_discount)
                                    <span class="absolute left-0 top-0 rounded-br-lg bg-red-600 px-2.5 py-1.5 text-[9px] font-extrabold text-white shadow-sm">Diskon Harga</span>
                                @endif
                            </div>
                            <div class="px-3 pb-3 pt-1 sm:px-4 sm:pb-4">
                                <a href="{{ route('product.show', $product) }}" class="line-clamp-2 min-h-9 text-[10px] font-medium leading-4 text-slate-600 hover:text-blue-800 sm:text-[11px]">{{ $product->name }}</a>
                                <p class="mt-1 line-clamp-1 text-[9px] font-semibold uppercase tracking-wide text-cyan-700 dark:text-cyan-300">{{ $product->sub_category }}</p>
                                <div class="mt-2 flex flex-wrap gap-1">
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-[8px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $product->inlet_size }}</span>
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-[8px] font-bold text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ $product->sound_type }}</span>
                                </div>
                                <div class="mt-1 flex flex-wrap items-baseline gap-x-1.5 gap-y-0.5">
                                    @if($product->has_discount)
                                        <span class="text-[9px] text-slate-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    @endif
                                    <span class="text-xs font-extrabold text-slate-900 sm:text-sm">Rp {{ number_format($product->effective_price, 0, ',', '.') }}</span>
                                </div>
                                @if($product->has_discount)
                                    <p class="mt-0.5 text-[9px] font-bold text-red-600">Hemat {{ $product->discount_percentage }}%</p>
                                @endif
                                <div class="mt-3 flex items-center gap-2 border-t border-slate-100 pt-2">
                                    <button type="button" @click="addToCartFromButton($el)" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-price="{{ $product->effective_price }}" data-product-image="{{ $product->image }}" data-product-stock="{{ $product->stock }}" :disabled="{{ $product->stock < 1 ? 'true' : 'false' }}" class="flex h-8 flex-1 items-center justify-center rounded-lg border border-blue-700 text-[10px] font-bold text-blue-700 transition hover:bg-blue-50 disabled:opacity-40">
                                        <span x-text="t('addToCart')"></span>
                                    </button>
                                    <button type="button" @click="openBookingModalForProduct($el)" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}" data-product-price="{{ $product->effective_price }}" class="flex h-8 w-9 items-center justify-center rounded-lg bg-blue-700 text-white transition hover:bg-blue-800" aria-label="Booking {{ $product->name }}">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 3v3m8-3v3M4 9h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1zM8 13h3m-3 4h3m3-4h2"/></svg>
                                    </button>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
                            <p class="text-lg font-bold text-slate-900">Produk tidak ditemukan</p>
                            <p class="mt-2 text-sm text-slate-500">Coba ubah kata pencarian atau filter yang dipilih.</p>
                        </div>
                    @endforelse
                    </div>
                </div>
                <div class="mt-8 flex justify-center">
                    {{ $products->onEachSide(1)->links() }}
                </div>
                </form>
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
    <x-auth-required-modal />

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
                <fieldset class="space-y-2 rounded-xl border border-white/10 bg-white/[0.03] p-4">
                    <legend class="px-1 text-xs font-bold uppercase text-slate-300" x-text="t('chooseProduct')"></legend>
                    <p x-show="bookingItems.length === 0" class="text-sm text-slate-400" x-text="t('serviceOnly')"></p>
                    <template x-for="(item, index) in bookingItems" :key="item.id">
                        <div class="flex items-start justify-between gap-4 text-sm">
                            <input type="hidden" :name="`products[${index}][id]`" :value="item.id">
                            <input type="hidden" :name="`products[${index}][quantity]`" :value="item.quantity">
                            <span class="min-w-0 text-slate-200"><span x-text="item.name"></span><span class="ml-1 text-slate-400">× <span x-text="item.quantity"></span></span></span>
                            <span class="shrink-0 font-bold text-yellow-200">Rp <span x-text="formatCurrency(item.price * item.quantity)"></span></span>
                        </div>
                    </template>
                    <div x-show="bookingItems.length > 0" class="flex justify-between border-t border-white/10 pt-2 text-sm font-black text-white">
                        <span>Total produk</span><span>Rp <span x-text="formatCurrency(bookingItems.reduce((total, item) => total + item.price * item.quantity, 0))"></span></span>
                    </div>
                </fieldset>
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

    <button type="button" @click="openBookingModal()" class="support-float fixed bottom-5 right-5 z-30 flex items-center gap-3 rounded-full border border-white/15 bg-slate-900/80 px-4 py-3 shadow-xl backdrop-blur-xl transition hover:-translate-y-1 hover:border-lime-300/40 sm:bottom-7 sm:right-7">
        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-lime-300/10 text-lime-300">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path stroke-linecap="round" d="M16 3v4M8 3v4M3 10h18m-12 4h3m-3 3h6"/></svg>
        </span>
        <span class="hidden text-left sm:block"><span class="block text-[10px] font-semibold text-slate-400">Butuh bantuan?</span><span class="text-xs font-extrabold text-white">Booking workshop</span></span>
    </button>
</body>
</html>
