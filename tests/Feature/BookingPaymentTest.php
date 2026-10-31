<?php

namespace Tests\Feature;

use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookingPaymentTest extends TestCase
{
    use RefreshDatabase;

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
        ])->assertRedirect(route('booking.status', ['phone' => '08123456789']))
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
            ->assertSee('Nilai produk:');
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
            ->assertDontSee('Diskon Harga');
    }
}
