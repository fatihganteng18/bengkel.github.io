<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'PRESA PLANETO 2 TUBELLESS 110/70-12',
                'slug' => 'presa-planeto-2-tubeless-110-70-12',
                'sku' => 'BAN-PRE-1107012',
                'category' => 'tire',
                'price' => 305000,
                'stock' => 45,
                'sold_count' => 12,
                'description' => 'Ban tubeless untuk skuter, ukuran 110/70-12.',
                'image' => null,
            ],
            [
                'name' => 'CORSA ELITE 90/90-14',
                'slug' => 'corsa-elite-90-90-14',
                'sku' => 'BAN-COR-909014',
                'category' => 'tire',
                'price' => 215000,
                'stock' => 30,
                'sold_count' => 28,
                'description' => 'Ban motor harian Corsa Elite ukuran 90/90-14.',
                'image' => null,
            ],
            [
                'name' => 'IRC FASTI PRO 90/80-14',
                'slug' => 'irc-fasti-pro-90-80-14',
                'sku' => 'BAN-IRC-908014',
                'category' => 'tire',
                'price' => 350000,
                'stock' => 15,
                'sold_count' => 8,
                'description' => 'Ban motor IRC Fasti Pro ukuran 90/80-14.',
                'image' => null,
            ],
            [
                'name' => 'AXIOO / BFP OIL MATIC 10W-30 0.8L',
                'slug' => 'axioo-bfp-oil-matic-10w-30-08l',
                'sku' => 'OLI-AXI-103008',
                'category' => 'oil',
                'price' => 55000,
                'stock' => 120,
                'sold_count' => 65,
                'description' => 'Oli mesin matic SAE 10W-30 kemasan 0,8 liter.',
                'image' => null,
            ],
            [
                'name' => 'AHM OIL MPX 2 MATIC 0.8L',
                'slug' => 'ahm-oil-mpx-2-matic-08l',
                'sku' => 'OLI-AHM-MPX2-08',
                'category' => 'oil',
                'price' => 60000,
                'stock' => 85,
                'sold_count' => 50,
                'description' => 'Oli mesin AHM MPX 2 untuk motor matic, kemasan 0,8 liter.',
                'image' => null,
            ],
            [
                'name' => 'ENDURO MATIC V 10W-40 1L',
                'slug' => 'enduro-matic-v-10w-40-1l',
                'sku' => 'OLI-END-MATV-1040',
                'category' => 'oil',
                'price' => 58000,
                'stock' => 10,
                'sold_count' => 4,
                'description' => 'Oli mesin motor matic SAE 10W-40 kemasan 1 liter.',
                'image' => null,
            ],
            [
                'name' => 'Busi NGK MotoDX',
                'slug' => 'busi-ngk-motodx',
                'sku' => 'SP-NGK-MOTODX',
                'category' => 'sparepart',
                'price' => 35000,
                'stock' => 50,
                'sold_count' => 19,
                'description' => 'Busi NGK MotoDX untuk pengapian motor.',
                'image' => null,
            ],
            [
                'name' => 'Kampas Rem Depan Nissin',
                'slug' => 'kampas-rem-depan-nissin',
                'sku' => 'SP-NIS-REM-DEP',
                'category' => 'sparepart',
                'price' => 45000,
                'stock' => 25,
                'sold_count' => 14,
                'description' => 'Kampas rem depan Nissin untuk penggantian rutin.',
                'image' => null,
            ],
        ];

        foreach ($products as $product) {
            Product::query()->updateOrCreate(
                ['slug' => $product['slug']],
                $product,
            );
        }
    }
}
