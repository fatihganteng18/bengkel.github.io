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
        $product = Product::query()->where('sku', 'EXH-01-001')->firstOrFail();

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee('13 terjual')
            ->assertSee('Inlet / diameter')
            ->assertSee('Karakter suara')
            ->assertSee('TIG Argon');
    }

    public function test_product_detail_marks_products_with_ten_or_fewer_units_as_low_stock(): void
    {
        $this->seed(ProductSeeder::class);
        $product = Product::query()->where('stock', '<=', 10)->firstOrFail();

        $this->get(route('product.show', $product))
            ->assertOk()
            ->assertSee('Stok terbatas')
            ->assertSee($product->sku);
    }
}