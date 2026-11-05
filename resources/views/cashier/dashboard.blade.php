<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="20">
    <title>Panel Kasir | Arisa Knalpot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <header class="bg-slate-950 text-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 px-4 py-4 sm:px-6">
            <div>
                <p class="text-xs font-black uppercase tracking-[.2em] text-lime-300">ARISA KNALPOT · OPERATOR</p>
                <h1 class="mt-1 text-xl font-black sm:text-2xl">Panel Kasir & Antrean</h1>
            </div>
            <nav class="flex items-center gap-2 text-sm">
                <a href="{{ route('queues.display') }}" target="_blank" rel="noopener" class="rounded-xl border border-white/15 px-3 py-2 font-semibold transition hover:bg-white/10">Monitor antrean</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="rounded-xl bg-white/10 px-3 py-2 font-bold transition hover:bg-white/20">Keluar</button>
                </form>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-7xl space-y-6 px-4 py-7 sm:px-6 lg:py-9">
        @if (session('success'))
            <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-bold text-emerald-800">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div role="alert" class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-bold text-rose-800">
                <p>Periksa kembali perubahan yang dimasukkan.</p>
                <ul class="mt-2 list-inside list-disc font-medium">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <section class="grid gap-4 sm:grid-cols-3" aria-label="Ringkasan kasir">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Antrean menunggu / terjadwal</p>
                <p class="mt-2 text-3xl font-black text-amber-600">{{ $waitingCount }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Sedang dikerjakan</p>
                <p class="mt-2 text-3xl font-black text-blue-700">{{ $inServiceCount }}</p>
            </article>
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-bold text-slate-500">Pembayaran perlu konfirmasi</p>
                <p class="mt-2 text-3xl font-black text-rose-600">{{ $pendingPayments }}</p>
            </article>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="text-lg font-black">Mesin kasir · transaksi langsung</h2>
                <p class="mt-1 text-xs text-slate-500">Pilih barang dan jumlahnya. Stok akan dicek dan diperbarui saat transaksi disimpan.</p>
            </div>
            <form action="{{ route('cashir.pos.store') }}" method="POST" class="space-y-5 p-5 sm:p-6" x-data="cashierPos(@js($posProducts))">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <label class="text-xs font-bold text-slate-600">Nama pelanggan <span class="font-normal">(opsional)</span>
                        <input name="customer_name" value="{{ old('customer_name') }}" placeholder="Pelanggan umum" class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900">
                    </label>
                    <label class="text-xs font-bold text-slate-600">Nomor WhatsApp <span class="font-normal">(opsional)</span>
                        <input name="customer_phone" value="{{ old('customer_phone') }}" placeholder="08xxxxxxxxxx" class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900">
                    </label>
                    <label class="text-xs font-bold text-slate-600">Cabang
                        <select name="store_id" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900">
                            <option value="">Pilih cabang</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}" @selected(old('store_id') == $store->id)>{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div class="space-y-3">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="text-sm font-black">Barang belanja</h3>
                        <button type="button" @click="addItem()" :disabled="items.length >= products.length" class="rounded-lg border border-slate-300 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 disabled:opacity-50">+ Tambah barang</button>
                    </div>
                    <template x-for="(item, index) in items" :key="index">
                        <div class="grid gap-3 rounded-xl bg-slate-50 p-3 sm:grid-cols-[minmax(0,1fr)_120px_auto] sm:items-end">
                            <label class="text-[11px] font-bold text-slate-600">Produk
                                <select x-model="item.id" :name="'products[' + index + '][id]'" required class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900">
                                    <option value="">Pilih produk</option>
                                    <template x-for="product in availableProducts(index)" :key="product.id">
                                        <option :value="product.id" x-text="`${product.name} · ${product.category} · Rp ${formatCurrency(product.price)} · Stok ${product.stock}`"></option>
                                    </template>
                                </select>
                            </label>
                            <label class="text-[11px] font-bold text-slate-600">Jumlah
                                <input type="number" x-model.number="item.quantity" :name="'products[' + index + '][quantity]'" min="1" :max="productFor(item.id)?.stock || 999" required class="mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900">
                            </label>
                            <button type="button" @click="removeItem(index)" class="rounded-lg border border-rose-200 px-3 py-2.5 text-xs font-bold text-rose-700 hover:bg-rose-50">Hapus</button>
                        </div>
                    </template>
                    @if ($posProducts->isEmpty())
                        <p class="rounded-xl bg-amber-50 p-4 text-sm text-amber-800">Belum ada produk dengan stok tersedia.</p>
                    @endif
                </div>

                <fieldset>
                    <legend class="text-xs font-bold text-slate-600">Metode pembayaran</legend>
                    <div class="mt-2 grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ([
                            'outlet_cash' => 'Tunai',
                            'outlet_qris' => 'QRIS',
                            'bank_bca' => 'Transfer BCA',
                            'bank_mandiri' => 'Transfer Mandiri',
                            'ewallet_gopay' => 'GoPay',
                            'ewallet_ovo' => 'OVO',
                            'ewallet_dana' => 'DANA',
                        ] as $method => $label)
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-semibold text-slate-700 has-[:checked]:border-blue-700 has-[:checked]:bg-blue-50">
                                <input type="radio" name="payment_method" value="{{ $method }}" @checked(old('payment_method', 'outlet_cash') === $method) required>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                <div class="flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 pt-4">
                    <p class="text-sm font-bold text-slate-600">Total transaksi <span class="ml-2 text-xl font-black text-blue-950">Rp <span x-text="formatCurrency(total)">0</span></span></p>
                    <button type="submit" :disabled="products.length === 0" class="rounded-xl bg-blue-950 px-5 py-3 text-sm font-black text-white hover:bg-blue-800 disabled:cursor-not-allowed disabled:opacity-50">Simpan & cetak struk</button>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="text-lg font-black">Antrean servis & jadwal berikutnya</h2>
                <p class="mt-1 text-xs text-slate-500">Antrean kunjungan mendatang ditampilkan di sini dan dapat dipanggil pada tanggal kunjungannya. Halaman diperbarui otomatis setiap 20 detik.</p>
            </div>
            <div class="grid gap-4 p-4 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($queues as $queue)
                    @php($queueCategories = $queue->booking->items->map(fn ($item) => $item->product?->category_label)->filter()->unique()->values())
                    <article class="rounded-2xl border border-slate-200 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-2xl font-black text-blue-950">{{ $queue->queue_code }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-500">{{ $queue->store->name }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase {{ $queue->status === 'in_service' ? 'bg-blue-100 text-blue-800' : ($queue->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $queue->status === 'in_service' ? 'Sedang dikerjakan' : ($queue->status === 'completed' ? 'Selesai' : 'Menunggu') }}
                            </span>
                        </div>
                        <p class="mt-3 text-sm font-bold">{{ $queue->booking->customer_name }} <span class="font-normal text-slate-500">· {{ \Illuminate\Support\Carbon::parse($queue->booking->booking_date)->format('d/m/Y') }} {{ $queue->booking->booking_time }}</span></p>
                        <p class="mt-1 text-xs text-slate-500">{{ $queue->booking->customer_phone }}</p>
                        <p class="mt-3 text-xs font-bold text-slate-700">Kategori: {{ $queueCategories->isNotEmpty() ? $queueCategories->join(', ') : 'Servis Umum' }}</p>
                        @if ($queue->booking->items->isNotEmpty())
                            <p class="mt-1 text-xs text-slate-500">{{ $queue->booking->items->map(fn ($item) => $item->product_name.' × '.$item->quantity)->join(' · ') }}</p>
                        @endif
                        <div class="mt-4 flex flex-wrap gap-2">
                            @if ($queue->status === 'waiting' && $queue->queue_date->toDateString() <= now()->toDateString())
                                <form action="{{ route('cashir.queues.status', $queue) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="in_service">
                                    <button class="rounded-lg bg-blue-950 px-3 py-2 text-xs font-bold text-white hover:bg-blue-800">Panggil antrean</button>
                                </form>
                            @elseif ($queue->status === 'waiting')
                                <span class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-bold text-amber-800">Terjadwal</span>
                            @elseif ($queue->status === 'in_service')
                                <form action="{{ route('cashir.queues.status', $queue) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="completed">
                                    <button class="rounded-lg bg-emerald-700 px-3 py-2 text-xs font-bold text-white hover:bg-emerald-600">Tandai selesai</button>
                                </form>
                            @endif
                            <a href="{{ route('cashir.bookings.receipt', $queue->booking) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Cetak struk</a>
                        </div>
                    </article>
                @empty
                    <p class="rounded-xl bg-slate-50 p-6 text-sm text-slate-500 sm:col-span-2 xl:col-span-3">Belum ada antrean servis hari ini.</p>
                @endforelse
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4 sm:px-6">
                <h2 class="text-lg font-black">Pesanan masuk & validasi pembayaran</h2>
                <p class="mt-1 text-xs text-slate-500">Konfirmasi pesanan untuk membuat nomor antrean; tandai pembayaran setelah memverifikasi penerimaan.</p>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($bookings as $booking)
                    <article class="grid gap-4 p-5 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center sm:p-6">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-black">{{ $booking->customer_name }}</h3>
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-black uppercase text-slate-600">Booking {{ $booking->status }}</span>
                                @if ($booking->serviceQueue)
                                    <span class="rounded-full bg-cyan-100 px-2.5 py-1 text-[10px] font-black text-cyan-900">{{ $booking->serviceQueue->queue_code }}</span>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-slate-500">{{ $booking->customer_phone }} · {{ $booking->store->name }} · {{ $booking->booking_date }} {{ $booking->booking_time }}</p>
                            <div class="mt-2 text-xs text-slate-700">
                                @forelse ($booking->items as $item)
                                    <span class="mr-3">{{ $item->product_name }} × {{ $item->quantity }}</span>
                                @empty
                                    <span>{{ $booking->product->name ?? 'Servis umum' }}</span>
                                @endforelse
                            </div>
                            @if ($booking->payment)
                                <p class="mt-2 text-xs font-bold text-slate-800">
                                    {{ $booking->payment->method_label }} · {{ $booking->payment->status }}
                                    @if ($booking->payment->amount !== null)
                                        · Rp {{ number_format((float) $booking->payment->amount, 0, ',', '.') }}
                                    @endif
                                </p>
                            @endif
                        </div>

                        <div class="flex flex-wrap items-end gap-2">
                            <form action="{{ route('cashir.bookings.process', $booking) }}" method="POST" class="flex flex-wrap items-end gap-2">
                                @csrf
                                <label class="text-[10px] font-bold text-slate-500">Booking
                                    <select name="status" class="mt-1 block rounded-lg border border-slate-300 px-2 py-2 text-xs text-slate-800">
                                        <option value="pending" @selected($booking->status === 'pending')>Pending</option>
                                        <option value="confirmed" @selected($booking->status === 'confirmed')>Konfirmasi & antrekan</option>
                                        <option value="completed" @selected($booking->status === 'completed')>Selesai</option>
                                    </select>
                                </label>
                                @if ($booking->payment)
                                    <label class="text-[10px] font-bold text-slate-500">Pembayaran
                                        <select name="payment_status" class="mt-1 block rounded-lg border border-slate-300 px-2 py-2 text-xs text-slate-800">
                                            <option value="pending" @selected($booking->payment->status === 'pending')>Pending</option>
                                            <option value="paid" @selected($booking->payment->status === 'paid')>Lunas</option>
                                            <option value="failed" @selected($booking->payment->status === 'failed')>Gagal</option>
                                        </select>
                                    </label>
                                @endif
                                <button class="rounded-lg bg-blue-950 px-3 py-2.5 text-xs font-bold text-white hover:bg-blue-800">Simpan</button>
                            </form>
                            <a href="{{ route('cashir.bookings.receipt', $booking) }}" class="rounded-lg border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50">Struk</a>
                        </div>
                    </article>
                @empty
                    <p class="p-6 text-sm text-slate-500">Belum ada pesanan masuk.</p>
                @endforelse
            </div>
        </section>
    </main>
</body>
</html>
