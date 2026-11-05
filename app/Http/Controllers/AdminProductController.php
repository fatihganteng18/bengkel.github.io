<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;
use Throwable;

class AdminProductController extends Controller
{
    private const SUB_CATEGORIES = [
        'Knalpot Standar Depan / Frontpipe',
        'Knalpot Standar Belakang / Tailpipe Standar',
        'Knalpot Racing Full System (1.0cc - 1.6cc)',
        'Knalpot Racing Full System (1.8cc - 2.0cc)',
        'Knalpot Racing Full System (2.5cc - 3.5cc)',
        'Silencer Only - Model Oval Racing',
        'Silencer Only - Model Tabung Bulat',
        'Header / Downpipe Custom (Non-Turbo)',
        'Header / Downpipe Custom (Turbo / Diesel)',
        'Resonator Tabung Peredam Suara',
        'Knalpot Custom Valvetronic (Dual Sound Mode)',
    ];

    public function create(): View
    {
        return view('admin.products.create', [
            'subCategories' => self::SUB_CATEGORIES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sub_category' => ['required', 'in:'.implode(',', self::SUB_CATEGORIES)],
            'inlet_size' => ['required', 'in:1.5 inch,2.0 inch,2.25 inch,2.5 inch,3.0 inch'],
            'sound_type' => ['required', 'in:Suara Halus Standart,Bass Bulat / Adem,Gahar,Silent'],
            'material' => ['required', 'in:Stainless Steel,Galvanis,Karbon'],
            'engine_cc' => ['required', 'in:1.0cc - 1.6cc,1.8cc - 2.0cc,2.5cc - 3.5cc'],
            'price' => ['required', 'numeric', 'min:1', 'max:9999999999.99'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000000'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $suffix = 2;

        while (Product::query()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        do {
            $sku = 'ARISA-'.Str::upper(Str::random(8));
        } while (Product::query()->where('sku', $sku)->exists());

        $imagePath = $validated['image']->store('products', 'public');

        if ($imagePath === false) {
            throw new RuntimeException('Gambar produk gagal disimpan.');
        }

        try {
            DB::transaction(function () use ($validated, $slug, $sku, $imagePath): void {
                Product::query()->create([
                    'name' => $validated['name'],
                    'slug' => $slug,
                    'sku' => $sku,
                    'category' => 'exhaust',
                    'sub_category' => $validated['sub_category'],
                    'price' => $validated['price'],
                    'stock' => $validated['stock'],
                    'sold_count' => 0,
                    'inlet_size' => $validated['inlet_size'],
                    'sound_type' => $validated['sound_type'],
                    'material' => $validated['material'],
                    'engine_cc' => $validated['engine_cc'],
                    'description' => $validated['description'] ?? null,
                    'image' => Storage::disk('public')->url($imagePath),
                ]);
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($imagePath);

            throw $exception;
        }

        return redirect()
            ->route('admin.products.create')
            ->with('success', 'Produk knalpot berhasil ditambahkan.');
    }
}
