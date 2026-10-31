import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

const cartStorageKey = 'planet-ban-cart';

window.Alpine = Alpine;

Alpine.data('productGallery', (images = []) => {
    const tireIllustration = (view) => {
        const tread = view === 1
            ? Array.from({ length: 11 }, (_, index) => `<path d="M${188 + index * 8} 138l-38 62m64-43-44 72m70-52-45 72m73-52-45 72m74-52-45 72m74-52-45 72m74-52-45 72m74-52-45 72m74-52-45 72m74-52-45 72m74-52-45 72" />`).join('')
            : '';
        const viewTransform = view === 2 ? 'rotate(-18 300 300)' : '';
        const sidewallText = view === 3
            ? '<text x="300" y="126" text-anchor="middle" fill="#cbd5e1" font-size="17" font-family="sans-serif" font-weight="700" letter-spacing="4">PRESA PLANETO</text>'
            : '';
        const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 600"><defs><radialGradient id="t" cx="36%" cy="28%"><stop stop-color="#475569"/><stop offset=".5" stop-color="#202833"/><stop offset="1" stop-color="#090d13"/></radialGradient><linearGradient id="r" x2="1" y2="1"><stop stop-color="#64748b"/><stop offset=".5" stop-color="#1e293b"/><stop offset="1" stop-color="#94a3b8"/></linearGradient><clipPath id="c"><circle cx="300" cy="300" r="189"/></clipPath></defs><rect width="600" height="600" rx="40" fill="#f1f5f9"/><g transform="${viewTransform}"><circle cx="300" cy="300" r="217" fill="#111827" stroke="#334155" stroke-width="8"/><circle cx="300" cy="300" r="195" fill="url(#t)" stroke="#0f172a" stroke-width="14"/><circle cx="300" cy="300" r="157" fill="none" stroke="#475569" stroke-width="2" stroke-dasharray="3 7"/><g clip-path="url(#c)" fill="none" stroke="#080b10" stroke-width="12" opacity=".85">${tread}</g><circle cx="300" cy="300" r="132" fill="url(#r)" stroke="#0f172a" stroke-width="12"/><circle cx="300" cy="300" r="103" fill="#cbd5e1" stroke="#334155" stroke-width="10"/><circle cx="300" cy="300" r="54" fill="#64748b" stroke="#1e293b" stroke-width="15"/><circle cx="300" cy="300" r="21" fill="#0f172a"/><path d="M300 198v49m0 106v49m102-102h-49m-106 0h-49m174-72-35 35m-74 74-35 35m144 0-35-35m-74-74-35-35" stroke="#475569" stroke-width="14" stroke-linecap="round"/>${sidewallText}</g><text x="300" y="558" text-anchor="middle" fill="#64748b" font-size="13" font-family="sans-serif" font-weight="700" letter-spacing="3">TUBELESS · MOTORCYCLE</text></svg>`;

        return `data:image/svg+xml,${encodeURIComponent(svg)}`;
    };

    return {
        images: images.length ? images : [0, 1, 2, 3].map(tireIllustration),
        activeIndex: 0,
        zooming: false,
        zoomX: 50,
        zoomY: 50,

        get activeImage() {
            return this.images[this.activeIndex];
        },

        select(index) {
            this.activeIndex = (index + this.images.length) % this.images.length;
            this.zooming = false;
        },

        move(direction) {
            this.select(this.activeIndex + direction);
        },

        trackZoom(event) {
            const bounds = event.currentTarget.getBoundingClientRect();
            this.zoomX = ((event.clientX - bounds.left) / bounds.width) * 100;
            this.zoomY = ((event.clientY - bounds.top) / bounds.height) * 100;
        },
    };
});

