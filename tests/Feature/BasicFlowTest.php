<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Platform;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_user_can_register_via_filament_if_enabled()
    {
        // This tests the route exists and is accessible
        $response = $this->get('/admin/register');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_admin_panel()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        $response = $this->get('/admin');
        $response->assertStatus(200);
    }

    public function test_regular_user_can_access_dashboard()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }
}
