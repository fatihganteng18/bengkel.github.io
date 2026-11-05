<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#172554">
    <title>Daftar Akun - Arisa Knalpot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
    <main class="mx-auto flex min-h-screen max-w-7xl items-center justify-center p-4 sm:p-8">
        <section class="grid w-full max-w-5xl overflow-hidden rounded-3xl border border-white/70 bg-white shadow-2xl md:min-h-[700px] md:grid-cols-2">
            <aside class="relative hidden flex-col justify-between overflow-hidden bg-blue-950 p-10 text-white md:flex lg:p-12">
                <div class="absolute -right-24 -top-20 h-80 w-80 rounded-full bg-blue-800/60 blur-3xl"></div>
                <div class="absolute -bottom-28 -left-20 h-80 w-80 rounded-full bg-red-600/20 blur-3xl"></div>

                <a href="{{ route('home') }}" class="relative inline-flex w-fit items-center rounded-xl bg-white/10 px-4 py-2 text-lg font-black italic tracking-wide text-white ring-1 ring-white/15">
                    Arisa <span class="ml-1 text-lime-300">Knalpot</span>
                </a>

                <div class="relative">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-blue-100">
                        <svg class="h-4 w-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5-4.5a12 12 0 01-8-3 12 12 0 01-8 3v5c0 5.2 3.4 9.5 8 11 4.6-1.5 8-5.8 8-11v-5z"/>
                        </svg>
                        Akun pribadi Anda, dalam satu tempat
                    </span>
                    <h1 class="mt-6 text-4xl font-black leading-tight tracking-tight lg:text-5xl">
                        Lebih mudah<br>
                        upgrade <span class="text-lime-300">mobil.</span>
                    </h1>
                    <p class="mt-5 max-w-sm text-sm leading-7 text-blue-100/80">
                        Buat akun untuk booking workshop, menyimpan detail kontak, dan mengikuti status pesanan knalpot.
                    </p>
                    <div class="mt-10 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                            <p class="text-sm font-bold">Booking lebih praktis</p>
                            <p class="mt-1 text-xs leading-5 text-blue-100/70">Jadwalkan kunjungan tanpa repot.</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                            <p class="text-sm font-bold">Status terpantau</p>
                            <p class="mt-1 text-xs leading-5 text-blue-100/70">Lihat riwayat servis Anda.</p>
                        </div>
                    </div>
                </div>

                <p class="relative text-xs text-blue-100/60">&copy; {{ date('Y') }} Arisa Knalpot.</p>
            </aside>

            <div class="flex items-center justify-center p-6 sm:p-10 lg:p-12">
                <div class="w-full max-w-md">
                    <div class="mb-7 flex items-center justify-between md:hidden">
                        <a href="{{ route('home') }}" class="rounded-lg bg-blue-950 px-3 py-1.5 text-base font-black italic text-white">
                            Arisa <span class="text-lime-300">Knalpot</span>
                        </a>
                        <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-500 hover:text-blue-900">Beranda</a>
                    </div>

                    <div class="mb-7">
                        <p class="text-sm font-bold uppercase tracking-[0.18em] text-red-600">Mulai sekarang</p>
                        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Buat akun baru</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Isi data berikut untuk mulai booking servis.</p>
                    </div>

                    <form action="{{ route('register.store') }}" method="POST" x-data="registerForm" @submit.prevent="submit($event)" class="space-y-4">
                        @csrf

                        <div>
                            <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nama lengkap</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required maxlength="255" placeholder="Nama sesuai identitas"
                                @class([
                                    'w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-900 focus:ring-4 focus:ring-blue-900/10',
                                    'border-red-400' => $errors->has('name'),
                                    'border-slate-200' => ! $errors->has('name'),
                                ])>
                            @error('name')
                                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Alamat email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required maxlength="255" placeholder="nama@email.com"
                                @class([
                                    'w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-900 focus:ring-4 focus:ring-blue-900/10',
                                    'border-red-400' => $errors->has('email'),
                                    'border-slate-200' => ! $errors->has('email'),
                                ])>
                            @error('email')
                                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="mb-2 block text-sm font-semibold text-slate-700">Nomor HP / WhatsApp</label>
                            <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" inputmode="tel" required minlength="8" maxlength="20" pattern="[0-9+\x28\x29\x20\x2D]{8,20}" title="Gunakan 8–20 karakter berupa angka, spasi, +, tanda kurung, atau tanda hubung." placeholder="08xx xxxx xxxx"
                                @class([
                                    'w-full rounded-xl border bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-900 focus:ring-4 focus:ring-blue-900/10',
                                    'border-red-400' => $errors->has('phone'),
                                    'border-slate-200' => ! $errors->has('phone'),
                                ])>
                            @error('phone')
                                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Kata sandi</label>
                            <div class="relative">
                                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" x-model="password" autocomplete="new-password" required minlength="8" pattern="(?=.*[A-Za-z])(?=.*[0-9]).{8,}" title="Kata sandi harus memiliki minimal 8 karakter serta mengandung huruf dan angka." placeholder="Minimal 8 karakter, huruf dan angka"
                                    @class([
                                        'w-full rounded-xl border bg-white py-3 pl-4 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-900 focus:ring-4 focus:ring-blue-900/10',
                                        'border-red-400' => $errors->has('password'),
                                        'border-slate-200' => ! $errors->has('password'),
                                    ])>
                                <button type="button" @click="showPassword = !showPassword" :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                    class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-blue-900">
                                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8m-8.5 2.1C3.2 13.9 2.5 12 2.5 12s3.5-7 9.5-7c1.3 0 2.5.3 3.6.8M20.5 8.5c.7 1 1 1.8 1 1.8s-3.5 7-9.5 7c-.8 0-1.6-.1-2.3-.3"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="mt-3" aria-live="polite">
                                <div class="mb-2 flex gap-1.5" aria-label="Kekuatan kata sandi">
                                    <template x-for="level in 3" :key="level">
                                        <span class="h-1.5 flex-1 rounded-full transition-colors"
                                            :class="passwordStrength >= level ? (passwordStrength === 3 ? 'bg-emerald-500' : passwordStrength === 2 ? 'bg-yellow-400' : 'bg-red-400') : 'bg-slate-200'"></span>
                                    </template>
                                </div>
                                <div class="flex flex-wrap gap-x-4 gap-y-1">
                                    <template x-for="check in passwordChecks" :key="check.label">
                                        <span class="inline-flex items-center gap-1 text-[11px]" :class="check.passed ? 'text-emerald-700' : 'text-slate-400'">
                                            <span x-text="check.passed ? '✓' : '•'"></span>
                                            <span x-text="check.label"></span>
                                        </span>
                                    </template>
                                </div>
                            </div>
                            @error('password')
                                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">Ulangi kata sandi</label>
                            <div class="relative">
                                <input id="password_confirmation" :type="showPasswordConfirmation ? 'text' : 'password'" name="password_confirmation" x-model="passwordConfirmation" autocomplete="new-password" required minlength="8" placeholder="Ketik ulang kata sandi" @input="passwordConfirmationError = false"
                                    class="w-full rounded-xl border border-slate-200 bg-white py-3 pl-4 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-900 focus:ring-4 focus:ring-blue-900/10">
                                <button type="button" @click="showPasswordConfirmation = !showPasswordConfirmation" :aria-label="showPasswordConfirmation ? 'Sembunyikan konfirmasi kata sandi' : 'Tampilkan konfirmasi kata sandi'"
                                    class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-blue-900">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                                    </svg>
                                </button>
                            </div>
                            <p x-show="passwordConfirmationError" x-cloak role="alert" class="mt-1.5 text-xs font-medium text-red-600">Konfirmasi password belum sama.</p>
                        </div>

                        <button type="submit" :disabled="isSubmitting"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-yellow-400 px-5 py-3.5 text-sm font-extrabold text-blue-950 shadow-lg shadow-yellow-400/20 transition hover:-translate-y-0.5 hover:bg-yellow-300 disabled:cursor-wait disabled:opacity-70">
                            <svg x-show="isSubmitting" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Membuat akun...' : 'Buat Akun'"></span>
                        </button>
                    </form>

                    <p class="mt-7 text-center text-sm text-slate-500">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-bold text-blue-900 transition hover:text-red-600">Masuk</a>
                    </p>
                    <a href="{{ route('home') }}" class="mt-5 block text-center text-xs font-semibold text-slate-400 transition hover:text-blue-900">&larr; Kembali ke beranda</a>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