Alpine.data('adminDashboard', () => ({
    charts: [],

    init() {
        const salesCanvas = this.$refs.salesChart;
        const bestSellersCanvas = this.$refs.bestSellersChart;
        const stockCanvas = this.$refs.stockChart;

        if (salesCanvas instanceof HTMLCanvasElement) {
            const data = JSON.parse(salesCanvas.dataset.chart);
            this.charts.push(new Chart(salesCanvas, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Pendapatan terkonfirmasi (Rp)',
                            data: data.revenue,
                            borderColor: '#0e7490',
                            backgroundColor: 'rgba(6, 182, 212, 0.12)',
                            fill: true,
                            tension: 0.35,
                            pointBackgroundColor: '#facc15',
                            pointBorderColor: '#fff',
                            pointRadius: 4,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Transaksi tercatat',
                            data: data.transactions,
                            borderColor: '#7c3aed',
                            backgroundColor: 'rgba(124, 58, 237, 0.08)',
                            fill: false,
                            tension: 0.35,
                            pointRadius: 3,
                            yAxisID: 'y1',
                        },
                    ],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } },
                        tooltip: {
                            callbacks: {
                                label: (context) => `Rp ${new Intl.NumberFormat('id-ID').format(context.parsed.y)}`,
                            },
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => new Intl.NumberFormat('id-ID', {
                                    notation: 'compact',
                                    maximumFractionDigits: 1,
                                }).format(value),
                            },
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            ticks: { precision: 0 },
                        },
                    },
                },
            }));
        }

        if (bestSellersCanvas instanceof HTMLCanvasElement) {
            const data = JSON.parse(bestSellersCanvas.dataset.chart);
            this.charts.push(new Chart(bestSellersCanvas, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Produk terjual',
                        data: data.values,
                        backgroundColor: ['#0e7490', '#0891b2', '#06b6d4', '#22d3ee', '#67e8f9'],
                        borderRadius: 8,
                        maxBarThickness: 36,
                    }],
                },
                options: {
                    indexAxis: 'y',
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
                },
            }));
        }

        if (stockCanvas instanceof HTMLCanvasElement) {
            const data = JSON.parse(stockCanvas.dataset.chart);
            this.charts.push(new Chart(stockCanvas, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Sisa stok',
                        data: data.values,
                        backgroundColor: data.values.map((stock) => stock <= 5 ? '#f87171' : '#facc15'),
                        borderRadius: 8,
                        maxBarThickness: 36,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                },
            }));
        }
    },

    destroy() {
        this.charts.forEach((chart) => chart.destroy());
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

Alpine.data('statsCounter', () => ({
    counters: [
        { target: 120000, value: 0, suffix: '+' },
        { target: 1000, value: 0, suffix: '+' },
        { target: 250000, value: 0, suffix: '+' },
    ],
    observer: null,

    init() {
        this.observer = new IntersectionObserver(([entry]) => {
            if (!entry.isIntersecting) {
                return;
            }

            this.counters.forEach((counter) => {
                const start = performance.now();
                const duration = 1400;
                const animate = (now) => {
                    const progress = Math.min((now - start) / duration, 1);
                    counter.value = Math.floor(counter.target * (1 - (1 - progress) ** 4));

                    if (progress < 1) {
                        requestAnimationFrame(animate);
                    }
                };

                requestAnimationFrame(animate);
            });

            this.observer?.disconnect();
        }, { threshold: 0.3 });

        this.observer.observe(this.$el);
    },

    destroy() {
        this.observer?.disconnect();
    },

    formatNumber(value) {
        return new Intl.NumberFormat(this.locale === 'en' ? 'en-US' : 'id-ID').format(value);
    },
}));

Alpine.data('planetBanApp', () => ({
    mobileMenuOpen: false,
    bookingModalOpen: false,
    accountDropdown: false,
    cartDrawerOpen: false,
    languageMenuOpen: false,
    isDark: false,
    locale: 'id',
    liveNotificationIndex: 0,
    liveNotificationVisible: true,
    notificationTimer: null,
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
            const savedLocale = localStorage.getItem('planet-ban-locale');
            const savedTheme = localStorage.getItem('planet-ban-theme');

            if (savedLocale === 'id' || savedLocale === 'en') {
                this.locale = savedLocale;
            }

            if (savedTheme === 'light' || savedTheme === 'dark') {
                this.isDark = savedTheme === 'dark';
            }
        } catch (error) {
            console.error('Preferensi bahasa dan tema tidak dapat dimuat.', error);
        }

        this.applyTheme();
        this.notificationTimer = window.setInterval(() => {
            this.liveNotificationVisible = false;
            window.setTimeout(() => {
                this.liveNotificationIndex = (this.liveNotificationIndex + 1) % 3;
                this.liveNotificationVisible = true;
            }, 350);
        }, 6500);

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

    destroy() {
        if (this.notificationTimer !== null) {
            window.clearInterval(this.notificationTimer);
        }
    },

    translations: {
        id: {
            navServices: 'Layanan',
            navCatalog: 'Katalog',
            navStores: 'Lokasi outlet',
            navBookingStatus: 'Cek booking',
            login: 'Masuk',
            accountMenu: 'Menu akun',
            myAccount: 'Akun saya',
            adminDashboard: 'Dashboard admin',
            logout: 'Keluar',
            cart: 'Keranjang',
            openNavigation: 'Buka navigasi',
            switchLight: 'Aktifkan mode terang',
            switchDark: 'Aktifkan mode gelap',
            mobileCatalog: 'Katalog produk',
            mobileBookingStatus: 'Cek status booking',
            bookingService: 'Booking servis',
            openToday: 'BUKA HARI INI',
            serviceAvailable: 'Layanan tersedia',
            heroEyebrow: 'Your ride, refined.',
            heroTitle: 'Performa motor,',
            heroSubtitle: 'tanpa kompromi.',
            heroDescription: 'Perawatan presisi, produk terjamin, dan mekanik ahli. Semua yang motor Anda butuhkan, dalam satu pit stop.',
            bookNow: 'Booking servis sekarang',
            browseProducts: 'Jelajahi produk',
            happyCustomers: 'Pelanggan puas',
            branches: 'Cabang',
            completedServices: 'Servis selesai',
            serviceIntro: 'Dibuat untuk perjalanan Anda',
            serviceHeadline: 'Satu pit stop.',
            serviceHeadlineMuted: 'Banyak beresnya.',
            serviceDescription: 'Servis dan kebutuhan motor dengan standar yang konsisten, ditangani oleh teknisi profesional di outlet terdekat.',
            tires: 'Ban motor',
            tiresDescription: 'Grip maksimal, siap segala kondisi jalan.',
            oils: 'Oli & pelumas',
            oilsDescription: 'Mesin halus, performa tetap optimal.',
            spareparts: 'Sparepart',
            sparepartsDescription: 'Komponen pilihan untuk kenyamanan berkendara.',
            serviceBooking: 'Booking servis',
            serviceBookingDescription: 'Pilih jadwal dan outlet, tanpa antre lama.',
            seeOptions: 'Lihat pilihan',
            setSchedule: 'Atur jadwal',
            catalogIntro: 'Pilihan untuk motor Anda',
            catalogHeadline: 'Upgrade perjalanan.',
            catalogHeadlineMuted: 'Mulai dari sini.',
            searchProducts: 'Cari ban, oli, atau sparepart...',
            allProducts: 'Semua produk',
            serviceCategory: 'Layanan servis',
            resetFilters: 'Reset filter',
            bookProduct: 'Booking produk',
            addToCart: 'Tambah ke keranjang',
            stockLeft: 'Stok',
            outOfStock: 'Habis',
            productsPreparing: 'Produk sedang disiapkan',
            productsPreparingDescription: 'Silakan kembali lagi untuk melihat katalog terbaru.',
            nearby: 'Dekat dengan Anda',
            findPitstop: 'Temukan pit stop',
            nearest: 'terdekat.',
            searchStores: 'Cari kota atau nama outlet...',
            bookHere: 'Booking di sini',
            readyToRide: 'Siap jalan lebih jauh?',
            bikeStartsHere: 'Motor prima dimulai di sini.',
            planVisit: 'Atur jadwal servis Anda. Kami siapkan teknisi dan outlet pilihan.',
            bookingNow: 'Booking sekarang',
            bookingEyebrow: 'Atur jadwal kunjungan',
            bookingTitle: 'Booking servis & produk',
            bookingDescription: 'Pilih outlet dan waktu yang paling nyaman untuk Anda.',
            fullName: 'Nama lengkap',
            namePlaceholder: 'Nama Anda',
            phone: 'No. WhatsApp / HP',
            phonePlaceholder: '081234567890',
            closeBooking: 'Tutup form booking',
            chooseStore: 'Pilih outlet cabang',
            chooseStorePlaceholder: 'Pilih lokasi outlet',
            chooseProduct: 'Pilih produk',
            optional: '(opsional)',
            serviceOnly: 'Hanya servis / tanpa produk',
            visitDate: 'Tanggal kunjungan',
            visitTime: 'Jam kunjungan',
            cancel: 'Batal',
            confirmBooking: 'Konfirmasi booking',
            sending: 'Mengirim...',
            paymentMethod: 'Metode pembayaran',
            paymentCash: 'Bayar di outlet · Tunai',
            paymentCashDescription: 'Pembayaran dilakukan saat kunjungan.',
            paymentQris: 'Bayar di outlet · QRIS',
            paymentQrisDescription: 'Scan QRIS yang tersedia di outlet.',
            paymentBankBca: 'Transfer bank · BCA',
            paymentBankMandiri: 'Transfer bank · Mandiri',
            paymentBankDescription: 'Detail rekening dikonfirmasi oleh tim outlet.',
            paymentGopay: 'E-Wallet · GoPay',
            paymentOvo: 'E-Wallet · OVO',
            paymentDana: 'E-Wallet · DANA',
            paymentWalletDescription: 'Instruksi pembayaran diberikan setelah booking dikonfirmasi.',
            paymentPendingNotice: 'Pilihan tersimpan sebagai metode pembayaran. Pembayaran belum diproses otomatis dan masih menunggu konfirmasi.',
            limitedStock: 'Stok terbatas',
            officialWarranty: 'Garansi resmi',
            originalProducts: 'Produk original 100%',
            freeConsultation: 'Konsultasi gratis',
            chatWithExperts: 'Tanya mekanik ahli',
            liveActivity: 'Aktivitas terbaru',
            liveDemo: 'Simulasi aktivitas pelanggan',
            addedCart: 'menambahkan produk ke keranjang',
            bookedService: 'booking servis di outlet',
            boughtTires: 'membeli ban motor',
            customerNames: ['Rizky dari Bandung', 'Nadia dari Jakarta', 'Dimas dari Surabaya'],
        },
        en: {
            navServices: 'Services',
            navCatalog: 'Catalog',
            navStores: 'Find a store',
            navBookingStatus: 'My booking',
            login: 'Sign in',
            accountMenu: 'Account menu',
            myAccount: 'My account',
            adminDashboard: 'Admin dashboard',
            logout: 'Sign out',
            cart: 'Cart',
            openNavigation: 'Open navigation',
            switchLight: 'Switch to light mode',
            switchDark: 'Switch to dark mode',
            mobileCatalog: 'Product catalog',
            mobileBookingStatus: 'Check booking status',
            bookingService: 'Book a service',
            openToday: 'OPEN TODAY',
            serviceAvailable: 'Service available',
            heroEyebrow: 'Your ride, refined.',
            heroTitle: 'Ride at your best,',
            heroSubtitle: 'without compromise.',
            heroDescription: 'Precision care, trusted products, and expert mechanics. Everything your motorcycle needs, in one pit stop.',
            bookNow: 'Book a service',
            browseProducts: 'Explore products',
            happyCustomers: 'Happy customers',
            branches: 'Branches',
            completedServices: 'Services completed',
            serviceIntro: 'Made for your journey',
            serviceHeadline: 'One pit stop.',
            serviceHeadlineMuted: 'Everything handled.',
            serviceDescription: 'Consistent service and motorcycle essentials, handled by professional technicians at a nearby store.',
            tires: 'Motorcycle tires',
            tiresDescription: 'Maximum grip, ready for every road.',
            oils: 'Oil & lubricants',
            oilsDescription: 'A smoother engine, peak performance.',
            spareparts: 'Spare parts',
            sparepartsDescription: 'Hand-picked parts for a better ride.',
            serviceBooking: 'Service booking',
            serviceBookingDescription: 'Choose your time and store, skip the long wait.',
            seeOptions: 'Explore options',
            setSchedule: 'Set a time',
            catalogIntro: 'Picked for your motorcycle',
            catalogHeadline: 'Upgrade your ride.',
            catalogHeadlineMuted: 'Start right here.',
            searchProducts: 'Search tires, oil, or spare parts...',
            allProducts: 'All products',
            serviceCategory: 'Service packages',
            resetFilters: 'Reset filters',
            bookProduct: 'Book this product',
            addToCart: 'Add to cart',
            stockLeft: 'Stock',
            outOfStock: 'Sold out',
            productsPreparing: 'Products are on the way',
            productsPreparingDescription: 'Check back soon for our latest catalog.',
            nearby: 'Close to you',
            findPitstop: 'Find your nearest',
            nearest: 'pit stop.',
            searchStores: 'Search city or store name...',
            bookHere: 'Book at this store',
            readyToRide: 'Ready to go further?',
            bikeStartsHere: 'A better ride starts here.',
            planVisit: 'Book a service. We will prepare the right technician and store for you.',
            bookingNow: 'Book now',
            bookingEyebrow: 'Plan your visit',
            bookingTitle: 'Service & product booking',
            bookingDescription: 'Choose a store and time that works best for you.',
            fullName: 'Full name',
            namePlaceholder: 'Your name',
            phone: 'WhatsApp / Phone',
            phonePlaceholder: '081234567890',
            closeBooking: 'Close booking form',
            chooseStore: 'Choose a store',
            chooseStorePlaceholder: 'Select a store',
            chooseProduct: 'Choose a product',
            optional: '(optional)',
            serviceOnly: 'Service only / no product',
            visitDate: 'Visit date',
            visitTime: 'Visit time',
            cancel: 'Cancel',
            confirmBooking: 'Confirm booking',
            sending: 'Sending...',
            paymentMethod: 'Payment method',
            paymentCash: 'Pay in store · Cash',
            paymentCashDescription: 'Payment is due during your visit.',
            paymentQris: 'Pay in store · QRIS',
            paymentQrisDescription: 'Scan the QRIS available at the store.',
            paymentBankBca: 'Bank transfer · BCA',
            paymentBankMandiri: 'Bank transfer · Mandiri',
            paymentBankDescription: 'Bank details will be confirmed by the store team.',
            paymentGopay: 'E-Wallet · GoPay',
            paymentOvo: 'E-Wallet · OVO',
            paymentDana: 'E-Wallet · DANA',
            paymentWalletDescription: 'Payment instructions will be shared after booking confirmation.',
            paymentPendingNotice: 'Your selection is saved as the payment method. No payment is processed automatically; confirmation is still pending.',
            limitedStock: 'Limited stock',
            officialWarranty: 'Official warranty',
            originalProducts: '100% genuine products',
            freeConsultation: 'Free consultation',
            chatWithExperts: 'Ask an expert mechanic',
            liveActivity: 'Recent activity',
            liveDemo: 'Simulated customer activity',
            addedCart: 'added a product to their cart',
            bookedService: 'booked a service at',
            boughtTires: 'bought motorcycle tires',
            customerNames: ['Rizky from Bandung', 'Nadia from Jakarta', 'Dimas from Surabaya'],
        },
    },

    t(key) {
        return this.translations[this.locale][key] ?? key;
    },

    setLanguage(locale) {
        if (locale !== 'id' && locale !== 'en') {
            console.error('Bahasa yang dipilih tidak didukung.', locale);
            return;
        }

        this.locale = locale;
        this.languageMenuOpen = false;
        document.documentElement.lang = locale;

        try {
            localStorage.setItem('planet-ban-locale', locale);
        } catch (error) {
            console.error('Preferensi bahasa tidak dapat disimpan.', error);
        }
    },

    toggleTheme() {
        this.isDark = !this.isDark;
        this.applyTheme();

        try {
            localStorage.setItem('planet-ban-theme', this.isDark ? 'dark' : 'light');
        } catch (error) {
            console.error('Preferensi tema tidak dapat disimpan.', error);
        }
    },

    applyTheme() {
        document.documentElement.classList.toggle('dark', this.isDark);
        document.documentElement.classList.toggle('theme-light', !this.isDark);
        document.documentElement.style.colorScheme = this.isDark ? 'dark' : 'light';
        document.documentElement.lang = this.locale;
        const themeColor = document.querySelector('meta[name="theme-color"]');

        if (themeColor) {
            themeColor.content = this.isDark ? '#090d13' : '#f8fafc';
        }
    },

    get liveNotification() {
        const index = this.liveNotificationIndex;
        const names = this.translations[this.locale].customerNames;
        const actions = ['addedCart', 'bookedService', 'boughtTires'];
        const action = actions[index];
        const suffix = action === 'bookedService' ? ' Planet Ban' : '';

        return `${names[index]} ${this.t(action)}${suffix}`;
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
