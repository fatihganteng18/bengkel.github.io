<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="10">
    <meta name="theme-color" content="#020617">
    <title>Monitor Antrean | Arisa Knalpot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-white">
    @if($adminLayout)
        <div class="min-h-screen lg:grid lg:grid-cols-[260px_minmax(0,1fr)]">
            @include('components.admin-sidebar', ['activeMenu' => 'queues'])
            <div class="min-w-0">
                <header class="flex items-center justify-between gap-4 border-b border-white/10 bg-slate-900 px-4 py-4 sm:px-7 lg:px-9">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[.2em] text-cyan-300">ARISA KNALPOT · ADMIN</p>
                        <h1 class="mt-1 text-xl font-black tracking-tight sm:text-2xl">Monitor Antrean</h1>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-xl border border-white/10 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-white/10">Keluar</button>
                    </form>
                </header>
                <main class="mx-auto max-w-[1600px] px-4 py-6 sm:px-7 lg:px-9">
    @else
        <main class="mx-auto max-w-7xl px-5 py-8 sm:px-8 sm:py-12">
        <header class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-black uppercase tracking-[.25em] text-cyan-300">ARISA KNALPOT · LIVE QUEUE</p>
                <h1 class="mt-2 text-3xl font-black sm:text-5xl">Antrean Servis Hari Ini</h1>
                <p class="mt-2 text-sm text-slate-400">{{ now()->translatedFormat('l, d F Y') }} · Diperbarui otomatis setiap 10 detik</p>
            </div>
            <a href="{{ route('home') }}" class="rounded-xl border border-white/15 px-4 py-2 text-sm font-bold text-slate-200 hover:bg-white/10">Beranda</a>
        </header>
    @endif

        @forelse ($queues as $storeName => $storeQueues)
            <section class="mb-8 overflow-hidden rounded-3xl border border-white/10 bg-white/[0.04]">
                <h2 class="border-b border-white/10 px-5 py-4 text-lg font-black text-yellow-200 sm:px-7">{{ $storeName }}</h2>
                <div class="grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 sm:p-6">
                    @foreach ($storeQueues as $queue)
                        @php($queueCategories = $queue->booking->items->map(fn ($item) => $item->product?->category_label)->filter()->unique()->values())
                        <article class="rounded-2xl border p-4 {{ $queue->status === 'in_service' ? 'border-cyan-300/40 bg-cyan-300/10' : ($queue->status === 'completed' ? 'border-emerald-300/20 bg-emerald-300/5 opacity-70' : 'border-white/10 bg-slate-900/80') }}">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-3xl font-black tracking-tight {{ $queue->status === 'in_service' ? 'text-cyan-200' : 'text-white' }}">{{ $queue->queue_code }}</span>
                                <span class="text-right text-[10px] font-black uppercase tracking-wide {{ $queue->status === 'in_service' ? 'text-cyan-200' : ($queue->status === 'completed' ? 'text-emerald-300' : 'text-amber-200') }}">
                                    {{ $queue->status === 'in_service' ? 'Sedang dikerjakan' : ($queue->status === 'completed' ? 'Selesai' : 'Menunggu') }}
                                </span>
                            </div>
                            <p class="mt-3 text-xs font-bold text-slate-300">Kategori: {{ $queueCategories->isNotEmpty() ? $queueCategories->join(', ') : 'Servis Umum' }}</p>
                            @if ($queue->booking->items->isNotEmpty())
                                <p class="mt-1 text-xs text-slate-400">{{ $queue->booking->items->map(fn ($item) => $item->product_name.' × '.$item->quantity)->join(' · ') }}</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        @empty
            <section class="rounded-3xl border border-white/10 bg-white/[0.04] px-6 py-16 text-center">
                <p class="text-lg font-bold">Belum ada antrean servis hari ini</p>
                <p class="mt-2 text-sm text-slate-400">Nomor antrean akan muncul setelah booking dikonfirmasi oleh petugas.</p>
            </section>
        @endforelse
        </main>
    @if($adminLayout)
        </div>
        </div>
    @endif
</body>
</html>
