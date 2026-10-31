<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>Dashboard Admin | Planet Ban</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <div x-data="adminDashboard" class="min-h-screen lg:grid lg:grid-cols-[260px_minmax(0,1fr)]">
        <aside class="flex flex-col bg-slate-950 text-white lg:min-h-screen">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 border-b border-white/10 px-6 py-6">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-yellow-400 text-lg font-black italic text-slate-950">P</span>
                <span><span class="block text-sm font-black italic tracking-wide">PLANET<span class="text-yellow-400">BAN</span></span><span class="mt-1 block text-[10px] font-bold uppercase tracking-[.2em] text-slate-400">Admin workspace</span></span>
            </a>

            <nav class="flex gap-2 overflow-x-auto px-4 py-4 lg:flex-col lg:overflow-visible lg:px-3" aria-label="Navigasi admin">
                <a href="{{ route('admin.dashboard') }}" aria-current="page" class="flex shrink-0 items-center gap-3 rounded-xl bg-cyan-300/10 px-4 py-3 text-sm font-bold text-cyan-200 ring-1 ring-cyan-300/10">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="5" rx="1.5"/><rect x="13" y="10" width="8" height="11" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/></svg>
                    Ringkasan
                </a>
                <a href="{{ route('admin.bookings') }}" class="flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-slate-300 transition hover:bg-white/[0.06] hover:text-white">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 3v3m8-3v3M4 9h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z"/><path stroke-linecap="round" d="m9 14 2 2 4-4"/></svg>
                    Kelola booking
                </a>
                <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold text-slate-300 transition hover:bg-white/[0.06] hover:text-white">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 4l9 6.5v9a.5.5 0 01-.5.5h-17a.5.5 0 01-.5-.5v-9z"/><path stroke-linecap="round" d="M9 20v-6h6v6"/></svg>
                    Lihat toko
                </a>
            </nav>

            <div class="mt-auto hidden border-t border-white/10 p-4 lg:block">
                <div class="flex items-center gap-3 rounded-xl bg-white/[0.04] p-3">
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-800 text-sm font-black text-yellow-300">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    <span class="min-w-0 flex-1"><span class="block truncate text-xs font-bold text-white">{{ Auth::user()->name }}</span><span class="mt-1 block text-[10px] uppercase tracking-wider text-slate-400">Administrator</span></span>
                </div>
            </div>
        </aside>

        <div class="min-w-0">
            <header class="flex items-center justify-between gap-4 border-b border-slate-200 bg-white px-4 py-4 sm:px-7 lg:px-9">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[.2em] text-cyan-700">Planet Ban · Analytics</p>
                    <h1 class="mt-1 text-xl font-black tracking-tight text-slate-950 sm:text-2xl">Dashboard Admin</h1>
                </div>
                <div class="flex items-center gap-2 sm:gap-4">
                    <span class="hidden text-right sm:block"><span class="block text-xs font-bold text-slate-800">{{ Auth::user()->name }}</span><span class="mt-1 block text-[10px] text-slate-500">Administrator</span></span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-xl bg-slate-950 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800 active:scale-95">Keluar</button>
                    </form>
                </div>
            </header>

            <main class="mx-auto max-w-[1600px] space-y-6 p-4 sm:p-7 lg:p-9">
                @if (session('success'))
                    <div role="status" class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 font-semibold text-green-800">{{ session('success') }}</div>
                @endif

                <section class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-4" aria-label="Ringkasan metrik">
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <p class="text-xs font-bold text-slate-500">Total booking</p>
                        <p class="mt-3 text-3xl font-black text-slate-950">{{ number_format($totalBookings) }}</p>
                        <p class="mt-1 text-xs text-slate-500">Seluruh jadwal servis dan pemesanan.</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <p class="text-xs font-bold text-slate-500">Menunggu konfirmasi</p>
                        <p class="mt-3 text-3xl font-black text-amber-600">{{ number_format($pendingBookings) }}</p>
                        <a href="{{ route('admin.bookings') }}" class="mt-2 inline-flex text-xs font-bold text-cyan-800 hover:underline">Tinjau booking →</a>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <p class="text-xs font-bold text-slate-500">Pembayaran menunggu</p>
                        <p class="mt-3 text-3xl font-black text-amber-600">{{ number_format($pendingPayments) }}</p>
                        <p class="mt-1 text-xs text-slate-500">Belum dikonfirmasi sebagai lunas.</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <p class="text-xs font-bold text-slate-500">Pendapatan terkonfirmasi · 6 bulan</p>
                        <p class="mt-3 text-2xl font-black text-emerald-700">Rp {{ number_format($revenueTotal, 0, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-slate-500">Akumulasi pembayaran berstatus lunas.</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                        <p class="text-xs font-bold text-slate-500">Produk stok rendah</p>
                        <p class="mt-3 text-3xl font-black text-rose-600">{{ number_format($lowStockCount) }}</p>
                        <p class="mt-1 text-xs text-slate-500">Stok {{ $lowStockThreshold }} unit atau kurang.</p>
                    </article>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    <div class="mb-5 flex flex-col justify-between gap-2 sm:flex-row sm:items-end">
                        <div>
                            <h2 class="text-lg font-black text-slate-950 sm:text-xl">Tren transaksi booking enam bulan terakhir</h2>
                            <p class="mt-1 max-w-3xl text-xs leading-5 text-slate-500">Garis pendapatan hanya menjumlahkan pembayaran berstatus lunas. Garis transaksi menghitung permintaan pembayaran yang tercatat, bukan pembayaran yang sudah lunas.</p>
                        </div>
                        <span class="inline-flex w-fit items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-[10px] font-bold text-emerald-800"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Data diperbarui saat halaman dibuka</span>
                    </div>
                    <div class="h-72 sm:h-80">
                        <canvas
                            x-ref="salesChart"
                            data-chart="{{ json_encode(['labels' => $chartLabels, 'transactions' => $transactionCounts, 'revenue' => $monthlyRevenue], JSON_HEX_APOS | JSON_HEX_QUOT) }}"
                            aria-label="Grafik tren pendapatan pembayaran lunas dan jumlah transaksi tercatat per bulan"
                            role="img"></canvas>
                    </div>
                </section>

                <section class="grid gap-6 xl:grid-cols-2">
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                        <div class="mb-5">
                            <h2 class="text-lg font-black text-slate-950">Produk & layanan terlaris</h2>
                            <p class="mt-1 text-xs leading-5 text-slate-500">Berdasarkan booking produk yang telah lunas atau servis yang telah selesai.</p>
                        </div>
                        <div class="h-64">
                            <canvas
                                x-ref="bestSellersChart"
                                data-chart="{{ json_encode(['labels' => $bestSellers->pluck('name')->values(), 'values' => $bestSellers->pluck('bookings_count')->values()], JSON_HEX_APOS | JSON_HEX_QUOT) }}"
                                aria-label="Grafik lima produk dengan jumlah penjualan terkonfirmasi tertinggi"
                                role="img"></canvas>
                        </div>
                        <div class="mt-4 divide-y divide-slate-100">
                            @forelse ($bestSellers as $product)
                                <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                                    <div class="min-w-0"><p class="truncate text-sm font-bold text-slate-800">{{ $product->name }}</p><p class="mt-1 text-[10px] capitalize text-slate-500">{{ $product->category }}</p></div>
                                    <span class="shrink-0 rounded-full bg-cyan-50 px-3 py-1 text-xs font-black text-cyan-900">{{ number_format($product->bookings_count) }} booking</span>
                                </div>
                            @empty
                                <p class="py-3 text-xs text-slate-500">Belum ada transaksi produk yang lunas atau booking servis yang selesai.</p>
                            @endforelse
                        </div>
                    </article>

                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                        <div class="mb-5">
                            <h2 class="text-lg font-black text-slate-950">Sisa stok produk</h2>
                            <p class="mt-1 text-xs leading-5 text-slate-500">Delapan produk dengan stok paling rendah saat ini.</p>
                        </div>
                        <div class="h-64">
                            <canvas
                                x-ref="stockChart"
                                data-chart="{{ json_encode(['labels' => $stockProducts->pluck('name')->values(), 'values' => $stockProducts->pluck('stock')->values()], JSON_HEX_APOS | JSON_HEX_QUOT) }}"
                                aria-label="Grafik jumlah stok tersisa untuk produk dengan stok terendah"
                                role="img"></canvas>
                        </div>
                        <div class="mt-4 divide-y divide-slate-100">
                            @forelse ($lowStockProducts as $product)
                                <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                                    <div class="min-w-0"><p class="truncate text-sm font-bold text-slate-800">{{ $product->name }}</p><p class="mt-1 text-[10px] capitalize text-slate-500">{{ $product->category }}</p></div>
                                    <span class="shrink-0 rounded-full bg-rose-50 px-3 py-1 text-xs font-black text-rose-700">{{ number_format($product->stock) }} tersisa</span>
                                </div>
                            @empty
                                <p class="py-3 text-xs text-slate-500">Tidak ada produk dengan stok rendah.</p>
                            @endforelse
                        </div>
                    </article>
                </section>
            </main>
        </div>
    </div>
</body>
</html>
