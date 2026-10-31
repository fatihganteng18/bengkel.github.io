<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_information_pages_render_successfully(): void
    {
        foreach (['about', 'faq', 'blog', 'career', 'privacy', 'copyright', 'terms'] as $page) {
            $this->get(route($page))->assertOk();
        }
    }

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertDontSee('href="/admin/bookings"')
            ->assertSee('Kebijakan Privasi')
            ->assertSee('Syarat & Ketentuan', false)
            ->assertSee('Senin–Sabtu, 08.00–17.00 WIB')
            ->assertSee('QRIS')
            ->assertSee('Pengaduan konsumen RI')
            ->assertSee('FAQ')
            ->assertSee('name="payment_method"', false)
            ->assertSee('E-Wallet · GoPay');
    }
}
