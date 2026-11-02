<div
    x-cloak
    x-show="guestAuthPromptOpen"
    x-transition.opacity
    @click.self="guestAuthPromptOpen = false"
    @keydown.escape.window="guestAuthPromptOpen = false"
    class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/75 p-4 backdrop-blur-sm"
    role="presentation"
>
    <section
        x-show="guestAuthPromptOpen"
        x-transition
        class="w-full max-w-md rounded-3xl border border-white/10 bg-slate-900 p-6 text-white shadow-2xl sm:p-8"
        role="alertdialog"
        aria-modal="true"
        aria-labelledby="guest-auth-title"
        aria-describedby="guest-auth-description"
    >
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-yellow-300/10 text-yellow-200">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-7 4h14a2 2 0 002-2v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7a2 2 0 002 2zm2-9V7a5 5 0 0110 0v3"/>
            </svg>
        </div>
        <h2 id="guest-auth-title" class="mt-4 text-center text-xl font-black">Masuk untuk melanjutkan</h2>
        <p id="guest-auth-description" class="mt-2 text-center text-sm leading-6 text-slate-300">Silakan masuk terlebih dahulu untuk melanjutkan pemesanan.</p>
        <div class="mt-6 grid gap-3 sm:grid-cols-2">
            <a
                :href="guestLoginUrl"
                class="inline-flex min-h-11 items-center justify-center rounded-xl bg-yellow-300 px-4 py-3 text-sm font-extrabold text-slate-950 transition hover:bg-yellow-200"
            >Login / Sign In</a>
            <a
                :href="guestRegisterUrl"
                class="inline-flex min-h-11 items-center justify-center rounded-xl border border-white/15 px-4 py-3 text-sm font-bold text-white transition hover:bg-white/10"
            >Register</a>
        </div>
        <button
            type="button"
            @click="guestAuthPromptOpen = false"
            class="mt-4 w-full py-2 text-xs font-semibold text-slate-400 transition hover:text-white"
        >Nanti saja</button>
    </section>
</div>
