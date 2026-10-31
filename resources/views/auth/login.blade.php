<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#172554">
    <title>Masuk - Planet Ban</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
    <main class="mx-auto flex min-h-screen max-w-7xl items-center justify-center p-4 sm:p-8">
        <section class="grid w-full max-w-5xl overflow-hidden rounded-3xl border border-white/70 bg-white shadow-2xl md:min-h-[640px] md:grid-cols-2">
            <aside class="relative hidden flex-col justify-between overflow-hidden bg-blue-950 p-10 text-white md:flex lg:p-12">
                <div class="absolute -right-24 -top-20 h-80 w-80 rounded-full bg-blue-800/60 blur-3xl"></div>
                <div class="absolute -bottom-28 -left-20 h-80 w-80 rounded-full bg-red-600/20 blur-3xl"></div>

                <a href="{{ route('home') }}" class="relative inline-flex w-fit items-center rounded-xl bg-white/10 px-4 py-2 text-lg font-black italic tracking-wide text-white ring-1 ring-white/15">
                    PLANET <span class="ml-1 text-yellow-400">BAN</span>
                </a>

                <div class="relative">
                    <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-blue-100">
                        <svg class="h-4 w-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m5-4.5a12 12 0 01-8-3 12 12 0 01-8 3v5c0 5.2 3.4 9.5 8 11 4.6-1.5 8-5.8 8-11v-5z"/>
                        </svg>
                        Perjalanan lebih tenang dimulai di sini
                    </span>
                    <h1 class="mt-6 text-4xl font-black leading-tight tracking-tight lg:text-5xl">
                        Kendaraan prima,<br>
                        perjalanan <span class="text-yellow-400">nyaman.</span>
                    </h1>
                    <p class="mt-5 max-w-sm text-sm leading-7 text-blue-100/80">
                        Masuk untuk mengatur booking servis, memantau status kunjungan, dan menemukan kebutuhan motor Anda.
                    </p>

                    <div class="mt-10 flex items-center gap-4 rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-yellow-400 text-blue-950">
                            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M5 17h14l-1.5-6.5a2 2 0 00-2-1.5h-7a2 2 0 00-2 1.5L5 17zm0 0a2 2 0 104 0m10 0a2 2 0 11-4 0M8 9l1-3h6l1 3"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-bold">Servis praktis, dekat dengan Anda</p>
                            <p class="mt-1 text-xs text-blue-100/70">Pilih outlet dan waktu kunjungan sesuai kebutuhan.</p>
                        </div>
                    </div>
                </div>

                <p class="relative text-xs text-blue-100/60">&copy; {{ date('Y') }} Planet Ban. Berkendara dengan lebih percaya diri.</p>
            </aside>

            <div class="flex items-center justify-center p-6 sm:p-10 lg:p-12">
                <div class="w-full max-w-md">
                    <div class="mb-8 flex items-center justify-between md:hidden">
                        <a href="{{ route('home') }}" class="rounded-lg bg-blue-950 px-3 py-1.5 text-base font-black italic text-white">
                            PLANET <span class="text-yellow-400">BAN</span>
                        </a>
                        <a href="{{ route('home') }}" class="text-sm font-semibold text-slate-500 hover:text-blue-900">Beranda</a>
                    </div>

                    <div class="mb-8">
                        <p class="text-sm font-bold uppercase tracking-[0.18em] text-red-600">Selamat datang kembali</p>
                        <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-950">Masuk ke akun Anda</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Masukkan email dan kata sandi untuk melanjutkan.</p>
                    </div>

                    @if (session('status'))
                        <div role="status" class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('success'))
                        <div role="status" class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('login.store') }}" method="POST" x-data="authForm" @submit.prevent="submit($event)" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Alamat email</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                autocomplete="email"
                                required
                                autofocus
                                placeholder="nama@email.com"
                                aria-describedby="email-error"
                                @class([
                                    'w-full rounded-xl border bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-900 focus:ring-4 focus:ring-blue-900/10',
                                    'border-red-400' => $errors->has('email'),
                                    'border-slate-200' => ! $errors->has('email'),
                                ])
                            >
                            @error('email')
                                <p id="email-error" class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="mb-2 flex items-center justify-between">
                                <label for="password" class="text-sm font-semibold text-slate-700">Kata sandi</label>
                                <a href="{{ route('password.request') }}" class="text-xs font-bold text-blue-900 transition hover:text-red-600">Lupa password?</a>
                            </div>
                            <div class="relative">
                                <input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    name="password"
                                    autocomplete="current-password"
                                    required
                                    placeholder="Masukkan kata sandi"
                                    aria-describedby="password-error"
                                    @class([
                                        'w-full rounded-xl border bg-white py-3.5 pl-4 pr-12 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-900 focus:ring-4 focus:ring-blue-900/10',
                                        'border-red-400' => $errors->has('password'),
                                        'border-slate-200' => ! $errors->has('password'),
                                    ])
                                >
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                    class="absolute inset-y-0 right-0 flex items-center px-4 text-slate-400 transition hover:text-blue-900"
                                >
                                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7z"/>
                                        <circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 3l18 18M10.6 10.6a2 2 0 002.8 2.8m-8.5 2.1C3.2 13.9 2.5 12 2.5 12s3.5-7 9.5-7c1.3 0 2.5.3 3.6.8M20.5 8.5c.7 1 1 1.8 1 1.8s-3.5 7-9.5 7c-.8 0-1.6-.1-2.3-.3"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p id="password-error" class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <label class="flex items-center gap-2.5 text-sm text-slate-600">
                            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 accent-blue-950">
                            Ingat saya di perangkat ini
                        </label>

                        <button
                            type="submit"
                            :disabled="isSubmitting"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-950 px-5 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-950/15 transition hover:-translate-y-0.5 hover:bg-blue-900 disabled:cursor-wait disabled:opacity-70"
                        >
                            <svg x-show="isSubmitting" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Memeriksa akun...' : 'Masuk'"></span>
                            <span x-show="!isSubmitting" aria-hidden="true">&rarr;</span>
                        </button>
                    </form>

                    <p class="mt-8 text-center text-sm text-slate-500">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="font-bold text-blue-900 transition hover:text-red-600">Daftar gratis</a>
                    </p>

                    <a href="{{ route('home') }}" class="mt-6 block text-center text-xs font-semibold text-slate-400 transition hover:text-blue-900">
                        &larr; Kembali ke beranda
                    </a>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
