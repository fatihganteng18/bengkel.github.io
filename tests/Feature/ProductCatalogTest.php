<?php

namespace Tests\Feature;

use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_displays_brand_filters_product_data_and_searchable_controls(): void
    {
        $this->seed(ProductSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('ASPIRA PREMIO')
            ->assertSee('MICHELIN')
            ->assertSee('filterCatalogByBrand(\'michelin\')', false)
            ->assertSee('aria-label="Filter merek MICHELIN"', false)
            ->assertSee('Cari ban buat motormu disini')
            ->assertSee('Menampilkan')
            ->assertSee('Urutkan')
            ->assertSee('Kompatibilitas untuk motor')
            ->assertSee('Ban Motor Matic')
            ->assertSee('Ukuran Ban')
            ->assertSee('Tampilkan')
            ->assertSee('PRESA PLANETO 2 TUBELLESS 110/70-12')
            ->assertSee('Rp 305.000')
            ->assertSee('data-type="tubeless"', false)
            ->assertSee('images/products/planet-ban-tire.svg');
    }
}
