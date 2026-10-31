import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

const cartStorageKey = 'planet-ban-cart';

window.Alpine = Alpine;

Alpine.data('adminDashboard', () => ({
    chart: null,

    init() {
        const canvas = this.$refs.bookingChart;

        if (!(canvas instanceof HTMLCanvasElement)) {
            return;
        }

        const data = JSON.parse(canvas.dataset.chart);
        this.chart = new Chart(canvas, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [
                    {
                        label: 'Permintaan pembayaran',
                        data: data.transactions,
                        borderColor: '#1e3a8a',
                        backgroundColor: 'rgba(30, 58, 138, 0.12)',
                        fill: true,
                        tension: 0.35,
                    },
                ],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } },
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                },
            },
        });
    },

    destroy() {
        this.chart?.destroy();
    },
}));

Alpine.data('authForm', () => ({
    showPassword: false,
    showConfirmation: false,
    isSubmitting: false,

    submit(event) {
        if (this.isSubmitting) {
            return;
        }

        const form = event.currentTarget;

        if (!(form instanceof HTMLFormElement)) {
            console.error('Form autentikasi tidak dapat dikirim.');
            return;
        }

        this.isSubmitting = true;
        window.setTimeout(() => form.submit(), 120);
    },
}));

Alpine.data('registerForm', () => ({
    showPassword: false,
    showPasswordConfirmation: false,
    isSubmitting: false,
    password: '',
    passwordConfirmation: '',
    passwordConfirmationError: false,

    get passwordChecks() {
        return [
            { label: 'Minimal 8 karakter', passed: this.password.length >= 8 },
            { label: 'Mengandung huruf', passed: /[A-Za-z]/.test(this.password) },
            { label: 'Mengandung angka', passed: /\d/.test(this.password) },
        ];
    },

    get passwordStrength() {
        return this.passwordChecks.filter((check) => check.passed).length;
    },

    submit(event) {
        if (this.isSubmitting) {
            return;
        }

        const form = event.currentTarget;

        if (!(form instanceof HTMLFormElement)) {
            console.error('Form pendaftaran tidak dapat dikirim.');
            return;
        }

        this.passwordConfirmationError = this.password !== this.passwordConfirmation;

        if (this.passwordConfirmationError) {
            form.elements.password_confirmation.focus();
            return;
        }

        this.isSubmitting = true;
        window.setTimeout(() => form.submit(), 120);
    },
}));

