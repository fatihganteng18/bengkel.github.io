<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_detail_displays_seeded_sales_count(): void
    {
        $this->seed(ProductSeeder::class);
        $product = Product::query()->where('slug', 'presa-planeto-2-tubeless-110-70-12')->firstOrFail();

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee('12 terjual');
    }

    public function test_product_detail_marks_products_with_ten_or_fewer_units_as_low_stock(): void
    {
        $this->seed(ProductSeeder::class);
        $product = Product::query()->where('slug', 'enduro-matic-v-10w-40-1l')->firstOrFail();

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('Stok terbatas')
            ->assertSee('OLI-END-MATV-1040');
    }
}