<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\ServiceQueue;
use App\Models\User;
use Database\Seeders\CashierUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ServiceQueueTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirming_a_booking_creates_an_idempotent_per_store_daily_queue_number(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => User::ROLE_ADMIN])->save();
        $storeId = $this->createStore();
        $firstBooking = $this->createBooking($storeId);
        $secondBooking = $this->createBooking($storeId);

        $this->actingAs($admin)
            ->post(route('admin.bookings.update', $firstBooking), ['status' => 'confirmed'])
            ->assertRedirect();
        $this->post(route('admin.bookings.update', $firstBooking), ['status' => 'confirmed'])
            ->assertRedirect();
        $this->post(route('admin.bookings.update', $secondBooking), ['status' => 'confirmed'])
            ->assertRedirect();

        $this->assertDatabaseCount('service_queues', 2);
        $this->assertSame('A-001', $firstBooking->fresh()->serviceQueue->queue_code);
        $this->assertSame('A-002', $secondBooking->fresh()->serviceQueue->queue_code);
    }

    public function test_cashier_can_confirm_booking_validate_payment_and_operate_queue(): void
    {
        $cashier = User::factory()->create(['email' => 'cashier@example.com']);
        $cashier->forceFill(['role' => User::ROLE_CASHIER])->save();
        $storeId = $this->createStore();
        $booking = $this->createBooking($storeId);
        $productId = DB::table('products')->insertGetId([
            'name' => 'Oli Mesin',
            'slug' => 'oli-mesin-antrean',
            'category' => 'oil',
            'price' => 55000,
            'stock' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $booking->items()->create([
            'product_id' => $productId,
            'product_name' => 'Oli Mesin',
            'unit_price' => 55000,
            'quantity' => 1,
        ]);
        Payment::create([
            'booking_id' => $booking->id,
            'method' => 'outlet_cash',
            'amount' => 125000,
            'status' => 'pending',
        ]);

        $this->actingAs($cashier)
            ->get(route('cashir.dashboard'))
            ->assertOk()
            ->assertSee('Pesanan masuk')
            ->assertSee('Ayu Test');

        $this->post(route('cashir.bookings.process', $booking), [
            'status' => 'confirmed',
            'payment_status' => 'paid',
        ])->assertRedirect();

        $queue = ServiceQueue::query()->where('booking_id', $booking->id)->firstOrFail();
        $this->assertSame('A-001', $queue->queue_code);
        $this->get(route('cashir.dashboard'))
            ->assertOk()
            ->assertSee('Kategori: Oli Motor')
            ->assertSee('Oli Mesin × 1');
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'status' => 'paid',
        ]);

        $this->post(route('cashir.queues.status', $queue), ['status' => 'in_service'])
            ->assertRedirect();
        $this->assertDatabaseHas('service_queues', [
            'id' => $queue->id,
            'status' => 'in_service',
        ]);
        $this->get(route('queues.display'))
            ->assertOk()
            ->assertSee('A-001')
            ->assertSee('Kategori: Oli Motor')
            ->assertSee('Oli Mesin × 1')
            ->assertSee('Sedang dikerjakan');
        $this->get(route('booking.status', [
            'booking_id' => $booking->id,
            'phone' => $booking->customer_phone,
        ]))
            ->assertOk()
            ->assertSee('Antrean servis: A-001')
            ->assertSee('Sedang dikerjakan');

        $this->post(route('cashir.queues.status', $queue), ['status' => 'completed'])
            ->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'completed',
        ]);
        $this->get(route('cashir.bookings.receipt', $booking))
            ->assertOk()
            ->assertSee('STRUK PEMESANAN')
            ->assertSee('A-001')
            ->assertSee('Cash');
    }

    public function test_cashier_can_record_a_counter_sale_and_receipt_without_creating_a_service_queue(): void
    {
        $cashier = User::factory()->create(['email' => 'cashier@example.com']);
        $cashier->forceFill(['role' => User::ROLE_CASHIER])->save();
        $storeId = $this->createStore();
        $productId = DB::table('products')->insertGetId([
            'name' => 'Ban Matic',
            'slug' => 'ban-matic-pos',
            'category' => 'tire',
            'price' => 250000,
            'sale_price' => 225000,
            'stock' => 5,
            'sold_count' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($cashier)
            ->get(route('cashir.dashboard'))
            ->assertOk()
            ->assertSee('Mesin kasir')
            ->assertSee('Ban Matic')
            ->assertSee('Ban Motor');

        $this->post(route('cashir.pos.store'), [
            'customer_name' => 'Budi',
            'customer_phone' => '08123456789',
            'store_id' => $storeId,
            'products' => [['id' => $productId, 'quantity' => 2]],
            'payment_method' => 'outlet_cash',
        ])->assertRedirect(route('cashir.bookings.receipt', 1));

        $booking = Booking::query()->with(['items', 'payment'])->firstOrFail();
        $this->assertSame(Booking::TYPE_COUNTER_SALE, $booking->booking_type);
        $this->assertSame('450000.00', $booking->payment->amount);
        $this->assertSame('paid', $booking->payment->status);
        $this->assertNotNull($booking->payment->paid_at);
        $this->assertDatabaseHas('products', [
            'id' => $productId,
            'stock' => 3,
            'sold_count' => 5,
        ]);
        $this->assertDatabaseHas('booking_items', [
            'booking_id' => $booking->id,
            'product_id' => $productId,
            'quantity' => 2,
            'unit_price' => '225000.00',
        ]);
        $this->assertDatabaseCount('service_queues', 0);

        $this->get(route('cashir.bookings.receipt', $booking))
            ->assertOk()
            ->assertSee('STRUK TRANSAKSI KASIR')
            ->assertSee('Rp 450.000')
            ->assertSee('Lunas');
    }

    public function test_cashier_pos_rejects_insufficient_stock_without_creating_a_sale(): void
    {
        $cashier = User::factory()->create();
        $cashier->forceFill(['role' => User::ROLE_CASHIER])->save();
        $storeId = $this->createStore();
        $productId = DB::table('products')->insertGetId([
            'name' => 'Oli Matic',
            'slug' => 'oli-matic-pos',
            'category' => 'oil',
            'price' => 80000,
            'stock' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($cashier)
            ->from(route('cashir.dashboard'))
            ->post(route('cashir.pos.store'), [
                'store_id' => $storeId,
                'products' => [['id' => $productId, 'quantity' => 2]],
                'payment_method' => 'outlet_cash',
            ])
            ->assertRedirect(route('cashir.dashboard'))
            ->assertSessionHasErrors('products.0.quantity');

        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('booking_items', 0);
        $this->assertDatabaseCount('payments', 0);
        $this->assertDatabaseHas('products', ['id' => $productId, 'stock' => 1]);
    }

    public function test_cashier_dashboard_shows_future_queues_but_does_not_allow_calling_them_early(): void
    {
        $cashier = User::factory()->create();
        $cashier->forceFill(['role' => User::ROLE_CASHIER])->save();
        $storeId = $this->createStore();
        $booking = $this->createBooking($storeId);
        $booking->update(['booking_date' => now()->addDay()->toDateString()]);

        $this->actingAs($cashier)
            ->post(route('cashir.bookings.process', $booking), [
                'status' => 'confirmed',
            ])
            ->assertRedirect();

        $queue = ServiceQueue::query()->where('booking_id', $booking->id)->firstOrFail();

        $this->get(route('cashir.dashboard'))
            ->assertOk()
            ->assertSee('Antrean servis & jadwal berikutnya', false)
            ->assertSee('Terjadwal')
            ->assertSee('A-001');

        $this->from(route('cashir.dashboard'))
            ->post(route('cashir.queues.status', $queue), ['status' => 'in_service'])
            ->assertRedirect(route('cashir.dashboard'))
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('service_queues', [
            'id' => $queue->id,
            'status' => ServiceQueue::STATUS_WAITING,
        ]);
    }

    public function test_cashier_routes_reject_guests_customers_and_admins(): void
    {
        $this->get(route('cashir.dashboard'))->assertRedirect(route('login'));

        $customer = User::factory()->create();
        $this->actingAs($customer)
            ->get(route('cashir.dashboard'))
            ->assertRedirect(route('home'));

        $admin = User::factory()->create();
        $admin->forceFill(['role' => User::ROLE_ADMIN])->save();
        $this->actingAs($admin)
            ->get(route('cashir.dashboard'))
            ->assertRedirect(route('home'));
    }

    public function test_cashier_credentials_can_sign_in_and_public_registration_stays_a_user(): void
    {
        $cashier = User::factory()->create([
            'email' => 'kasir@planetban.com',
            'password' => 'cashier-test123',
        ]);
        $cashier->forceFill(['role' => User::ROLE_CASHIER])->save();

        $this->post(route('login.store'), [
            'email' => $cashier->email,
            'password' => 'cashier-test123',
        ])->assertRedirect(route('cashir.dashboard'));

        $this->post(route('logout'));
        $this->post(route('register.store'), [
            'name' => 'Pelanggan Baru',
            'email' => 'new-user@example.com',
            'phone' => '08123456789',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertRedirect(route('home'));

        $this->assertSame(User::ROLE_USER, User::query()->where('email', 'new-user@example.com')->firstOrFail()->role);
    }

    public function test_cashier_seeder_creates_the_configured_cashier_account(): void
    {
        config(['planetban.cashier.password' => 'cashier-test123']);
        $this->seed(CashierUserSeeder::class);
        $cashier = User::query()->where('email', 'kasir@planetban.com')->firstOrFail();

        $this->assertSame(User::ROLE_CASHIER, $cashier->role);
        $this->assertTrue(Hash::check('cashier-test123', $cashier->password));
    }

    private function createStore(): int
    {
        return DB::table('stores')->insertGetId([
            'name' => 'Planet Ban Depok',
            'address' => 'Jl. Raya Depok',
            'city' => 'Depok',
            'phone' => '08123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createBooking(int $storeId): Booking
    {
        return Booking::create([
            'customer_name' => 'Ayu Test',
            'customer_phone' => '08123456789',
            'store_id' => $storeId,
            'booking_date' => now()->toDateString(),
            'booking_time' => '09:00',
            'status' => 'pending',
        ]);
    }
}
