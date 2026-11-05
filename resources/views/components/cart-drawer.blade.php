<div
    x-cloak
    x-show="cartDrawerOpen"
    x-transition.opacity
    @click.self="cartDrawerOpen = false"
    class="fixed inset-0 z-[60] bg-slate-950/75 backdrop-blur-md"
    style="display: none;"
    aria-labelledby="cart-drawer-title"
    aria-modal="true"
    role="dialog"
>
    <aside
        x-show="cartDrawerOpen"
        x-transition:enter="transform transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        @click.stop
        class="cart-drawer-panel absolute right-0 top-0 flex h-full w-full max-w-md flex-col border-l border-white/10 shadow-2xl"
    >
        <div class="flex items-center justify-between border-b border-white/10 px-5 py-5 sm:px-7">
            <div>
                <h2 id="cart-drawer-title" class="text-xl font-black text-slate-900 dark:text-white" x-text="t('cart')"></h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400"><span x-text="cartCount"></span> <span x-text="locale === 'en' ? 'items selected' : 'produk dipilih'"></span></p>
            </div>
            <button type="button" @click="cartDrawerOpen = false" class="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:border-white/10 dark:text-slate-300 dark:hover:bg-white/10 dark:hover:text-white" :aria-label="locale === 'en' ? 'Close cart' : 'Tutup keranjang'">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <p x-show="cartError" x-text="cartError" role="alert" class="mx-5 mt-4 rounded-xl border border-rose-300/20 bg-rose-300/10 px-4 py-3 text-sm font-medium text-rose-200 sm:mx-7"></p>

        <div x-show="cart.length === 0" class="flex flex-1 flex-col items-center justify-center px-8 text-center">
            <span class="mb-4 flex h-16 w-16 items-center justify-center rounded-full border border-white/10 bg-white/[0.04] text-cyan-200">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5m1.6 8l-1.6 2.3A1 1 0 006.2 17H17m-9 3a1 1 0 100-2 1 1 0 000 2zm9 0a1 1 0 100-2 1 1 0 000 2z"/></svg>
            </span>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white" x-text="locale === 'en' ? 'Your cart is empty' : 'Keranjang masih kosong'"></h3>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400" x-text="locale === 'en' ? 'Choose the products you need and they will show up here.' : 'Pilih produk yang Anda butuhkan dan produk akan muncul di sini.'"></p>
            <a href="{{ route('home') }}#katalog" @click="cartDrawerOpen = false" class="mt-6 rounded-xl bg-yellow-300 px-5 py-3 text-sm font-bold text-slate-950 transition hover:bg-yellow-200 active:scale-95">
                <span x-text="locale === 'en' ? 'Browse catalog' : 'Lihat katalog'"></span>
            </a>
        </div>

        <div x-show="cart.length > 0" class="flex-1 space-y-4 overflow-y-auto px-5 py-5 sm:px-7">
            <template x-for="item in cart" :key="item.id">
                <article class="rounded-2xl border border-white/10 bg-white/[0.035] p-4 transition hover:border-cyan-300/20">
                    <div class="flex gap-4">
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-white/[0.06] bg-[#111923] text-3xl">
                            <img x-show="item.image" :src="item.image" :alt="item.name" onerror="this.onerror=null;this.src='/images/products/exhaust-sample.svg'" class="h-full w-full object-contain">
                            <span x-show="!item.image" aria-hidden="true">⌁</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 x-text="item.name" class="line-clamp-2 text-sm font-bold text-white"></h3>
                            <p class="mt-1 text-sm font-black text-yellow-300">Rp <span x-text="formatCurrency(item.price)"></span></p>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="updateCartQuantity(item.id, -1)" class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:border-cyan-300/40 hover:text-cyan-700 dark:border-white/10 dark:text-slate-200 dark:hover:text-cyan-200" :aria-label="(locale === 'en' ? 'Decrease quantity ' : 'Kurangi jumlah ') + item.name">−</button>
                                    <span x-text="item.quantity" class="min-w-5 text-center text-sm font-bold text-slate-900 dark:text-white"></span>
                                    <button type="button" @click="updateCartQuantity(item.id, 1)" :disabled="item.quantity >= item.stock" class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:border-cyan-300/40 hover:text-cyan-700 disabled:cursor-not-allowed disabled:opacity-40 dark:border-white/10 dark:text-slate-200 dark:hover:text-cyan-200" :aria-label="(locale === 'en' ? 'Increase quantity ' : 'Tambah jumlah ') + item.name">+</button>
                                </div>
                                <button type="button" @click="removeFromCart(item.id)" class="text-xs font-semibold text-slate-500 transition hover:text-rose-500 dark:text-slate-400 dark:hover:text-rose-300" x-text="locale === 'en' ? 'Remove' : 'Hapus'"></button>
                            </div>
                        </div>
                    </div>
                </article>
            </template>
        </div>

        <div x-show="cart.length > 0" class="border-t border-white/10 bg-[#0d131b]/90 px-5 py-5 backdrop-blur-xl sm:px-7">
            <div class="flex items-center justify-between text-sm">
                <span class="font-semibold text-slate-500 dark:text-slate-400" x-text="locale === 'en' ? 'Subtotal' : 'Subtotal'"></span>
                <span class="text-lg font-black text-slate-900 dark:text-white"><span x-text="locale === 'en' ? 'IDR' : 'Rp'"></span> <span x-text="formatCurrency(cartTotal)"></span></span>
            </div>
            <p class="mt-2 text-xs leading-relaxed text-slate-500" x-text="locale === 'en' ? 'All products and quantities will be included in one booking.' : 'Semua produk dan jumlahnya akan dimasukkan ke satu booking.'"></p>
            <button type="button" @click="checkoutAll()" class="mt-4 w-full rounded-xl bg-yellow-300 px-4 py-3 text-sm font-black text-slate-950 transition hover:bg-yellow-200 active:scale-[.98]">
                <span x-text="locale === 'en' ? 'Proceed to Booking / Checkout All' : 'Lanjut Booking / Checkout Semua'"></span>
            </button>
        </div>
    </aside>
</div>
