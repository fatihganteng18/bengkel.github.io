<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Knalpot Standar Depan / Frontpipe', 'product' => 'Frontpipe'],
            ['name' => 'Knalpot Standar Belakang / Tailpipe Standar', 'product' => 'Tailpipe'],
            ['name' => 'Knalpot Racing Full System (1.0cc - 1.6cc)', 'product' => 'Full System Compact'],
            ['name' => 'Knalpot Racing Full System (1.8cc - 2.0cc)', 'product' => 'Full System Sport'],
            ['name' => 'Knalpot Racing Full System (2.5cc - 3.5cc)', 'product' => 'Full System Big Engine'],
            ['name' => 'Silencer Only - Model Oval Racing', 'product' => 'Silencer Oval'],
            ['name' => 'Silencer Only - Model Tabung Bulat', 'product' => 'Silencer Round'],
            ['name' => 'Header / Downpipe Custom (Non-Turbo)', 'product' => 'Header Custom'],
            ['name' => 'Header / Downpipe Custom (Turbo / Diesel)', 'product' => 'Downpipe Custom'],
            ['name' => 'Resonator Tabung Peredam Suara', 'product' => 'Resonator'],
            ['name' => 'Knalpot Custom Valvetronic (Dual Sound Mode)', 'product' => 'Valvetronic'],
        ];
        $brands = ['Apex', 'Vektor', 'Carbonix', 'Rogue', 'Nitroline', 'Torque', 'Raven', 'Flux', 'Overdrive', 'Shift'];
        $soundTypes = ['Suara Halus Standart', 'Bass Bulat / Adem', 'Gahar', 'Silent'];
        $engineRanges = [
            2 => '1.0cc - 1.6cc',
            3 => '1.8cc - 2.0cc',
            4 => '2.5cc - 3.5cc',
        ];
        $otherEngineRanges = ['1.0cc - 1.6cc', '1.8cc - 2.0cc', '2.5cc - 3.5cc'];
        $products = [];
        $now = now();
        $number = 0;

        foreach ($categories as $categoryIndex => $category) {
            $categoryCount = intdiv(500, count($categories)) + ($categoryIndex < 500 % count($categories) ? 1 : 0);

            for ($itemIndex = 1; $itemIndex <= $categoryCount; $itemIndex++) {
                $number++;
                $engineCc = $engineRanges[$categoryIndex]
                    ?? ($categoryIndex === 10
                        ? $engineRanges[4]
                        : $otherEngineRanges[($itemIndex + $categoryIndex) % count($otherEngineRanges)]);
                $inletSizes = match ($categoryIndex) {
                    0, 1, 2 => ['1.5 inch', '2.0 inch'],
                    3, 5, 6, 9 => ['2.0 inch', '2.25 inch', '2.5 inch'],
                    4, 8, 10 => ['2.5 inch', '3.0 inch'],
                    default => ['2.0 inch', '2.25 inch', '2.5 inch'],
                };
                $material = match ($categoryIndex) {
                    0 => $itemIndex % 3 === 0 ? 'Stainless Steel' : 'Galvanis',
                    1 => $itemIndex % 3 === 0 ? 'Karbon' : 'Galvanis',
                    5, 6, 9 => 'Stainless Steel',
                    10, 4 => $itemIndex % 2 === 0 ? 'Karbon' : 'Stainless Steel',
                    default => 'Stainless Steel',
                };
                [$minimumPrice, $maximumPrice] = match ($categoryIndex) {
                    0, 1 => [150000, 450000],
                    2 => [1800000, 3000000],
                    3 => [2200000, 3800000],
                    4 => [2800000, 4500000],
                    5, 6, 9 => [350000, 850000],
                    7 => [650000, 1500000],
                    8 => [800000, 1500000],
                    10 => [2200000, 4500000],
                };
                $priceStep = intdiv($maximumPrice - $minimumPrice, 9);
                $price = $minimumPrice + (($itemIndex * 7 + $categoryIndex * 3) % 10) * $priceStep;
                $soundType = $categoryIndex === 10
                    ? $soundTypes[$itemIndex % 2 === 0 ? 1 : 2]
                    : $soundTypes[($itemIndex + $categoryIndex) % count($soundTypes)];
                $brand = $brands[($itemIndex + $categoryIndex) % count($brands)];
                $name = sprintf(
                    '%s %s %s - Seri %03d',
                    $brand,
                    $category['product'],
                    $inletSizes[$itemIndex % count($inletSizes)],
                    $itemIndex,
                );
                $slug = Str::slug($name).'-'.str_pad((string) $number, 3, '0', STR_PAD_LEFT);
                $weldNote = 'Sambungan presisi dengan las TIG Argon untuk hasil rapi dan kuat.';
                $description = match ($categoryIndex) {
                    0, 1 => "Bikin bagian bawah mobil makin proper dan auto ganteng. {$weldNote} Material {$material} dengan karakter {$soundType}, Daily Drive Approved.",
                    5, 6 => "Upgrade suara tanpa ganti satu set. Model {$category['product']} berbahan {$material}, inlet {$inletSizes[$itemIndex % count($inletSizes)]}, karakter {$soundType}. Anti dengerin suara standar, tetap nyaman buat harian. {$weldNote}",
                    7, 8 => "Aliran gas buang lebih optimal dengan fitment custom untuk mesin {$engineCc}. Bahan {$material}, karakter {$soundType}, dan {$weldNote} Vibes racing tanpa kompromi.",
                    9 => "Redam resonansi yang mengganggu, jaga kabin tetap nyaman. Resonator {$material} dengan karakter {$soundType}; rapi dilas TIG Argon dan siap daily drive.",
                    10 => "Satu setup, dua vibes: mode senyap untuk harian dan suara gahar saat dibutuhkan. Valvetronic {$material} untuk mesin {$engineCc}, dirakit dengan {$weldNote}",
                    default => "Setup {$category['product']} berbahan {$material} untuk mesin {$engineCc}. Diameter {$inletSizes[$itemIndex % count($inletSizes)]} dan karakter {$soundType}; {$weldNote} Perform maksimal, daily drive tetap approved.",
                };

                $products[] = [
                    'name' => $name,
                    'slug' => $slug,
                    'sku' => sprintf('EXH-%02d-%03d', $categoryIndex + 1, $itemIndex),
                    'category' => 'exhaust',
                    'sub_category' => $category['name'],
                    'price' => $price,
                    'stock' => 8 + (($number * 7) % 93),
                    'sold_count' => ($number * 13) % 401,
                    'inlet_size' => $inletSizes[$itemIndex % count($inletSizes)],
                    'sound_type' => $soundType,
                    'material' => $material,
                    'engine_cc' => $engineCc,
                    'description' => $description,
                    'image' => '/images/products/exhaust-'.($categoryIndex + 1).'.jpg',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::transaction(function () use ($products): void {
            Product::query()->delete();

            foreach (array_chunk($products, 100) as $productChunk) {
                Product::query()->insert($productChunk);
            }
        });
    }
}
