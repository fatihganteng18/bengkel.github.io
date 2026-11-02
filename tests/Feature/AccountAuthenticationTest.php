<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AccountAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_customer_can_register_and_is_signed_in(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Ayu Pratama',
            'email' => 'AYU@example.com',
            'phone' => '+62 812-3456-7890',
            'password' => 'planetban123',
            'password_confirmation' => 'planetban123',
            'role' => 'admin',
        ]);

        $user = User::where('email', 'ayu@example.com')->firstOrFail();

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame('+62 812-3456-7890', $user->phone);
        $this->assertSame('user', $user->role);
        $this->assertTrue(Hash::check('planetban123', $user->password));
    }

    public function test_registration_rejects_invalid_contact_details_and_weak_passwords(): void
    {
        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Ayu Pratama',
                'email' => 'invalid-email',
                'phone' => 'not-a-phone',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['email', 'phone', 'password']);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_cannot_claim_a_reserved_admin_email_in_any_case(): void
    {
        foreach (['ADMIN@GMAIL.COM', 'ADILLAHALFARIS@GMAIL.COM'] as $email) {
            $this->from(route('register'))
                ->post(route('register.store'), [
                    'name' => 'Impersonator',
                    'email' => $email,
                    'phone' => '08123456789',
                    'password' => 'planetban123',
                    'password_confirmation' => 'planetban123',
                ])
                ->assertRedirect(route('register'))
                ->assertSessionHasErrors('email');
        }

        $this->assertDatabaseCount('users', 0);
    }

    public function test_a_customer_can_sign_in_and_sign_out(): void
    {
        $user = User::factory()->create([
            'email' => 'rider@example.com',
            'password' => 'planetban123',
        ]);

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'planetban123',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_guest_booking_is_redirected_to_sign_in_without_creating_a_booking(): void
    {
        $this->post(route('booking.store'), [])
            ->assertRedirect(route('login'))
            ->assertSessionHas('url.intended', url('/'));

        $this->assertDatabaseCount('bookings', 0);
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_login_and_registration_return_to_a_safe_intended_storefront_url(): void
    {
        $user = User::factory()->create(['password' => 'planetban123']);

        $this->get(route('login', ['intended' => '/product/42?from=cart#katalog']))
            ->assertOk();
        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'planetban123',
        ])->assertRedirect(url('/product/42?from=cart#katalog'));

        auth()->logout();
        $this->get(route('register', ['intended' => '/product/43#katalog']))
            ->assertOk();
        $this->post(route('register.store'), [
            'name' => 'Ayu Pratama',
            'email' => 'ayu-redirect@example.com',
            'phone' => '08123456789',
            'password' => 'planetban123',
            'password_confirmation' => 'planetban123',
        ])->assertRedirect(url('/product/43#katalog'));
    }

    public function test_login_ignores_external_intended_redirects(): void
    {
        $user = User::factory()->create(['password' => 'planetban123']);

        $this->get(route('login', ['intended' => 'https://example.com']))
            ->assertOk();
        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'planetban123',
        ])->assertRedirect(route('home'));
    }

    public function test_signed_in_customer_sees_account_menu_and_guest_sees_sign_in_link(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('login'), false)
            ->assertSee(route('register'), false)
            ->assertSee('Masuk')
            ->assertSee('Daftar')
            ->assertSee('Silakan masuk terlebih dahulu untuk melanjutkan pemesanan.')
            ->assertSee('data-authenticated="false"', false);

        $user = User::factory()->create([
            'name' => 'Ayu Rider',
            'email' => 'ayu@example.com',
        ]);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Ayu Rider')
            ->assertSee('ayu@example.com')
            ->assertSee(route('account.dashboard'), false)
            ->assertSee(route('logout'), false)
            ->assertSee('Akun saya')
            ->assertSee('Keluar')
            ->assertSee('data-authenticated="true"', false);
    }

    public function test_the_customer_dashboard_only_shows_the_signed_in_users_bookings(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $storeId = DB::table('stores')->insertGetId([
            'name' => 'Planet Ban Depok',
            'address' => 'Jl. Raya Depok',
            'city' => 'Depok',
            'phone' => '08123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $bookingData = [
            'customer_name' => 'Pemilik Akun',
            'customer_phone' => '08123456789',
            'store_id' => $storeId,
            'booking_date' => now()->addDay()->toDateString(),
            'booking_time' => '09:00',
            'status' => 'confirmed',
        ];

        Booking::create($bookingData + ['user_id' => $user->id]);
        Booking::create($bookingData + ['user_id' => $otherUser->id]);

        $this->actingAs($user)
            ->get(route('account.dashboard'))
            ->assertOk()
            ->assertSee('Planet Ban Depok')
            ->assertSee('Dikonfirmasi')
            ->assertSee('Total booking')
            ->assertDontSee('Booking #2');
    }

    public function test_a_signed_in_booking_is_linked_to_the_customer_account(): void
    {
        $user = User::factory()->create([
            'name' => 'Rider Planet',
            'phone' => '08123456789',
        ]);
        $storeId = DB::table('stores')->insertGetId([
            'name' => 'Planet Ban Bekasi',
            'address' => 'Jl. Raya Bekasi',
            'city' => 'Bekasi',
            'phone' => '08123456789',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user)
            ->post(route('booking.store'), [
                'customer_name' => $user->name,
                'customer_phone' => $user->phone,
                'store_id' => $storeId,
                'booking_date' => now()->addDay()->toDateString(),
                'booking_time' => '09:00',
                'payment_method' => 'outlet_cash',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'customer_name' => $user->name,
        ]);
    }

    public function test_a_customer_can_request_and_use_a_password_reset_link(): void
    {
        Notification::fake();
        $user = User::factory()->create([
            'email' => 'reset@example.com',
            'password' => 'oldpassword123',
        ]);

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);

        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ])->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }
}
