<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sign_in_redirects_to_the_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'planetban123',
        ]);
        $admin->forceFill(['role' => 'admin'])->save();

        $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'planetban123',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);

        $this->get(route('account.dashboard'))
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_signed_in_admin_sees_admin_dashboard_and_sign_out_options_in_storefront_menu(): void
    {
        $admin = User::factory()->create([
            'name' => 'Planet Admin',
            'email' => 'admin@example.com',
        ]);
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Planet Admin')
            ->assertSee(route('admin.dashboard'), false)
            ->assertSee(route('logout'), false)
            ->assertSee('Dashboard admin')
            ->assertSee('Keluar');
    }

    public function test_the_admin_seeder_creates_the_fixed_admin_account_and_login_redirects_to_dashboard(): void
    {
        config(['planetban.admin.password' => 'Adillah354']);
        $this->seed(AdminUserSeeder::class);
        $admin = User::query()->where('email', 'adillahalfaris@gmail.com')->firstOrFail();

        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('Adillah354', $admin->password));

        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Masuk ke Dashboard Admin');

        $this->post(route('admin.login.submit'), [
            'email' => 'adillahalfaris@gmail.com',
            'password' => 'Adillah354',
        ])->assertRedirect(route('admin.dashboard'));
    }

    public function test_database_seeder_is_safe_to_run_more_than_once(): void
    {
        config(['planetban.admin.password' => 'Adillah354']);

        $this->seed();
        $this->seed();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('motorcycles', 3);
        $this->assertDatabaseCount('products', 2);
        $this->assertDatabaseCount('stores', 1);
    }

    public function test_the_admin_login_rejects_customer_accounts(): void
    {
        $customer = User::factory()->create([
            'email' => 'rider@example.com',
            'password' => 'planetban123',
        ]);

        $this->from(route('admin.login'))
            ->post(route('admin.login.submit'), [
                'email' => $customer->email,
                'password' => 'planetban123',
            ])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_only_admins_can_open_admin_pages_or_update_bookings(): void
    {
        $customer = User::factory()->create();
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));

        $storeId = DB::table('stores')->insertGetId([
            'name' => 'Planet Ban Depok',
            'address' => 'Jl. Raya Depok',
            'city' => 'Depok',
            'phone' => '08123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $booking = Booking::create([
            'customer_name' => 'Ayu',
            'customer_phone' => '08123456789',
            'store_id' => $storeId,
            'booking_date' => now()->addDay()->toDateString(),
            'booking_time' => '09:00',
            'status' => 'pending',
        ]);

        $this->actingAs($customer)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('home'));

        $this->actingAs($customer)
            ->get(route('admin.bookings'))
            ->assertRedirect(route('home'));

        $this->actingAs($customer)
            ->post(route('admin.bookings.update', $booking), ['status' => 'completed'])
            ->assertRedirect(route('home'));
    }

    public function test_admin_can_update_booking_status_and_invalid_status_is_rejected(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $storeId = DB::table('stores')->insertGetId([
            'name' => 'Planet Ban Depok',
            'address' => 'Jl. Raya Depok',
            'city' => 'Depok',
            'phone' => '08123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $booking = Booking::create([
            'customer_name' => 'Ayu',
            'customer_phone' => '08123456789',
            'store_id' => $storeId,
            'booking_date' => now()->addDay()->toDateString(),
            'booking_time' => '09:00',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.bookings.update', $booking), ['status' => 'confirmed'])
            ->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);

        $this->post(route('admin.bookings.update', $booking), ['status' => 'unknown'])
            ->assertSessionHasErrors('status');
    }

    public function test_admin_dashboard_shows_booking_trends_best_sellers_and_low_stock(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

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
            'stock' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Booking::create([
            'customer_name' => 'Ayu',
            'customer_phone' => '08123456789',
            'store_id' => $storeId,
            'product_id' => $productId,
            'booking_date' => now()->addDay()->toDateString(),
            'booking_time' => '09:00',
            'status' => 'pending',
        ]);
        Booking::create([
            'customer_name' => 'Budi',
            'customer_phone' => '08123456780',
            'store_id' => $storeId,
            'booking_date' => now()->addDay()->toDateString(),
            'booking_time' => '10:00',
            'status' => 'confirmed',
        ]);
        Payment::create([
            'booking_id' => Booking::query()->firstOrFail()->id,
            'method' => 'bank_bca',
            'amount' => 250000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Tren transaksi booking enam bulan terakhir')
            ->assertSee('bukan pembayaran yang sudah lunas')
            ->assertSee('Produk & layanan terlaris', false)
            ->assertSee('Sisa stok produk')
            ->assertSee('Pembayaran menunggu')
            ->assertSee('Ban Motor')
            ->assertSee('1 booking')
            ->assertSee('3 tersisa');
    }

    public function test_admin_can_mark_a_payment_paid_and_the_timestamp_is_recorded(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $storeId = DB::table('stores')->insertGetId([
            'name' => 'Planet Ban Depok',
            'address' => 'Jl. Raya Depok',
            'city' => 'Depok',
            'phone' => '08123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $booking = Booking::create([
            'customer_name' => 'Ayu',
            'customer_phone' => '08123456789',
            'store_id' => $storeId,
            'booking_date' => now()->addDay()->toDateString(),
            'booking_time' => '09:00',
            'status' => 'pending',
        ]);
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'method' => 'ewallet_gopay',
            'amount' => 150000,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.bookings.update', $booking), [
                'status' => 'confirmed',
                'payment_status' => 'paid',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
        ]);
        $this->assertNotNull($payment->fresh()->paid_at);

        $this->get(route('admin.bookings'))
            ->assertOk()
            ->assertSee('E-Wallet · GoPay')
            ->assertSee('paid');
    }
}
