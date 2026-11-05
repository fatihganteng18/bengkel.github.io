<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Arisa Knalpot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-slate-100 px-4 py-10 font-sans text-slate-800 antialiased">
    <main class="w-full max-w-lg rounded-3xl border border-white/70 bg-white p-7 shadow-2xl sm:p-10">
        <a href="{{ route('home') }}" class="inline-flex rounded-lg bg-blue-950 px-3 py-1.5 text-base font-black italic text-white">
            Arisa <span class="ml-1 text-lime-300">Knalpot</span>
        </a>

        <div class="mt-8 flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-blue-950">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-7a2 2 0 00-2-2H6a2 2 0 00-2 2v7a2 2 0 002 2zm10-11V7a4 4 0 00-8 0v3h8z"/>
            </svg>
        </div>
        <h1 class="mt-5 text-3xl font-black tracking-tight text-slate-950">Atur ulang password</h1>
        <p class="mt-2 text-sm leading-6 text-slate-500">Masukkan email akun Anda. Kami akan mengirim tautan untuk membuat password baru.</p>

        @if (session('status'))
            <div role="status" class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" x-data="authForm" @submit.prevent="submit($event)" class="mt-7 space-y-5">
            @csrf
            <div>
                <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Alamat email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="nama@email.com"
                    @class([
                        'w-full rounded-xl border bg-white px-4 py-3.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-blue-900 focus:ring-4 focus:ring-blue-900/10',
                        'border-red-400' => $errors->has('email'),
                        'border-slate-200' => ! $errors->has('email'),
                    ])>
                @error('email')
                    <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" :disabled="isSubmitting" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-950 px-5 py-3.5 text-sm font-bold text-white shadow-lg transition hover:bg-blue-900 disabled:cursor-wait disabled:opacity-70">
                <svg x-show="isSubmitting" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim tautan pemulihan'"></span>
            </button>
        </form>

        <a href="{{ route('login') }}" class="mt-7 block text-center text-sm font-bold text-blue-900 transition hover:text-red-600">&larr; Kembali ke halaman masuk</a>
    </main>
</body>
</html>
