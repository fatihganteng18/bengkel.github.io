<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - Planet Ban</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-800 antialiased">
    <header class="sticky top-0 z-30 border-b border-slate-100 bg-white/95 shadow-sm backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="rounded-lg bg-blue-950 px-3 py-1.5 text-lg font-black italic text-white shadow">
                PLANET <span class="text-yellow-400">BAN</span>
            </a>
            <div class="flex items-center gap-3 sm:gap-5">
                <a href="{{ route('home') }}" class="hidden text-sm font-semibold text-slate-600 transition hover:text-blue-950 sm:block">Lanjut belanja</a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">Keluar</button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        @if (session('success'))
            <div role="status" class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <section class="relative overflow-hidden rounded-3xl bg-blue-950 px-6 py-8 text-white shadow-xl sm:px-10 sm:py-10">
            <div class="absolute -right-16 -top-28 h-72 w-72 rounded-full bg-blue-800/60 blur-3xl"></div>
            <div class="relative flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.15em] text-yellow-400">Akun Planet Ban</p>
                    <h1 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Halo, {{ Auth::user()->name }}</h1>
                    <p class="mt-2 text-sm text-blue-100/80">Kelola booking servis dan pantau kunjungan Anda di sini.</p>
                </div>
                <a href="{{ route('home') }}#katalog" class="inline-flex w-fit items-center gap-2 rounded-xl bg-yellow-400 px-5 py-3 text-sm font-extrabold text-blue-950 transition hover:bg-yellow-300">
                    Jelajahi produk <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </section>

        <section class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-[320px_1fr]">
            <aside class="h-fit rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-xl font-black text-blue-950">
                    {{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}
                </div>
                <h2 class="mt-4 text-lg font-black text-slate-950">{{ Auth::user()->name }}</h2>
                <p class="mt-1 break-all text-sm text-slate-500">{{ Auth::user()->email }}</p>
                <p class="mt-1 text-sm text-slate-500">{{ Auth::user()->phone }}</p>

                <div class="mt-6 border-t border-slate-100 pt-5">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-slate-500">Total booking</span>
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-sm font-black text-blue-950">{{ $bookings->count() }}</span>
                    </div>
                </div>
            </aside>

            <div>
                <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-black tracking-tight text-slate-950">Riwayat booking</h2>
                        <p class="mt-1 text-sm text-slate-500">Status dan jadwal servis Anda sebelumnya.</p>
                    </div>
                    <a href="{{ route('booking.status') }}" class="text-sm font-bold text-blue-900 transition hover:text-red-600">Cek status booking &rarr;</a>
                </div>

                @forelse ($bookings as $booking)
                    @php
                        $statusStyles = [
                            'pending' => 'bg-amber-50 text-amber-800 ring-amber-200',
                            'confirmed' => 'bg-blue-50 text-blue-800 ring-blue-200',
                            'completed' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
                        ];
                        $statusLabels = [
                            'pending' => 'Menunggu konfirmasi',
                            'confirmed' => 'Dikonfirmasi',
                            'completed' => 'Selesai',
                        ];
                    @endphp
                    <article class="mb-4 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-md sm:p-6">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Booking #{{ $booking->id }}</p>
                                <h3 class="mt-1 text-base font-extrabold text-slate-950">{{ $booking->product?->name ?? 'Servis motor' }}</h3>
                                <p class="mt-2 text-sm text-slate-600">{{ $booking->store?->name ?? 'Outlet' }}{{ $booking->store?->city ? ', ' . $booking->store->city : '' }}</p>
                            </div>
                            <span @class([
                                'inline-flex w-fit rounded-full px-3 py-1.5 text-xs font-bold ring-1 ring-inset',
                                $statusStyles[$booking->status] ?? 'bg-slate-50 text-slate-700 ring-slate-200',
                            ])>
                                {{ $statusLabels[$booking->status] ?? ucfirst($booking->status) }}
                            </span>
                        </div>
                        <div class="mt-5 flex flex-wrap gap-x-6 gap-y-2 border-t border-slate-100 pt-4 text-sm text-slate-500">
                            <span><strong class="font-semibold text-slate-700">Tanggal:</strong> {{ $booking->booking_date }}</span>
                            <span><strong class="font-semibold text-slate-700">Jam:</strong> {{ $booking->booking_time }} WIB</span>
                            @if ($booking->serviceQueue)
                                <span><strong class="font-semibold text-slate-700">Antrean:</strong> {{ $booking->serviceQueue->queue_code }} · {{ $booking->serviceQueue->status === 'in_service' ? 'Sedang dikerjakan' : ($booking->serviceQueue->status === 'completed' ? 'Selesai' : 'Menunggu') }}</span>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
                        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-950">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3M5 11h14M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                            </svg>
                        </span>
                        <h3 class="mt-4 text-lg font-black text-slate-950">Belum ada booking</h3>
                        <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">Booking servis atau pilih produk untuk dipasang di outlet Planet Ban.</p>
                        <a href="{{ route('home') }}#beranda" class="mt-5 inline-flex rounded-xl bg-blue-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-900">Mulai booking</a>
                    </div>
                @endforelse
            </div>
        </section>
    </main>
</body>
</html>
