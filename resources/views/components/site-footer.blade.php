<footer class="mt-20 border-t-4 border-yellow-400 bg-blue-950 text-white">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-4 py-12 sm:grid-cols-2 sm:px-6 lg:grid-cols-5 lg:px-8 lg:py-16">
        <section class="space-y-5 sm:col-span-2 lg:col-span-1" aria-labelledby="footer-brand">
            <a href="{{ route('home') }}" class="inline-flex rounded-lg bg-slate-900 px-3 py-2 text-base font-black italic tracking-wide text-white transition hover:bg-slate-800">
                Arisa <span class="ml-1 text-lime-300">Knalpot</span>
            </a>
            <div class="space-y-2 text-sm leading-relaxed text-blue-100">
                <h2 id="footer-brand" class="font-bold text-white">Workshop Knalpot Mobil</h2>
                <p>Custom exhaust, fabrication, dan konsultasi setup performa untuk daily drive sampai weekend run.</p>
            </div>
            <div class="flex items-center gap-3" aria-label="Media sosial Arisa Knalpot">
                <a href="https://instagram.com/fthidilll" target="_blank" rel="noopener noreferrer" aria-label="Instagram Arisa Knalpot" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-lime-300 hover:text-blue-950">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="5"></rect>
                        <circle cx="12" cy="12" r="4"></circle>
                        <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"></circle>
                    </svg>
                </a>
                <a href="https://tiktok.com/@mcidilll_" target="_blank" rel="noopener noreferrer" aria-label="TikTok Arisa Knalpot" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-lime-300 hover:text-blue-950">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M19.6 7.1a6.8 6.8 0 0 1-4.1-1.4v8.1a6.2 6.2 0 1 1-5.4-6.1v3.4a2.9 2.9 0 1 0 2.1 2.8V2h3.3c.2 2 1.6 3.7 3.6 4.1.2 0 .4.1.5.1v.9Z"></path>
                    </svg>
                </a>
            </div>
        </section>

        <nav aria-labelledby="footer-services">
            <h2 id="footer-services" class="mb-4 text-sm font-black uppercase tracking-wider text-lime-300">Layanan</h2>
            <ul class="space-y-3 text-sm text-blue-100">
                <li><a href="{{ route('home') }}#katalog" class="transition hover:text-white">Full system</a></li>
                <li><a href="{{ route('home') }}#katalog" class="transition hover:text-white">Silencer & resonator</a></li>
                <li><a href="{{ route('home') }}#katalog" class="transition hover:text-white">Header & downpipe</a></li>
                <li><a href="{{ route('home') }}#beranda" class="transition hover:text-white">Custom valvetronic</a></li>
            </ul>
        </nav>

        <nav aria-labelledby="footer-help">
            <h2 id="footer-help" class="mb-4 text-sm font-black uppercase tracking-wider text-lime-300">Informasi & bantuan</h2>
            <ul class="space-y-3 text-sm text-blue-100">
                <li><a href="{{ route('booking.status') }}" class="transition hover:text-white">Cek Status Booking</a></li>
                <li><a href="{{ route('stores.index') }}" class="transition hover:text-white">Lokasi Toko</a></li>
                <li><a href="{{ route('about') }}" class="transition hover:text-white">Tentang Kami</a></li>
                <li><a href="{{ route('blog') }}" class="transition hover:text-white">Blog</a></li>
            </ul>
        </nav>

        <nav aria-labelledby="footer-legal">
            <h2 id="footer-legal" class="mb-4 text-sm font-black uppercase tracking-wider text-lime-300">Legal & kebijakan</h2>
            <ul class="space-y-3 text-sm text-blue-100">
                <li><a href="{{ route('privacy') }}" class="transition hover:text-white">Kebijakan Privasi</a></li>
                <li><a href="{{ route('terms') }}" class="transition hover:text-white">Syarat & Ketentuan</a></li>
                <li><a href="{{ route('copyright') }}" class="transition hover:text-white">Kebijakan Hak Cipta</a></li>
            </ul>
        </nav>

        <section aria-labelledby="footer-contact">
            <h2 id="footer-contact" class="mb-4 text-sm font-black uppercase tracking-wider text-lime-300">Hubungi kami</h2>
            <ul class="space-y-3 text-sm text-blue-100">
                <li><a href="{{ route('stores.index') }}" class="transition hover:text-white">Lihat lokasi workshop</a></li>
                <li>Booking konsultasi langsung melalui katalog produk.</li>
                <li class="pt-1">
                    <span class="block font-bold text-white">Jam operasional</span>
                    <span>Senin–Sabtu, 08.00–17.00 WIB</span>
                </li>
                <li class="pt-1">
                    <span class="block font-bold text-white">Pengaduan konsumen RI</span>
                    <span>Ditjen PKTN Kemendag</span>
                    <a href="https://wa.me/6285311111010" target="_blank" rel="noopener noreferrer" class="block transition hover:text-white">WhatsApp: 0853-1111-1010</a>
                </li>
            </ul>
        </section>
    </div>

    <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-6 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-blue-200">Opsi pembayaran</p>
                <ul class="mt-3 flex flex-wrap gap-2" aria-label="Opsi pembayaran">
                    <li class="rounded-lg border border-white/15 bg-white/5 px-3 py-1.5 text-xs font-semibold text-white">Cash</li>
                    <li class="rounded-lg border border-white/15 bg-white/5 px-3 py-1.5 text-xs font-semibold text-white">QRIS</li>
                    <li class="rounded-lg border border-white/15 bg-white/5 px-3 py-1.5 text-xs font-semibold text-white">BCA / Mandiri</li>
                    <li class="rounded-lg border border-white/15 bg-white/5 px-3 py-1.5 text-xs font-semibold text-white">GoPay / OVO / DANA</li>
                </ul>
            </div>
            <p class="text-xs leading-relaxed text-blue-200 lg:max-w-sm lg:text-right">
                Pilihan pembayaran tersimpan saat booking. Pembayaran transfer dan e-wallet menunggu instruksi serta konfirmasi tim outlet.
            </p>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-5 text-xs text-blue-200 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <p>&copy; {{ now()->year }} Arisa Knalpot. Hak cipta dilindungi.</p>
            <nav aria-label="Tautan legal" class="flex flex-wrap gap-x-5 gap-y-2 font-semibold">
                <a href="{{ route('privacy') }}" class="transition hover:text-white">Kebijakan Privasi</a>
                <a href="{{ route('terms') }}" class="transition hover:text-white">Syarat & Ketentuan</a>
                <a href="{{ route('copyright') }}" class="transition hover:text-white">Kebijakan Hak Cipta</a>
                <a href="{{ route('faq') }}" class="transition hover:text-white">FAQ</a>
            </nav>
        </div>
    </div>
</footer>
