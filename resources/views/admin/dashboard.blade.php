<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Planet Ban</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <div x-data="adminDashboard" class="min-h-screen">
        <header class="bg-blue-950 text-white shadow-lg">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-5 sm:px-6 lg:px-8">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-blue-200">Planet Ban</p>
                    <h1 class="mt-1 text-xl font-black sm:text-2xl">Dashboard Admin</h1>
                </div>
                <div class="flex items-center gap-2 sm:gap-4">
                    <a href="{{ route('admin.bookings') }}" class="rounded-xl bg-white/10 px-3 py-2 text-sm font-bold transition hover:bg-white/20 sm:px-4">
                        Kelola booking
                    </a>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-xl bg-yellow-400 px-3 py-2 text-sm font-bold text-blue-950 transition hover:bg-yellow-300 sm:px-4">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
            @if (session('success'))
                <div role="status" class="rounded-2xl border border-green-200 bg-green-50 px-5 py-4 font-semibold text-green-800">
                    {{ session('success') }}
                </div>
            @endif

            <section class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">Total booking</p>
                    <p class="mt-3 text-3xl font-black text-blue-950">{{ number_format($totalBookings) }}</p>
                    <p class="mt-1 text-sm text-slate-500">Seluruh jadwal servis dan pemesanan.</p>
                </article>
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">Menunggu konfirmasi</p>
                    <p class="mt-3 text-3xl font-black text-amber-600">{{ number_format($pendingBookings) }}</p>
                    <a href="{{ route('admin.bookings') }}" class="mt-2 inline-flex text-sm font-bold text-blue-900 hover:underline">Tinjau booking &rarr;</a>
                </article>
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">Pembayaran menunggu</p>
                    <p class="mt-3 text-3xl font-black text-amber-600">{{ number_format($pendingPayments) }}</p>
                    <p class="mt-1 text-sm text-slate-500">Belum dikonfirmasi sebagai lunas.</p>
                </article>
                <article class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-semibold text-slate-500">Produk stok rendah</p>
                    <p class="mt-3 text-3xl font-black text-red-600">{{ number_format($lowStockCount) }}</p>
                    <p class="mt-1 text-sm text-slate-500">Stok {{ $lowStockThreshold }} unit atau kurang.</p>
                </article>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <div class="mb-5">
                    <h2 class="text-lg font-black text-blue-950 sm:text-xl">Tren transaksi booking enam bulan terakhir</h2>
                    <p class="mt-1 text-sm text-slate-500">Grafik menghitung permintaan pembayaran yang tercatat, bukan pembayaran yang sudah lunas.</p>
                </div>
                <div class="h-72 sm:h-80">
                    <canvas
                        x-ref="bookingChart"
                        data-chart="{{ json_encode(['labels' => $chartLabels, 'transactions' => $transactionCounts], JSON_HEX_APOS | JSON_HEX_QUOT) }}"
                        aria-label="Grafik jumlah transaksi booking yang tercatat per bulan"
                        role="img"></canvas>
                </div>
            </section>

            <section class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                    <div class="mb-5">
                        <h2 class="text-lg font-black text-blue-950">Produk & layanan terlaris</h2>
                        <p class="mt-1 text-sm text-slate-500">Diurutkan dari jumlah booking terkait produk.</p>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($bestSellers as $product)
                            <div class="flex items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                                <div class="min-w-0">
                                    <p class="truncate font-bold text-slate-800">{{ $product->name }}</p>
                                    <p class="mt-1 text-xs capitalize text-slate-500">{{ $product->category }}</p>
                                </div>
                                <span class="shrink-0 rounded-full bg-blue-50 px-3 py-1 text-sm font-black text-blue-900">
                                    {{ number_format($product->bookings_count) }} booking
                                </span>
                            </div>
                        @empty
                            <p class="py-4 text-sm text-slate-500">Belum ada booking yang terkait dengan produk.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-3xl border border-red-100 bg-white p-5 shadow-sm sm:p-7">
                    <div class="mb-5">
                        <h2 class="text-lg font-black text-blue-950">Peringatan stok rendah</h2>
                        <p class="mt-1 text-sm text-slate-500">Produk dengan persediaan {{ $lowStockThreshold }} unit atau kurang.</p>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($lowStockProducts as $product)
                            <div class="flex items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                                <div class="min-w-0">
                                    <p class="truncate font-bold text-slate-800">{{ $product->name }}</p>
                                    <p class="mt-1 text-xs capitalize text-slate-500">{{ $product->category }}</p>
                                </div>
                                <span class="shrink-0 rounded-full bg-red-50 px-3 py-1 text-sm font-black text-red-700">
                                    {{ number_format($product->stock) }} tersisa
                                </span>
                            </div>
                        @empty
                            <p class="py-4 text-sm text-slate-500">Tidak ada produk dengan stok rendah.</p>
                        @endforelse
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
