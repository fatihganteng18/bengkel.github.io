<aside class="flex flex-col bg-slate-950 text-white lg:min-h-screen">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 border-b border-white/10 px-6 py-6">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-lime-300 text-lg font-black italic text-slate-950">A</span>
        <span><span class="block text-sm font-black italic tracking-wide">Arisa<span class="text-lime-300"> Knalpot</span></span><span class="mt-1 block text-[10px] font-bold uppercase tracking-[.2em] text-slate-400">Admin workspace</span></span>
    </a>

    <nav class="flex gap-2 overflow-x-auto px-4 py-4 lg:flex-col lg:overflow-visible lg:px-3" aria-label="Navigasi admin">
        <a href="{{ route('admin.dashboard') }}" @class(['flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-sm transition', 'bg-cyan-300/10 font-bold text-cyan-200 ring-1 ring-cyan-300/10' => $activeMenu === 'dashboard', 'font-semibold text-slate-300 hover:bg-white/[0.06] hover:text-white' => $activeMenu !== 'dashboard']) @if($activeMenu === 'dashboard') aria-current="page" @endif>
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="5" rx="1.5"/><rect x="13" y="10" width="8" height="11" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/></svg>
            Ringkasan
        </a>
        <a href="{{ route('admin.bookings') }}" @class(['flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-sm transition', 'bg-cyan-300/10 font-bold text-cyan-200 ring-1 ring-cyan-300/10' => $activeMenu === 'bookings', 'font-semibold text-slate-300 hover:bg-white/[0.06] hover:text-white' => $activeMenu !== 'bookings']) @if($activeMenu === 'bookings') aria-current="page" @endif>
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 3v3m8-3v3M4 9h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z"/><path stroke-linecap="round" d="m9 14 2 2 4-4"/></svg>
            Kelola booking
        </a>
        <a href="{{ route('admin.queues') }}" @class(['flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-sm transition', 'bg-cyan-300/10 font-bold text-cyan-200 ring-1 ring-cyan-300/10' => $activeMenu === 'queues', 'font-semibold text-slate-300 hover:bg-white/[0.06] hover:text-white' => $activeMenu !== 'queues']) @if($activeMenu === 'queues') aria-current="page" @endif>
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5h16M4 12h16M4 19h16"/><circle cx="7" cy="5" r="1"/><circle cx="7" cy="12" r="1"/><circle cx="7" cy="19" r="1"/></svg>
            Monitor antrean
        </a>
        <a href="{{ route('admin.products.create') }}" @class(['flex shrink-0 items-center gap-3 rounded-xl px-4 py-3 text-sm transition', 'bg-cyan-300/10 font-bold text-cyan-200 ring-1 ring-cyan-300/10' => $activeMenu === 'products', 'font-semibold text-slate-300 hover:bg-white/[0.06] hover:text-white' => $activeMenu !== 'products']) @if($activeMenu === 'products') aria-current="page" @endif>
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m-7-7h14"/><rect x="3" y="3" width="18" height="18" rx="3"/></svg>
            Kelola Produk <span class="text-[10px] text-slate-400">(Tambah Produk)</span>
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
