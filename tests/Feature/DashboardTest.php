<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard(): void
    {
        $user = User::factory()->create();
        $user->role = 'administrador';
        $user->save();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200)
            ->assertViewIs('admin.dashboard.dashboard');
    }
}
