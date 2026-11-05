<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Baru - Arisa Knalpot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100 px-4 py-10 font-sans text-slate-800 antialiased">
    <main class="w-full max-w-lg rounded-3xl border border-white/70 bg-white p-7 shadow-2xl sm:p-10">
        <a href="{{ route('home') }}" class="inline-flex rounded-lg bg-blue-950 px-3 py-1.5 text-base font-black italic text-white">
            Arisa <span class="ml-1 text-lime-300">Knalpot</span>
        </a>

        <h1 class="mt-8 text-3xl font-black tracking-tight text-slate-950">Buat password baru</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan password yang kuat dan belum pernah Anda pakai sebelumnya.</p>

        <form action="{{ route('password.update') }}" method="POST" x-data="authForm" @submit.prevent="submit($event)" class="mt-7 space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div>
                <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Alamat email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $email) }}" autocomplete="email" required
                    class="w-full rounded-xl border border-slate-200 px-4 py-3.5 text-sm outline-none transition focus:border-blue-900 focus:ring-4 focus:ring-blue-900/10">
                @error('email')
                    <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="mb-2 block text-sm font-semibold text-slate-700">Password baru</label>
                <div class="relative">
                    <input id="password" :type="showPassword ? 'text' : 'password'" name="password" autocomplete="new-password" required minlength="8" pattern="(?=.*[A-Za-z])(?=.*[0-9]).{8,}" title="Password harus memiliki minimal 8 karakter serta mengandung huruf dan angka."
                        class="w-full rounded-xl border border-slate-200 py-3.5 pl-4 pr-12 text-sm outline-none transition focus:border-blue-900 focus:ring-4 focus:ring-blue-900/10">
                    <button type="button" @click="showPassword = !showPassword" :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'" class="absolute inset-y-0 right-0 px-4 text-slate-400 hover:text-blue-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-slate-700">Ulangi password baru</label>
                <div class="relative">
                    <input id="password_confirmation" :type="showConfirmation ? 'text' : 'password'" name="password_confirmation" autocomplete="new-password" required minlength="8"
                        class="w-full rounded-xl border border-slate-200 py-3.5 pl-4 pr-12 text-sm outline-none transition focus:border-blue-900 focus:ring-4 focus:ring-blue-900/10">
                    <button type="button" @click="showConfirmation = !showConfirmation" :aria-label="showConfirmation ? 'Sembunyikan konfirmasi password' : 'Tampilkan konfirmasi password'" class="absolute inset-y-0 right-0 px-4 text-slate-400 hover:text-blue-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.5 12s3.5-7 9.5-7 9.5 7 9.5 7-3.5 7-9.5 7-9.5-7-9.5-7z"/><circle cx="12" cy="12" r="3" stroke-width="1.8"/>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" :disabled="isSubmitting" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-950 px-5 py-3.5 text-sm font-bold text-white shadow-lg transition hover:bg-blue-900 disabled:cursor-wait disabled:opacity-70">
                <svg x-show="isSubmitting" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan password baru'"></span>
            </button>
        </form>
    </main>
</body>
</html>
