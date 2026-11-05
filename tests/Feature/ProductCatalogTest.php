<?php

namespace Tests\Feature;

use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_displays_exhaust_categories_specs_and_searchable_controls(): void
    {
        $this->seed(ProductSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Arisa Knalpot')
            ->assertDontSee('EXHAUST')
            ->assertSee('Knalpot Standar Depan / Frontpipe')
            ->assertSee('Knalpot Custom Valvetronic (Dual Sound Mode)')
            ->assertSee('Cari knalpot, diameter, atau karakter suara...')
            ->assertSee('Menampilkan')
            ->assertSee('Urutkan')
            ->assertSee('Inlet / diameter')
            ->assertSee('Karakter suara')
            ->assertSee('Kapasitas mesin')
            ->assertSee('Merek')
            ->assertSee('Tampilkan')
            ->assertSeeText('Menampilkan 1-16 dari 500 Data')
            ->assertDontSee('Vektor Frontpipe 2.0 inch - Seri 017')
            ->assertSee('Vektor Frontpipe 2.0 inch - Seri 001')
            ->assertSee('images/products/exhaust-1.jpg');
    }

    public function test_catalog_filters_and_pagination_preserve_search_and_price_parameters(): void
    {
        $this->seed(ProductSeeder::class);
        $filters = [
            'q' => 'Frontpipe',
            'category' => 'exhaust',
            'sub_category' => 'Knalpot Standar Depan / Frontpipe',
            'min_price' => 100000,
            'max_price' => 500000,
        ];

        $this->get(route('home', ['q' => 'Frontpipe']))
            ->assertOk()
            ->assertSeeText('Menampilkan 1-16 dari 46 Data');

        $this->get(route('home', $filters))
            ->assertOk()
            ->assertSee('name="q" value="Frontpipe"', false)
            ->assertSee('name="sub_category" value="Knalpot Standar Depan / Frontpipe"', false)
            ->assertSeeText('Menampilkan 1-16 dari 46 Data')
            ->assertSee('q=Frontpipe', false)
            ->assertSee('sub_category=', false)
            ->assertSee('min_price=100000', false)
            ->assertSee('max_price=500000', false);

        $this->get(route('home', [...$filters, 'page' => 2]))
            ->assertOk()
            ->assertSeeText('Menampilkan 17-32 dari 46 Data')
            ->assertSee('q=Frontpipe', false)
            ->assertSee('min_price=100000', false);
    }

    public function test_catalog_filters_by_brand_and_effective_price(): void
    {
        $this->seed(ProductSeeder::class);

        $this->get(route('home', [
            'brand' => 'Apex',
            'min_price' => 100000,
            'max_price' => 200000,
            'sort' => 'price-asc',
        ]))
            ->assertOk()
            ->assertSee('name="brand" value="Apex"', false)
            ->assertSee('name="sort"', false)
            ->assertSeeText('Menampilkan 1-4 dari 4 Data')
            ->assertSee('Apex');
    }
}
