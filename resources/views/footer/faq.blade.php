<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Planet Ban</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen font-sans">

    <!-- Header Sederhana -->
    <header class="bg-white shadow-xs border-b border-gray-100 py-4 px-6 md:px-12 flex justify-between items-center">
        <a href="/home" class="bg-blue-900 text-white font-black italic px-3 py-1 rounded-lg text-lg">
            PLANET <span class="text-yellow-400">BAN</span>
        </a>
        <a href="/home" class="text-sm font-bold text-blue-900 hover:underline">&larr; Kembali ke Beranda</a>
    </header>

    <!-- Konten Halaman -->
    <main class="container mx-auto px-4 md:px-12 py-12 flex-grow max-w-4xl">
        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-sm border border-gray-200 space-y-6">
            <h1 class="text-3xl font-black text-gray-900">Tentang Kami</h1>
            <p class="text-sm text-gray-600 leading-relaxed">
                Bagaimana cara melakukan booking? Pilih produk atau layanan, tentukan outlet dan jadwal, lalu kirim permintaan booking. Tim outlet akan menghubungi Anda untuk konfirmasi jadwal dan pembayaran.
            </p>
            <div class="border-t border-gray-100 pt-6">
                <h3 class="font-bold text-gray-900 mb-2">Kantor Pusat</h3>
                <p class="text-xs text-gray-500">Jl. H. Dimun Raya BBM No. 109-110 Sukamaju, Cilodong - Depok, Jawa Barat, 16415.</p>
            </div>
        </div>
    </main>

    @include('components.site-footer')
</body>
</html>