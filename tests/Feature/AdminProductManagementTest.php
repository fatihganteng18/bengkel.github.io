<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_product_form_and_admin_queue_with_consistent_sidebar(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => User::ROLE_ADMIN])->save();
        $this->actingAs($admin);

        $this->get(route('admin.products.create'))
            ->assertOk()
            ->assertSee('Tambah Produk Baru')
            ->assertSee('name="name"', false)
            ->assertSee('name="sub_category"', false)
            ->assertSee('name="image"', false)
            ->assertSee(route('admin.dashboard'), false)
            ->assertSee(route('admin.bookings'), false)
            ->assertSee(route('admin.queues'), false)
            ->assertSee(route('home'), false);

        $this->get(route('admin.bookings'))
            ->assertOk()
            ->assertSee('Kelola Booking')
            ->assertSee(route('admin.products.create'), false);

        $this->get(route('admin.queues'))
            ->assertOk()
            ->assertSee('Monitor Antrean')
            ->assertSee(route('admin.products.create'), false);
    }

    public function test_admin_can_create_product_with_generated_unique_slug_and_uploaded_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create();
        $admin->forceFill(['role' => User::ROLE_ADMIN])->save();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Arisa Valvetronic 2.5 Inch',
            'sub_category' => 'Knalpot Custom Valvetronic (Dual Sound Mode)',
            'inlet_size' => '2.5 inch',
            'sound_type' => 'Bass Bulat / Adem',
            'material' => 'Stainless Steel',
            'engine_cc' => '2.5cc - 3.5cc',
            'price' => 3250000,
            'stock' => 8,
            'description' => 'Dual sound mode dengan las TIG Argon.',
            'image' => UploadedFile::fake()->image('valvetronic.jpg'),
        ]);

        $response->assertRedirect(route('admin.products.create'))
            ->assertSessionHas('success', 'Produk knalpot berhasil ditambahkan.');

        $product = Product::query()->where('name', 'Arisa Valvetronic 2.5 Inch')->firstOrFail();
        $this->assertSame('arisa-valvetronic-25-inch', $product->slug);
        $this->assertSame('exhaust', $product->category);
        $this->assertSame(0, $product->sold_count);
        $this->assertSame('Stainless Steel', $product->material);
        $this->assertStringContainsString('/storage/products/', $product->image);
        Storage::disk('public')->assertExists(Str::after($product->image, '/storage/'));

        $this->post(route('admin.products.store'), [
            'name' => 'Arisa Valvetronic 2.5 Inch',
            'sub_category' => 'Knalpot Custom Valvetronic (Dual Sound Mode)',
            'inlet_size' => '2.5 inch',
            'sound_type' => 'Bass Bulat / Adem',
            'material' => 'Stainless Steel',
            'engine_cc' => '2.5cc - 3.5cc',
            'price' => 3250000,
            'stock' => 8,
            'image' => UploadedFile::fake()->image('valvetronic-second.jpg'),
        ])->assertRedirect(route('admin.products.create'));

        $this->assertDatabaseHas('products', [
            'name' => 'Arisa Valvetronic 2.5 Inch',
            'slug' => 'arisa-valvetronic-25-inch-2',
        ]);
        $this->assertSame(2, Product::query()->where('name', 'Arisa Valvetronic 2.5 Inch')->distinct('sku')->count());
    }

    public function test_product_form_rejects_invalid_specs_and_non_image_uploads(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => User::ROLE_ADMIN])->save();

        $this->actingAs($admin)
            ->from(route('admin.products.create'))
            ->post(route('admin.products.store'), [
                'name' => 'Produk tidak valid',
                'sub_category' => 'Bukan kategori knalpot',
                'inlet_size' => '4 inch',
                'sound_type' => 'Terlalu berisik',
                'material' => 'Plastik',
                'engine_cc' => '99cc',
                'price' => -10,
                'stock' => -1,
                'image' => UploadedFile::fake()->create('gambar.txt', 10, 'text/plain'),
            ])
            ->assertRedirect(route('admin.products.create'))
            ->assertSessionHasErrors([
                'sub_category',
                'inlet_size',
                'sound_type',
                'material',
                'engine_cc',
                'price',
                'stock',
                'image',
            ]);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_non_admin_cannot_access_product_management_or_admin_queue(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get(route('admin.products.create'))
            ->assertRedirect(route('home'));

        $this->get(route('admin.queues'))
            ->assertRedirect(route('home'));
    }
}
