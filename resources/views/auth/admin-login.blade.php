<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Planet Ban</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-800 antialiased">
    <header class="border-b border-slate-100 bg-white px-5 py-4 shadow-sm sm:px-8">
        <div class="mx-auto flex max-w-7xl items-center justify-between">
            <a href="{{ route('home') }}" class="rounded-lg bg-blue-950 px-3 py-1.5 text-lg font-black italic text-white shadow">
                PLANET <span class="text-yellow-400">BAN</span>
            </a>
            <a href="{{ route('home') }}" class="text-sm font-bold text-blue-950 transition hover:text-blue-700">&larr; Kembali ke Beranda</a>
        </div>
    </header>

    <main class="flex flex-1 items-center justify-center px-4 py-12">
        <div class="w-full max-w-md rounded-3xl border border-slate-100 bg-white p-8 text-center shadow-2xl sm:p-10">
            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-950 text-yellow-400 shadow-lg">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-7a2 2 0 00-2-2H6a2 2 0 00-2 2v7a2 2 0 002 2zm10-11V7a4 4 0 00-8 0v3h8z"/>
                </svg>
            </div>

            <h1 class="text-2xl font-black text-slate-900">Login Admin</h1>
            <p class="mt-2 text-sm text-slate-500">Masuk untuk mengelola booking Planet Ban.</p>

            @if ($errors->any())
                <div role="alert" class="mt-6 rounded-xl border border-red-200 bg-red-50 p-3 text-left text-sm font-semibold text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('admin.login.submit') }}" method="POST" class="mt-8 space-y-5 text-left">
                @csrf
                <div>
                    <label for="admin-email" class="mb-2 block text-sm font-semibold text-slate-700">Email admin</label>
                    <input id="admin-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" autofocus
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm transition placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-4 focus:ring-blue-900/10">
                </div>
                <div>
                    <label for="admin-password" class="mb-2 block text-sm font-semibold text-slate-700">Password admin</label>
                    <input id="admin-password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan password admin"
                        class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm transition placeholder:text-slate-400 focus:border-blue-900 focus:outline-none focus:ring-4 focus:ring-blue-900/10">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 accent-blue-950">
                    Ingat saya di perangkat ini
                </label>

                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-blue-950 px-5 py-3.5 text-sm font-bold text-white shadow-lg transition hover:bg-blue-900">
                    Masuk ke Dashboard Admin <span aria-hidden="true">&rarr;</span>
                </button>
            </form>

            <a href="{{ route('login') }}" class="mt-6 inline-block text-sm font-semibold text-blue-900 transition hover:text-blue-700">
                Masuk sebagai pelanggan
            </a>
        </div>
    </main>

    <footer class="border-t border-slate-100 bg-white px-4 py-5 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Planet Ban.
    </footer>
</body>
</html>
