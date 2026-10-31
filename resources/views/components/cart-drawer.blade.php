<div
    x-show="cartDrawerOpen"
    x-transition.opacity
    @click.self="cartDrawerOpen = false"
    class="fixed inset-0 z-[60] bg-slate-950/50 backdrop-blur-sm"
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
        class="absolute right-0 top-0 flex h-full w-full max-w-md flex-col bg-white shadow-2xl"
    >
        <div class="flex items-center justify-between border-b border-slate-100 px-5 py-5 sm:px-7">
            <div>
                <h2 id="cart-drawer-title" class="text-xl font-black text-slate-900">Keranjang Belanja</h2>
                <p class="mt-1 text-sm text-slate-500"><span x-text="cartCount"></span> produk dipilih</p>
            </div>
            <button
                type="button"
                @click="cartDrawerOpen = false"
                class="rounded-full p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
                aria-label="Tutup keranjang"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <p x-show="cartError" x-text="cartError" role="alert" class="mx-5 mt-4 rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700 sm:mx-7"></p>

        <div x-show="cart.length === 0" class="flex flex-1 flex-col items-center justify-center px-8 text-center">
            <span class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5m1.6 8l-1.6 2.3A1 1 0 006.2 17H17m-9 3a1 1 0 100-2 1 1 0 000 2zm9 0a1 1 0 100-2 1 1 0 000 2z"/>
                </svg>
            </span>
            <h3 class="text-lg font-bold text-slate-900">Keranjang masih kosong</h3>
            <p class="mt-2 text-sm text-slate-500">Pilih produk yang Anda butuhkan dan produk akan muncul di sini.</p>
            <a
                href="{{ route('home') }}#katalog"
                @click="cartDrawerOpen = false"
                class="mt-6 rounded-xl bg-blue-950 px-5 py-3 text-sm font-bold text-white transition hover:bg-blue-900"
            >
                Lihat katalog
            </a>
        </div>

        <div x-show="cart.length > 0" class="flex-1 space-y-4 overflow-y-auto px-5 py-5 sm:px-7">
            <template x-for="item in cart" :key="item.id">
                <article class="rounded-2xl border border-slate-100 p-4">
                    <div class="flex gap-4">
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-slate-50 text-3xl">
                            <img x-show="item.image" :src="item.image" :alt="item.name" class="h-full w-full object-contain">
                            <span x-show="!item.image" aria-hidden="true">🛵</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 x-text="item.name" class="line-clamp-2 text-sm font-bold text-slate-900"></h3>
                            <p class="mt-1 text-sm font-black text-blue-950">
                                Rp <span x-text="formatCurrency(item.price)"></span>
                            </p>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        @click="updateCartQuantity(item.id, -1)"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:border-blue-900 hover:text-blue-900"
                                        :aria-label="'Kurangi jumlah ' + item.name"
                                    >−</button>
                                    <span x-text="item.quantity" class="min-w-5 text-center text-sm font-bold text-slate-800"></span>
                                    <button
                                        type="button"
                                        @click="updateCartQuantity(item.id, 1)"
                                        :disabled="item.quantity >= item.stock"
                                        class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:border-blue-900 hover:text-blue-900 disabled:cursor-not-allowed disabled:opacity-40"
                                        :aria-label="'Tambah jumlah ' + item.name"
                                    >+</button>
                                </div>
                                <button
                                    type="button"
                                    @click="removeFromCart(item.id)"
                                    class="text-xs font-semibold text-slate-500 transition hover:text-red-600"
                                >Hapus</button>
                            </div>
                        </div>
                    </div>
                    <button
                        type="button"
                        @click="bookCartItem(item.id)"
                        class="mt-4 w-full rounded-xl bg-yellow-400 px-4 py-2.5 text-sm font-extrabold text-blue-950 transition hover:bg-yellow-300"
                    >Pilih untuk booking</button>
                </article>
            </template>
        </div>

        <div x-show="cart.length > 0" class="border-t border-slate-100 bg-white px-5 py-5 sm:px-7">
            <div class="flex items-center justify-between text-sm">
                <span class="font-semibold text-slate-500">Subtotal</span>
                <span class="text-lg font-black text-slate-900">Rp <span x-text="formatCurrency(cartTotal)"></span></span>
            </div>
            <p class="mt-2 text-xs leading-relaxed text-slate-500">Pilih produk untuk memasukkannya ke form booking. Setiap booking mencatat satu produk.</p>
        </div>
    </aside>
</div>
