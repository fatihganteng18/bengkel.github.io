<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#090d13">
    <title>{{ $product->name }} | Arisa Knalpot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body
    class="min-h-screen bg-[#090d13] font-sans text-slate-100 antialiased"
    x-data="planetBanApp"
    data-authenticated="{{ Auth::check() ? 'true' : 'false' }}"
    data-login-url="{{ route('login') }}"
    data-register-url="{{ route('register') }}"
    x-init="@if($errors->any()) bookingModalOpen = true; bookingItems = @js($bookingItems); selectedStoreId = @js((string) old('store_id', '')) @endif"
    x-effect="document.body.classList.toggle('overflow-hidden', cartDrawerOpen || bookingModalOpen)"
    @keydown.escape.window="closeOverlays()"
>
    @if(session('success'))
        <div role="status" class="relative z-[80] flex items-center justify-center gap-3 bg-emerald-500 px-4 py-3 text-center text-sm font-bold text-white">
            <span>{{ session('success') }}</span>
            <button type="button" @click="$el.parentElement.remove()" class="text-xl leading-none" aria-label="Tutup notifikasi">&times;</button>
        </div>
    @endif

    <header class="relative z-40 border-b border-white/10 bg-[#090d13]/90 shadow-sm backdrop-blur-xl">
        <div class="mx-auto flex h-[72px] max-w-7xl items-center justify-between gap-5 px-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2" aria-label="Arisa Knalpot, beranda">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-lime-300 text-lg font-black italic text-slate-950 shadow-sm">E</span>
                <span class="text-lg font-black italic tracking-tight text-white">Arisa<span class="text-lime-300"> Knalpot</span></span>
            </a>
            <nav class="hidden items-center gap-7 text-sm font-semibold text-slate-600 lg:flex" aria-label="Navigasi utama">
                <a class="detail-nav-link" href="{{ route('home') }}#layanan">Layanan</a>
                <a class="detail-nav-link" href="{{ route('home') }}#katalog">Knalpot & produk</a>
                <a class="detail-nav-link" href="{{ route('stores.index') }}">Lokasi outlet</a>
                <a class="detail-nav-link" href="{{ route('booking.status') }}">Cek booking</a>
            </nav>
            <div class="flex items-center gap-2">
                @include('components.account-menu')
                <button
                    type="button"
                    @click="cartDrawerOpen = true"
                    class="relative flex h-10 items-center gap-2 rounded-full bg-blue-950 px-4 text-sm font-bold text-white transition hover:bg-blue-800 active:scale-95"
                    :aria-label="'Keranjang, ' + cartCount + ' produk'"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.2 11.1a2 2 0 002 1.6h8.7a2 2 0 001.9-1.4L22 8H6M10 20a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/></svg>
                    <span class="hidden sm:inline">Keranjang</span>
                    <span x-show="cartCount > 0" x-text="cartCount" x-transition class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-yellow-300 px-1 text-[10px] font-black text-blue-950"></span>
                </button>
            </div>
        </div>
    </header>

    <main class="mx-auto w-full max-w-7xl flex-1 px-4 pb-20 pt-5 sm:px-6 lg:px-8 lg:pt-8">
        <nav class="mb-6 flex flex-wrap items-center gap-2 text-xs text-slate-500" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="transition hover:text-cyan-300">Home</a><span aria-hidden="true">/</span>
            <a href="{{ route('home') }}#katalog" class="transition hover:text-cyan-300">Katalog knalpot</a><span aria-hidden="true">/</span>
            <span class="font-semibold uppercase text-slate-400">{{ $product->sub_category ?? $product->category_label }}</span><span aria-hidden="true">/</span>
            <span class="max-w-[18rem] truncate font-bold uppercase text-white" aria-current="page">{{ $product->name }}</span>
        </nav>

        <section class="grid items-start gap-8 lg:grid-cols-[minmax(0,1.04fr)_minmax(360px,.96fr)] lg:gap-12">
            <div x-data="productGallery(@js($galleryImages))" class="min-w-0">
                <div
                    class="product-gallery-stage group relative flex h-[340px] items-center justify-center overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white p-7 shadow-sm sm:h-[500px] sm:p-10"
                    @mousemove="trackZoom($event)"
                    @mouseenter="zooming = true"
                    @mouseleave="zooming = false"
                    @keydown.arrow-left.prevent="move(-1)"
                    @keydown.arrow-right.prevent="move(1)"
                    tabindex="0"
                    aria-label="Galeri foto produk. Gunakan tombol panah kiri atau kanan."
                >
                    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_50%_45%,rgba(191,219,254,.35),transparent_58%)]"></div>
                    <img
                        :src="activeImage"
                        alt="{{ $product->name }}"
                        onerror="this.onerror=null;this.src='{{ asset('images/products/exhaust-sample.svg') }}'"
                        class="product-gallery-image relative z-10 h-full w-full object-contain drop-shadow-[0_24px_28px_rgba(15,23,42,.16)]"
                        :class="zooming && 'is-zooming'"
                        :style="zooming ? `transform: scale(1.38); transform-origin: ${zoomX}% ${zoomY}%` : ''"
                        fetchpriority="high"
                    >
                    @if($product->has_discount)
                        <span class="absolute left-5 top-5 z-20 rounded-full bg-rose-600 px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-white shadow-sm">Hemat {{ $product->discount_percentage }}%</span>
                    @endif
                    <span class="absolute bottom-5 right-5 z-20 rounded-full border border-white/80 bg-white/85 px-3 py-1.5 text-[10px] font-bold text-slate-600 shadow-sm backdrop-blur">Geser kursor untuk zoom</span>

                    <button
                        type="button"
                        @click="move(-1)"
                        :disabled="images.length < 2"
                        class="gallery-arrow absolute left-4 top-1/2 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white/90 text-slate-700 shadow-md transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-900 active:scale-95 disabled:cursor-not-allowed disabled:opacity-40"
                        aria-label="Foto sebelumnya"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <button
                        type="button"
                        @click="move(1)"
                        :disabled="images.length < 2"
                        class="gallery-arrow absolute right-4 top-1/2 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white/90 text-slate-700 shadow-md transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-900 active:scale-95 disabled:cursor-not-allowed disabled:opacity-40"
                        aria-label="Foto berikutnya"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/></svg>
                    </button>
                </div>

                <div class="mt-4 flex gap-3 overflow-x-auto pb-2" aria-label="Pilih foto produk">
                    <template x-for="(image, index) in images" :key="image">
                        <button
                            type="button"
                            @click="select(index)"
                            class="gallery-thumbnail relative h-[76px] w-[76px] shrink-0 overflow-hidden rounded-xl border-2 bg-white p-2 transition hover:border-blue-300 sm:h-[88px] sm:w-[88px]"
                            :class="activeIndex === index ? 'border-blue-900 ring-2 ring-blue-100' : 'border-slate-200'"
                            :aria-label="'Pilih foto ' + (index + 1)"
                            :aria-pressed="activeIndex === index"
                        >
                            <img :src="image" alt="" onerror="this.onerror=null;this.src='{{ asset('images/products/exhaust-sample.svg') }}'" class="h-full w-full object-contain">
                        </button>
                    </template>
                    @if($galleryImages->count() <= 1)
                        <p class="self-center pl-1 text-[11px] leading-5 text-slate-500">Foto tambahan produk dapat diatur melalui data galeri produk.</p>
                    @endif
                </div>
            </div>

            <div class="product-detail-panel rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm sm:p-7 lg:p-8">
                <div class="mb-4 flex flex-wrap items-center gap-2">
                    <span class="rounded-full bg-cyan-300/10 px-3 py-1.5 text-[10px] font-black uppercase tracking-[.14em] text-cyan-200">{{ $product->sub_category ?? $product->category_label }}</span>
                    @if($product->is_low_stock)
                        <span class="rounded-full bg-amber-50 px-3 py-1.5 text-[10px] font-extrabold text-amber-800">Stok terbatas</span>
                    @endif
                </div>

                <h1 class="text-2xl font-black leading-tight tracking-tight text-slate-950 sm:text-3xl">{{ $product->name }}</h1>
                <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-2 text-xs text-slate-500">
                    <span class="inline-flex items-center gap-1.5"><span class="text-amber-400" aria-hidden="true">★★★★★</span><span>Belum ada ulasan</span></span>
                    <span class="hidden text-slate-300 sm:inline" aria-hidden="true">•</span>
                    <span>{{ number_format($soldCount) }} terjual</span>
                    <span class="hidden text-slate-300 sm:inline" aria-hidden="true">•</span>
                    <span>SKU: <strong class="font-bold text-slate-200">{{ $product->sku ?? 'EXH-' . str_pad((string) $product->id, 5, '0', STR_PAD_LEFT) }}</strong></span>
                </div>

                <div class="my-6 border-t border-slate-100"></div>

                <div class="flex flex-wrap items-end gap-x-3 gap-y-2">
                    <p class="text-3xl font-black tracking-tight text-white sm:text-4xl">Rp {{ number_format($product->effective_price, 0, ',', '.') }}</p>
                    @if($product->has_discount)
                        <p class="pb-1 text-sm font-semibold text-slate-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        <span class="mb-1 rounded-full bg-rose-50 px-2.5 py-1 text-[10px] font-black text-rose-700">Hemat {{ $product->discount_percentage }}%</span>
                    @endif
                </div>
                <p class="mt-2 text-xs text-slate-500">Harga dan ketersediaan dikonfirmasi kembali oleh outlet pilihan Anda.</p>

                <div class="mt-7">
                    <h2 class="mb-3 text-xs font-black uppercase tracking-[.15em] text-slate-500">Spesifikasi produk</h2>
                    <dl class="grid grid-cols-2 gap-2.5 sm:grid-cols-3">
                        @if($product->category === 'exhaust')
                            <div class="spec-tile"><dt>Inlet / diameter</dt><dd>{{ $product->inlet_size }}</dd></div>
                            <div class="spec-tile"><dt>Karakter suara</dt><dd>{{ $product->sound_type }}</dd></div>
                            <div class="spec-tile"><dt>Material</dt><dd>{{ $product->material }}</dd></div>
                            <div class="spec-tile"><dt>Kapasitas mesin</dt><dd>{{ $product->engine_cc }}</dd></div>
                            <div class="spec-tile"><dt>Subkategori</dt><dd>{{ $product->sub_category }}</dd></div>
                        @else
                            <div class="spec-tile"><dt>Ukuran ban</dt><dd>{{ $tireSize ?? 'Lihat deskripsi' }}</dd></div>
                            <div class="spec-tile"><dt>Posisi ban</dt><dd>Depan / belakang</dd></div>
                            <div class="spec-tile"><dt>Diameter velg</dt><dd>{{ $tireSize ? substr($tireSize, strrpos($tireSize, '-') + 1) . '"' : '—' }}</dd></div>
                            <div class="spec-tile"><dt>Tipe</dt><dd>{{ str_contains(strtolower($product->name), 'tubeless') ? 'Tubeless' : '—' }}</dd></div>
                            <div class="spec-tile"><dt>Kategori</dt><dd>{{ $product->category_label }}</dd></div>
                        @endif
                        <div class="spec-tile"><dt>Ketersediaan</dt><dd class="{{ $product->stock > 0 ? 'text-emerald-700' : 'text-rose-700' }}">{{ $product->stock > 0 ? $product->stock . ' pcs' : 'Stok habis' }}</dd></div>
                    </dl>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <button
                        type="button"
                        @click="openBookingModalForProduct($el)"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}"
                        data-product-price="{{ $product->effective_price }}"
                        class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl border-2 border-blue-950 px-4 py-3 text-sm font-extrabold text-blue-950 transition hover:bg-blue-50 active:scale-[.97]"
                    >
                        Beli Langsung
                        <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
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
                        class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-blue-950 px-4 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-blue-800 hover:shadow-md active:scale-[.97] disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l2.2 11.1a2 2 0 002 1.6h8.7a2 2 0 001.9-1.4L22 8H6M10 20a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/></svg>
                        + Keranjang
                    </button>
                </div>
                <div class="mt-5 flex flex-wrap gap-x-5 gap-y-2 border-t border-slate-100 pt-4 text-[11px] font-semibold text-slate-500">
                    <span class="inline-flex items-center gap-1.5"><span class="text-emerald-600">✓</span> Produk original</span>
                    <span class="inline-flex items-center gap-1.5"><span class="text-emerald-600">✓</span> Bisa dipasang di outlet</span>
                    <span class="inline-flex items-center gap-1.5"><span class="text-emerald-600">✓</span> Bantuan mekanik</span>
                </div>
            </div>
        </section>

        <section x-data="{ activeTab: 'compatibility', fitmentOpen: true }" class="detail-extra-section mt-10 overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-sm sm:mt-14">
            <div class="flex border-b border-slate-200 px-4 sm:px-7">
                <button type="button" @click="activeTab = 'compatibility'" :aria-selected="activeTab === 'compatibility'" class="detail-tab" :class="activeTab === 'compatibility' && 'detail-tab-active'">{{ $product->category === 'exhaust' ? 'Fitment & sound setup' : 'Kompatibilitas' }}</button>
                <button type="button" @click="activeTab = 'reviews'" :aria-selected="activeTab === 'reviews'" class="detail-tab" :class="activeTab === 'reviews' && 'detail-tab-active'">Ulasan Produk <span class="ml-1 rounded-full bg-slate-100 px-1.5 py-0.5 text-[9px] text-slate-500">0</span></button>
            </div>

            <div x-show="activeTab === 'compatibility'" x-transition class="p-5 sm:p-7">
                @if($product->category === 'exhaust')
                    <div class="rounded-xl border border-cyan-300/20 bg-cyan-300/[0.06] p-5">
                        <p class="text-sm font-extrabold text-white">Setup dibuat sesuai diameter inlet dan kapasitas mesin.</p>
                        <p class="mt-2 text-xs leading-6 text-slate-300">Konfirmasi ukuran jalur knalpot dan ruang pemasangan mobil sebelum checkout. Workshop dapat bantu konsultasi karakter suara {{ strtolower($product->sound_type ?? 'custom') }} agar tetap nyaman untuk daily drive.</p>
                        <p class="mt-3 text-xs font-bold text-lime-300">TIG Argon precision · Daily Drive Approved · Weekend ready</p>
                    </div>
                @else
                <button type="button" @click="fitmentOpen = !fitmentOpen" :aria-expanded="fitmentOpen" class="flex w-full items-center justify-between gap-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-4 text-left transition hover:border-blue-200 hover:bg-blue-50/50">
                    <span><span class="block text-sm font-extrabold text-slate-900">Ban ini cocok untuk motor</span><span class="mt-1 block text-xs text-slate-500">{{ $tireSize ? 'Ukuran acuan: ' . $tireSize : 'Konfirmasi ukuran sebelum pemasangan' }}</span></span>
                    <svg class="h-5 w-5 shrink-0 text-slate-500 transition-transform" :class="fitmentOpen && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                </button>
                <div x-show="fitmentOpen" x-transition class="pt-4">
                    @if($compatibleMotorcycles->isNotEmpty())
                        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($compatibleMotorcycles as $motorcycle)
                                <article class="fitment-card flex items-center gap-3 rounded-xl border border-slate-200 p-4">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-cyan-300/10 text-cyan-200"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><circle cx="6" cy="17" r="3"/><circle cx="18" cy="17" r="3"/><path stroke-linecap="round" stroke-linejoin="round" d="m6 17 4-7h4l4 7m-8-7-2-3h4m-3 5h6"/></svg></span>
                                    <span><strong class="block text-sm text-slate-800">{{ $motorcycle->brand }}</strong><span class="mt-1 block text-xs text-slate-500">{{ $motorcycle->model_name }}</span></span>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5">
                            <p class="text-sm font-bold text-slate-800">Periksa ukuran ban motor sebelum memilih.</p>
                            <p class="mt-1 text-xs leading-5 text-slate-500">Data motor yang cocok belum tersedia untuk ukuran {{ $tireSize ?? 'produk ini' }}. Contoh merek untuk diverifikasi: Piaggio dan Vespa. Pastikan ukuran ban depan/belakang sesuai dengan rekomendasi pabrikan.</p>
                        </div>
                    @endif
                    <p class="mt-4 text-[11px] leading-5 text-slate-500">Kecocokan bergantung pada ukuran ban dan posisi yang direkomendasikan untuk motor Anda. Konfirmasikan kembali dengan mekanik sebelum pemasangan.</p>
                </div>
                @endif
            </div>

            <div x-cloak x-show="activeTab === 'reviews'" x-transition class="p-5 sm:p-7">
                <div class="rounded-2xl bg-slate-50 px-6 py-10 text-center">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white text-amber-400 shadow-sm" aria-hidden="true">★</span>
                    <h2 class="mt-4 text-sm font-extrabold text-slate-900">Belum ada ulasan</h2>
                    <p class="mx-auto mt-2 max-w-md text-xs leading-5 text-slate-500">Jadilah pelanggan pertama yang berbagi pengalaman setelah mencoba produk ini.</p>
                </div>
            </div>
        </section>

        <section class="detail-extra-section mt-8 rounded-[1.75rem] border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
            <div class="grid gap-6 md:grid-cols-[1fr_auto] md:items-start">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[.18em] text-blue-800">Tentang produk</p>
                    <h2 class="mt-2 text-xl font-black text-slate-950">Deskripsi & fitur</h2>
                    <p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600">{{ $product->description ?: 'Produk knalpot untuk meningkatkan karakter suara dan performa kendaraan. Pastikan ukuran serta fitment sesuai spesifikasi mobil Anda.' }}</p>
                    <ul class="mt-5 grid gap-3 text-xs leading-5 text-slate-600 sm:grid-cols-2">
                        <li class="flex gap-2"><span class="font-black text-lime-300">✓</span><span>Material {{ $product->material ?? 'pilihan' }} dan welding TIG Argon untuk hasil sambungan presisi.</span></li>
                        <li class="flex gap-2"><span class="font-black text-lime-300">✓</span><span>Karakter suara {{ $product->sound_type ?? 'custom' }} untuk menyesuaikan gaya berkendara.</span></li>
                        <li class="flex gap-2"><span class="font-black text-lime-300">✓</span><span>Pastikan inlet {{ $product->inlet_size ?? 'sesuai spesifikasi' }} cocok dengan jalur knalpot kendaraan.</span></li>
                        <li class="flex gap-2"><span class="font-black text-lime-300">✓</span><span>Konsultasi fitment dan opsi pemasangan tersedia melalui workshop Arisa Knalpot.</span></li>
                    </ul>
                </div>
                <div class="rounded-2xl border border-blue-100 bg-blue-50/70 p-4 md:min-w-56">
                    <p class="text-[10px] font-black uppercase tracking-wider text-blue-800">Standar produk</p>
                    <p class="mt-2 text-sm font-extrabold text-slate-900">Workshop ready</p>
                    <p class="mt-1 text-xs leading-5 text-slate-600">Daily drive approved. Konsultasikan setup dan kapasitas mesin sebelum pemasangan.</p>
                </div>
            </div>
        </section>

        @if($relatedProducts->isNotEmpty())
            <section class="mt-14">
                <div class="mb-5 flex items-end justify-between gap-4">
                    <div><p class="text-[10px] font-black uppercase tracking-[.18em] text-blue-800">Jelajahi katalog</p><h2 class="mt-2 text-xl font-black text-slate-950">Produk sejenis</h2></div>
                    <a href="{{ route('home') }}#katalog" class="text-xs font-bold text-blue-900 transition hover:text-blue-600">Lihat katalog →</a>
                </div>
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($relatedProducts as $relatedProduct)
                        <article class="related-product-card rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <div class="flex h-36 items-center justify-center overflow-hidden rounded-xl bg-slate-50 p-4">
                                @if($relatedProduct->image)
                                    <img src="{{ $relatedProduct->image }}" alt="{{ $relatedProduct->name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ asset('images/products/exhaust-sample.svg') }}'" class="h-full w-full object-contain">
                                @else
                                    <span class="text-5xl" aria-hidden="true">⌁</span>
                                @endif
                            </div>
                            <span class="mt-4 block text-[9px] font-black uppercase tracking-wider text-cyan-300">{{ $relatedProduct->sub_category ?? $relatedProduct->category_label }}</span>
                            <h3 class="mt-1 line-clamp-2 min-h-10 text-sm font-extrabold text-slate-900">{{ $relatedProduct->name }}</h3>
                            <div class="mt-3 flex items-center justify-between gap-2">
                                <span class="text-sm font-black text-blue-950">Rp {{ number_format($relatedProduct->effective_price, 0, ',', '.') }}</span>
                                <a href="{{ route('product.show', $relatedProduct->id) }}" class="rounded-lg bg-blue-50 px-3 py-2 text-[10px] font-bold text-blue-900 transition hover:bg-blue-100">Detail</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </main>

    <x-cart-drawer />
    <x-auth-required-modal />

    <div x-cloak x-show="bookingModalOpen" x-transition.opacity @click.self="closeOverlays()" class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" style="display: none;">
        <section
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-y-4 scale-95 opacity-0"
            x-transition:enter-end="translate-y-0 scale-100 opacity-100"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-y-0 scale-100 opacity-100"
            x-transition:leave-end="translate-y-4 scale-95 opacity-0"
            class="relative max-h-[92vh] w-full max-w-xl overflow-y-auto rounded-3xl bg-white p-6 shadow-2xl sm:p-8"
            role="dialog"
            aria-modal="true"
            aria-labelledby="product-booking-title"
        >
            <button type="button" @click="closeOverlays()" :disabled="isLoading" class="absolute right-5 top-5 flex h-9 w-9 items-center justify-center rounded-full text-xl text-slate-400 transition hover:bg-slate-100 hover:text-slate-800 disabled:opacity-50" aria-label="Tutup form booking">&times;</button>
            <p class="text-[10px] font-black uppercase tracking-[.18em] text-blue-800">Atur jadwal kunjungan</p>
            <h2 id="product-booking-title" class="mt-2 pr-8 text-2xl font-black text-slate-950">Booking produk & servis</h2>
            <p class="mb-6 mt-2 text-sm text-slate-500">Pilih outlet dan waktu yang paling nyaman untuk Anda.</p>

            <form action="{{ route('booking.store') }}" method="POST" @submit.prevent="submitForm($event)" class="space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <label class="block text-xs font-bold text-slate-700">Nama lengkap<input type="text" name="customer_name" value="{{ old('customer_name', Auth::user()?->name) }}" required maxlength="255" placeholder="Nama Anda" class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-700 focus:ring-4 focus:ring-blue-900/10"></label>
                    <label class="block text-xs font-bold text-slate-700">No. WhatsApp / HP<input type="tel" name="customer_phone" value="{{ old('customer_phone', Auth::user()?->phone) }}" required maxlength="20" placeholder="081234567890" class="mt-1.5 w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-blue-700 focus:ring-4 focus:ring-blue-900/10"></label>
                </div>
                <label class="block text-xs font-bold text-slate-700">Pilih outlet cabang
                    <select name="store_id" x-model="selectedStoreId" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-blue-700 focus:ring-4 focus:ring-blue-900/10">
                        <option value="">Pilih lokasi outlet</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }} ({{ $store->city }})</option>
                        @endforeach
                    </select>
                </label>
                <fieldset class="space-y-2 rounded-xl border border-slate-200 bg-slate-50 p-4">
                    <legend class="px-1 text-xs font-bold uppercase text-slate-700">Ringkasan produk</legend>
                    <template x-for="(item, index) in bookingItems" :key="item.id">
                        <div class="flex items-start justify-between gap-4 text-sm">
                            <input type="hidden" :name="`products[${index}][id]`" :value="item.id">
                            <input type="hidden" :name="`products[${index}][quantity]`" :value="item.quantity">
                            <span class="min-w-0 text-slate-700"><span x-text="item.name"></span><span class="ml-1 text-slate-500">× <span x-text="item.quantity"></span></span></span>
                            <span class="shrink-0 font-bold text-slate-900">Rp <span x-text="formatCurrency(item.price * item.quantity)"></span></span>
                        </div>
                    </template>
                </fieldset>
                <div class="grid grid-cols-2 gap-3">
                    <label class="block text-xs font-bold text-slate-700">Tanggal kunjungan<input type="date" name="booking_date" required min="{{ date('Y-m-d') }}" value="{{ old('booking_date') }}" class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm outline-none focus:border-blue-700 focus:ring-4 focus:ring-blue-900/10"></label>
                    <label class="block text-xs font-bold text-slate-700">Jam kunjungan
                        <select name="booking_time" required class="mt-1.5 w-full rounded-xl border border-slate-200 bg-white px-3 py-3 text-sm outline-none focus:border-blue-700 focus:ring-4 focus:ring-blue-900/10">
                            @foreach(['09:00', '11:00', '13:00', '15:00', '17:00'] as $time)
                                <option value="{{ $time }}" @selected(old('booking_time', '09:00') === $time)>{{ $time }} WIB</option>
                            @endforeach
                        </select>
                    </label>
                </div>
                @include('components.payment-method-options')
                @if($errors->any())
                    <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                        <p class="font-bold">Periksa kembali data booking.</p>
                        <ul class="mt-1 list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                <div class="flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <button type="button" @click="closeOverlays()" :disabled="isLoading" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-bold text-slate-600 transition hover:bg-slate-50 disabled:opacity-50">Batal</button>
                    <button type="submit" :disabled="isLoading" class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-950 px-5 py-3 text-sm font-extrabold text-white transition hover:bg-blue-800 active:scale-95 disabled:cursor-wait disabled:opacity-60">
                        <svg x-show="isLoading" class="h-4 w-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 4 0 5.373 0 12h4z"></path></svg>
                        <span x-text="isLoading ? 'Mengirim...' : 'Konfirmasi booking'">Konfirmasi booking</span>
                    </button>
                </div>
            </form>
        </section>
    </div>

    @include('components.site-footer')
</body>
</html>
