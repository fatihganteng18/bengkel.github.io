<div class="relative" x-data="{ accountMenuOpen: false }" @keydown.escape.window="accountMenuOpen = false">
    @auth
        <button
            type="button"
            @click="accountMenuOpen = !accountMenuOpen"
            @click.outside="accountMenuOpen = false"
            :aria-expanded="accountMenuOpen"
            aria-haspopup="true"
            :aria-label="t('accountMenu')"
            class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white/80 text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-900 dark:border-white/10 dark:bg-white/[0.06] dark:text-slate-200 dark:hover:bg-white/10"
        >
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                <circle cx="12" cy="8" r="4"/>
                <path stroke-linecap="round" d="M4.5 21a7.5 7.5 0 0115 0"/>
            </svg>
        </button>

        <div
            x-cloak
            x-show="accountMenuOpen"
            x-transition.origin.top.right
            class="absolute right-0 top-full z-50 mt-2 w-60 overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 text-slate-800 shadow-xl dark:border-white/10 dark:bg-slate-900 dark:text-slate-100"
            role="menu"
        >
            <div class="border-b border-slate-100 px-3 py-2.5 dark:border-white/10">
                <p class="truncate text-sm font-bold">{{ Auth::user()->name }}</p>
                <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ Auth::user()->email }}</p>
            </div>

            <a
                href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('account.dashboard') }}"
                class="mt-1 flex w-full items-center rounded-xl px-3 py-2.5 text-left text-sm font-semibold transition hover:bg-slate-100 dark:hover:bg-white/5"
                role="menuitem"
            >
                <span x-text="t('{{ Auth::user()->role === 'admin' ? 'adminDashboard' : 'myAccount' }}')">{{ Auth::user()->role === 'admin' ? 'Dashboard admin' : 'Akun saya' }}</span>
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button
                    type="submit"
                    class="flex w-full items-center rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-rose-700 transition hover:bg-rose-50 dark:text-rose-300 dark:hover:bg-rose-400/10"
                    role="menuitem"
                    x-text="t('logout')"
                >Keluar</button>
            </form>
        </div>
    @else
        <a
            href="{{ route('login') }}"
            class="hidden rounded-full border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-100 dark:border-white/10 dark:text-slate-200 dark:hover:border-white/30 dark:hover:bg-white/5 sm:inline-flex"
            x-text="t('login')"
        >Masuk</a>
    @endauth
</div>
