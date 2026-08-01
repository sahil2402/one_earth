<?php

namespace Tests\Feature;

use App\Constants\OwnerCredentials;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_log_in_and_view_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => OwnerCredentials::EMAIL,
            'password' => OwnerCredentials::PASSWORD,
        ]);

        $response->assertRedirect('/dashboard');
        $this->get('/dashboard')->assertOk()->assertSee('Welcome back, Owner.');
    }

    public function test_dashboard_requires_a_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }
}