Alpine.data('planetBanApp', () => ({
    mobileMenuOpen: false,
    bookingModalOpen: false,
    accountDropdown: false,
    cartDrawerOpen: false,
    selectedCategory: 'all',
    searchProduct: '',
    searchStore: '',
    selectedProductId: '',
    selectedStoreId: '',
    isLoading: false,
    cart: [],
    cartError: '',

    init() {
        try {
            const savedCart = localStorage.getItem(cartStorageKey);

            if (!savedCart) {
                return;
            }

            const parsedCart = JSON.parse(savedCart);

            if (!Array.isArray(parsedCart)) {
                throw new TypeError('Data keranjang bukan daftar produk.');
            }

            this.cart = parsedCart.filter((item) => {
                const valid = item
                    && (typeof item.id === 'string' || typeof item.id === 'number')
                    && typeof item.name === 'string'
                    && Number.isFinite(item.price)
                    && Number.isInteger(item.quantity)
                    && Number.isInteger(item.stock)
                    && item.stock > 0;

                if (!valid) {
                    console.warn('Produk keranjang yang tidak valid dilewati.', item);
                }

                return valid;
            });
        } catch (error) {
            console.error('Keranjang lokal tidak dapat dimuat.', error);
            this.cartError = 'Keranjang tersimpan tidak dapat dimuat. Silakan tambahkan produk kembali.';
        }
    },

    get cartCount() {
        return this.cart.reduce((count, item) => count + item.quantity, 0);
    },

    get cartTotal() {
        return this.cart.reduce((total, item) => total + item.price * item.quantity, 0);
    },

    openBookingModal(productId = '', storeId = '') {
        this.selectedProductId = productId === null ? '' : String(productId);
        this.selectedStoreId = storeId === null ? '' : String(storeId);
        this.bookingModalOpen = true;
        this.mobileMenuOpen = false;
        this.cartDrawerOpen = false;
    },

    openBookingModalWithStore(storeId) {
        this.openBookingModal('', storeId);
    },

    submitForm(event) {
        if (this.isLoading) {
            return;
        }

        const form = event.currentTarget;

        if (!(form instanceof HTMLFormElement)) {
            console.error('Form booking tidak dapat dikirim.');
            return;
        }

        this.isLoading = true;
        window.setTimeout(() => form.submit(), 120);
    },

    closeOverlays() {
        this.cartDrawerOpen = false;
        this.mobileMenuOpen = false;

        if (!this.isLoading) {
            this.bookingModalOpen = false;
        }
    },

    focusProductSearch() {
        const searchInput = document.querySelector('[data-product-search]');

        if (!searchInput) {
            console.error('Kolom pencarian produk tidak ditemukan.');
            return;
        }

        document.querySelector('#katalog')?.scrollIntoView({ behavior: 'smooth' });
        searchInput.focus({ preventScroll: true });
    },

    addToCartFromButton(button) {
        if (!(button instanceof HTMLElement)) {
            console.error('Tombol produk untuk keranjang tidak valid.');
            this.cartError = 'Produk tidak dapat ditambahkan. Silakan coba lagi.';
            return;
        }

        const product = {
            id: button.dataset.productId,
            name: button.dataset.productName,
            price: Number(button.dataset.productPrice),
            image: button.dataset.productImage || '',
            stock: Number(button.dataset.productStock),
        };

        if (
            !product.id
            || !product.name
            || !Number.isFinite(product.price)
            || !Number.isInteger(product.stock)
            || product.stock < 1
        ) {
            console.error('Data produk untuk keranjang tidak lengkap.', product);
            this.cartError = 'Produk tidak dapat ditambahkan. Silakan coba lagi.';
            return;
        }

        const cartItem = this.cart.find((item) => String(item.id) === String(product.id));

        if (cartItem && cartItem.quantity >= product.stock) {
            this.cartError = 'Jumlah produk di keranjang sudah mencapai batas stok.';
            this.cartDrawerOpen = true;
            return;
        }

        if (cartItem) {
            cartItem.quantity += 1;
            cartItem.stock = product.stock;
        } else {
            this.cart.push({ ...product, quantity: 1 });
        }

        this.saveCart();
        this.cartDrawerOpen = true;
    },

    updateCartQuantity(productId, amount) {
        const cartItem = this.cart.find((item) => String(item.id) === String(productId));

        if (!cartItem) {
            console.error('Produk yang akan diubah tidak ditemukan di keranjang.', productId);
            this.cartError = 'Produk keranjang tidak ditemukan. Silakan muat ulang halaman.';
            return;
        }

        const nextQuantity = cartItem.quantity + amount;

        if (nextQuantity > cartItem.stock) {
            this.cartError = 'Jumlah produk tidak boleh melebihi stok yang tersedia.';
            return;
        }

        this.cartError = '';

        if (nextQuantity < 1) {
            this.removeFromCart(productId);
            return;
        }

        cartItem.quantity = nextQuantity;
        this.saveCart();
    },

    removeFromCart(productId) {
        this.cart = this.cart.filter((item) => String(item.id) !== String(productId));
        this.saveCart();
    },

    bookCartItem(productId) {
        this.openBookingModal(productId);
    },

    formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID').format(amount);
    },

    saveCart() {
        try {
            localStorage.setItem(cartStorageKey, JSON.stringify(this.cart));
            this.cartError = '';
        } catch (error) {
            console.error('Keranjang tidak dapat disimpan di browser.', error);
            this.cartError = 'Keranjang tidak dapat disimpan di browser ini.';
        }
    },
}));

Alpine.start();
