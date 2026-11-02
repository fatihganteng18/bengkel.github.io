<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookingPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_checkout_saves_the_payment_method_and_product_amount_as_pending(): void
    {
        $storeId = DB::table('stores')->insertGetId([
            'name' => 'Planet Ban Depok',
            'address' => 'Jl. Raya Depok',
            'city' => 'Depok',
            'phone' => '08123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $productId = DB::table('products')->insertGetId([
            'name' => 'Ban Motor',
            'slug' => 'ban-motor',
            'category' => 'tire',
            'price' => 250000,
            'sale_price' => 200000,
            'stock' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post(route('booking.store'), [
            'customer_name' => 'Ayu',
            'customer_phone' => '08123456789',
            'store_id' => $storeId,
            'product_id' => $productId,
            'booking_date' => now()->addDay()->toDateString(),
            'booking_time' => '09:00',
            'payment_method' => 'bank_bca',
        ])->assertRedirect(route('booking.status', ['booking_id' => 1, 'phone' => '08123456789']))
            ->assertSessionHas('success');

        $booking = Booking::query()->firstOrFail();

        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'method' => 'bank_bca',
            'amount' => '200000.00',
            'status' => 'pending',
        ]);

        $this->get(route('booking.status', ['phone' => '08123456789']))
            ->assertOk()
            ->assertSee('Transfer bank · BCA')
            ->assertSee('Status pembayaran:')
            ->assertSee('Total produk:');
    }

    public function test_checkout_all_saves_every_product_quantity_and_the_combined_payment_total(): void
    {
        $storeId = DB::table('stores')->insertGetId([
            'name' => 'Planet Ban Depok',
            'address' => 'Jl. Raya Depok',
            'city' => 'Depok',
            'phone' => '08123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $productIds = DB::table('products')->insertGetId([
            'name' => 'Oli Matic',
            'slug' => 'oli-matic',
            'category' => 'oil',
            'price' => 100000,
            'stock' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $secondProductId = DB::table('products')->insertGetId([
            'name' => 'Ban Matic',
            'slug' => 'ban-matic',
            'category' => 'tire',
            'price' => 250000,
            'stock' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post(route('booking.store'), [
            'customer_name' => 'Ayu',
            'customer_phone' => '08123456789',
            'store_id' => $storeId,
            'products' => [
                ['id' => $productIds, 'quantity' => 2],
                ['id' => $secondProductId, 'quantity' => 1],
            ],
            'booking_date' => now()->addDay()->toDateString(),
            'booking_time' => '11:00',
            'payment_method' => 'bank_mandiri',
        ])->assertRedirect(route('booking.status', ['booking_id' => 1, 'phone' => '08123456789']));

        $booking = Booking::query()->with('items')->firstOrFail();

        $this->assertCount(2, $booking->items);
        $this->assertDatabaseHas('booking_items', [
            'booking_id' => $booking->id,
            'product_id' => $productIds,
            'quantity' => 2,
            'unit_price' => '100000.00',
        ]);
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'method' => 'bank_mandiri',
            'amount' => '450000.00',
            'status' => 'pending',
        ]);

        $this->get(route('booking.status', ['phone' => '08123456789']))
            ->assertOk()
            ->assertSee('Oli Matic')
            ->assertSee('Ban Matic')
            ->assertSee('Transfer bank · Mandiri');

        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->get(route('admin.bookings'))
            ->assertOk()
            ->assertSee('Oli Matic')
            ->assertSee('Ban Matic')
            ->assertSee('Transfer bank · Mandiri')
            ->assertSee('Total produk: Rp 450.000');
    }

    public function test_checkout_rejects_quantities_above_current_stock(): void
    {
        $storeId = DB::table('stores')->insertGetId([
            'name' => 'Planet Ban Depok',
            'address' => 'Jl. Raya Depok',
            'city' => 'Depok',
            'phone' => '08123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $productId = DB::table('products')->insertGetId([
            'name' => 'Ban Matic',
            'slug' => 'ban-matic',
            'category' => 'tire',
            'price' => 250000,
            'stock' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->from(route('home'))
            ->post(route('booking.store'), [
                'customer_name' => 'Ayu',
                'customer_phone' => '08123456789',
                'store_id' => $storeId,
                'products' => [['id' => $productId, 'quantity' => 2]],
                'booking_date' => now()->addDay()->toDateString(),
                'booking_time' => '09:00',
                'payment_method' => 'outlet_cash',
            ])
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors('products.0.quantity');

        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseCount('booking_items', 0);
    }

    public function test_qr_checkout_shows_demo_qr_and_reflects_admin_payment_confirmation(): void
    {
        $storeId = DB::table('stores')->insertGetId([
            'name' => 'Planet Ban Depok',
            'address' => 'Jl. Raya Depok',
            'city' => 'Depok',
            'phone' => '08123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $productId = DB::table('products')->insertGetId([
            'name' => 'Oli Matic',
            'slug' => 'oli-matic',
            'category' => 'oil',
            'price' => 100000,
            'stock' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post(route('booking.store'), [
            'customer_name' => 'Ayu',
            'customer_phone' => '08123456789',
            'store_id' => $storeId,
            'products' => [['id' => $productId, 'quantity' => 1]],
            'booking_date' => now()->addDay()->toDateString(),
            'booking_time' => '11:00',
            'payment_method' => 'ewallet_gopay',
        ])->assertRedirect(route('booking.status', ['booking_id' => 1, 'phone' => '08123456789']));

        $resultUrl = route('booking.status', ['booking_id' => 1, 'phone' => '08123456789']);
        $this->get($resultUrl)
            ->assertOk()
            ->assertSee('QR pembayaran contoh')
            ->assertSee('QR ini hanya mockup untuk demo')
            ->assertSee('tidak dapat digunakan untuk membayar')
            ->assertSee('Menunggu konfirmasi pembayaran oleh admin')
            ->assertSee('http-equiv="refresh" content="15"', false);

        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $booking = Booking::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.bookings.update', $booking), [
                'status' => 'confirmed',
                'payment_status' => 'paid',
            ])
            ->assertRedirect();

        $this->get($resultUrl)
            ->assertOk()
            ->assertSee('Pembayaran telah dikonfirmasi oleh admin')
            ->assertDontSee('http-equiv="refresh" content="15"', false);
    }

    public function test_checkout_rejects_unknown_payment_methods_without_creating_records(): void
    {
        $storeId = DB::table('stores')->insertGetId([
            'name' => 'Planet Ban Depok',
            'address' => 'Jl. Raya Depok',
            'city' => 'Depok',
            'phone' => '08123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->from(route('home'))
            ->post(route('booking.store'), [
                'customer_name' => 'Ayu',
                'customer_phone' => '08123456789',
                'store_id' => $storeId,
                'booking_date' => now()->addDay()->toDateString(),
                'booking_time' => '09:00',
                'payment_method' => 'fake_method',
            ])
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors('payment_method');

        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_catalog_only_shows_discount_badges_for_a_valid_sale_price(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Ban Promo',
                'slug' => 'ban-promo',
                'category' => 'tire',
                'price' => 250000,
                'sale_price' => 200000,
                'stock' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Oli Normal',
                'slug' => 'oli-normal',
                'category' => 'oil',
                'price' => 50000,
                'sale_price' => 60000,
                'stock' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        DB::table('stores')->insert([
            'name' => 'Planet Ban Depok',
            'address' => 'Jl. Raya Depok',
            'city' => 'Depok',
            'phone' => '08123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Hemat 20%')
            ->assertSee('Rp 200.000')
            ->assertSee('Diskon Harga');
    }
}
