<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>Tambah Produk | Arisa Knalpot</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-[260px_minmax(0,1fr)]">
        @include('components.admin-sidebar', ['activeMenu' => 'products'])

        <div class="min-w-0">
            <header class="flex items-center justify-between gap-4 border-b border-slate-200 bg-white px-4 py-4 sm:px-7 lg:px-9">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[.2em] text-cyan-700">ARISA KNALPOT · INVENTARIS</p>
                    <h1 class="mt-1 text-xl font-black tracking-tight text-slate-950 sm:text-2xl">Tambah Produk Baru</h1>
                </div>
                <div class="flex items-center gap-2 sm:gap-4">
                    <span class="hidden text-right sm:block"><span class="block text-xs font-bold text-slate-800">{{ Auth::user()->name }}</span><span class="mt-1 block text-[10px] text-slate-500">Administrator</span></span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="rounded-xl bg-slate-950 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-slate-800 active:scale-95">Keluar</button>
                    </form>
                </div>
            </header>

            <main class="mx-auto max-w-5xl space-y-6 p-4 sm:p-7 lg:p-9">
                @if(session('success'))
                    <div role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 font-semibold text-emerald-800">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div role="alert" class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-800">
                        <p class="font-bold">Produk belum dapat disimpan. Periksa kembali isian berikut:</p>
                        <ul class="mt-2 list-inside list-disc text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" x-data="{ productName: @js(old('name', '')), makeSlug(value) { return value.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '') } }" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    @csrf
                    <div class="border-b border-slate-200 px-5 py-5 sm:px-8">
                        <h2 class="text-lg font-black text-slate-950">Informasi produk</h2>
                        <p class="mt-1 text-sm text-slate-500">Lengkapi detail setup knalpot untuk menambahkannya ke katalog toko.</p>
                    </div>

                    <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-8">
                        <div class="sm:col-span-2">
                            <label for="name" class="mb-2 block text-sm font-bold text-slate-700">Nama produk <span class="text-rose-600">*</span></label>
                            <input id="name" name="name" type="text" x-model="productName" value="{{ old('name') }}" required maxlength="255" autocomplete="off" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm text-slate-900 outline-none transition focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100" placeholder="Contoh: Arisa Valvetronic Dual Mode 2.5 inch">
                            @error('name')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="slug-preview" class="mb-2 block text-sm font-bold text-slate-700">Slug produk <span class="text-xs font-normal text-slate-500">(otomatis dari nama)</span></label>
                            <input id="slug-preview" type="text" :value="makeSlug(productName)" readonly class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-500" aria-describedby="slug-help">
                            <p id="slug-help" class="mt-1 text-xs text-slate-500">Slug akan dibuat unik secara otomatis ketika disimpan.</p>
                        </div>

                        <div>
                            <label for="category" class="mb-2 block text-sm font-bold text-slate-700">Kategori <span class="text-rose-600">*</span></label>
                            <input id="category" type="text" value="Knalpot Mobil" readonly class="h-11 w-full rounded-xl border border-slate-200 bg-slate-50 px-3 text-sm text-slate-500">
                        </div>

                        <div>
                            <label for="sub_category" class="mb-2 block text-sm font-bold text-slate-700">Sub-kategori <span class="text-rose-600">*</span></label>
                            <select id="sub_category" name="sub_category" required class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                                <option value="">Pilih sub-kategori</option>
                                @foreach($subCategories as $subCategory)
                                    <option value="{{ $subCategory }}" @selected(old('sub_category') === $subCategory)>{{ $subCategory }}</option>
                                @endforeach
                            </select>
                            @error('sub_category')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="inlet_size" class="mb-2 block text-sm font-bold text-slate-700">Inlet / diameter <span class="text-rose-600">*</span></label>
                            <select id="inlet_size" name="inlet_size" required class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                                <option value="">Pilih diameter</option>
                                @foreach(['1.5 inch', '2.0 inch', '2.25 inch', '2.5 inch', '3.0 inch'] as $inlet)
                                    <option value="{{ $inlet }}" @selected(old('inlet_size') === $inlet)>{{ $inlet }}</option>
                                @endforeach
                            </select>
                            @error('inlet_size')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="sound_type" class="mb-2 block text-sm font-bold text-slate-700">Tipe suara <span class="text-rose-600">*</span></label>
                            <select id="sound_type" name="sound_type" required class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                                <option value="">Pilih karakter suara</option>
                                @foreach(['Suara Halus Standart', 'Bass Bulat / Adem', 'Gahar', 'Silent'] as $sound)
                                    <option value="{{ $sound }}" @selected(old('sound_type') === $sound)>{{ $sound }}</option>
                                @endforeach
                            </select>
                            @error('sound_type')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="material" class="mb-2 block text-sm font-bold text-slate-700">Bahan / material <span class="text-rose-600">*</span></label>
                            <select id="material" name="material" required class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                                <option value="">Pilih material</option>
                                @foreach(['Stainless Steel', 'Galvanis', 'Karbon'] as $material)
                                    <option value="{{ $material }}" @selected(old('material') === $material)>{{ $material }}</option>
                                @endforeach
                            </select>
                            @error('material')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="engine_cc" class="mb-2 block text-sm font-bold text-slate-700">Kapasitas mesin <span class="text-rose-600">*</span></label>
                            <select id="engine_cc" name="engine_cc" required class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                                <option value="">Pilih kapasitas mesin</option>
                                @foreach(['1.0cc - 1.6cc', '1.8cc - 2.0cc', '2.5cc - 3.5cc'] as $engineRange)
                                    <option value="{{ $engineRange }}" @selected(old('engine_cc') === $engineRange)>{{ $engineRange }}</option>
                                @endforeach
                            </select>
                            @error('engine_cc')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="price" class="mb-2 block text-sm font-bold text-slate-700">Harga produk (Rp) <span class="text-rose-600">*</span></label>
                            <input id="price" name="price" type="number" min="1" max="9999999999" step="1" value="{{ old('price') }}" required inputmode="numeric" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm text-slate-900 outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100" placeholder="Contoh: 850000">
                            @error('price')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="stock" class="mb-2 block text-sm font-bold text-slate-700">Stok awal <span class="text-rose-600">*</span></label>
                            <input id="stock" name="stock" type="number" min="0" max="1000000" step="1" value="{{ old('stock', 0) }}" required inputmode="numeric" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm text-slate-900 outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100">
                            @error('stock')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="image" class="mb-2 block text-sm font-bold text-slate-700">Gambar produk <span class="text-rose-600">*</span></label>
                            <input id="image" name="image" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" required class="block w-full rounded-xl border border-slate-300 bg-white text-sm text-slate-700 file:mr-4 file:rounded-l-xl file:border-0 file:bg-slate-100 file:px-4 file:py-3 file:text-sm file:font-bold file:text-slate-700 hover:file:bg-slate-200">
                            <p class="mt-1 text-xs text-slate-500">Format JPG, PNG, atau WebP. Maksimal 5 MB.</p>
                            @error('image')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label for="description" class="mb-2 block text-sm font-bold text-slate-700">Deskripsi produk <span class="text-xs font-normal text-slate-500">(opsional)</span></label>
                            <textarea id="description" name="description" rows="4" maxlength="5000" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-cyan-600 focus:ring-2 focus:ring-cyan-100" placeholder="Jelaskan karakter suara, bahan, dan fitment knalpot...">{{ old('description') }}</textarea>
                            @error('description')<p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="flex flex-col-reverse justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:px-8">
                        <a href="{{ route('admin.dashboard') }}" class="rounded-xl border border-slate-300 px-5 py-3 text-center text-sm font-bold text-slate-700 transition hover:bg-white">Batal</a>
                        <button type="submit" class="rounded-xl bg-cyan-700 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-cyan-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-cyan-700">Tambah Produk Baru</button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
</html>
