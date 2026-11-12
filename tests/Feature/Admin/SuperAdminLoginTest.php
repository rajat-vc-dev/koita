<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('Superadmin Sign In');
    }

    public function test_superadmin_can_authenticate(): void
    {
        $user = User::factory()->superAdmin()->create([
            'email' => 'superadmin@koita.test',
            'password' => 'password',
        ]);

        $this->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_non_superadmin_cannot_authenticate(): void
    {
        User::factory()->create([
            'email' => 'user@example.com',
            'password' => 'password',
            'role' => User::ROLE_USER,
        ]);

        $this->post(route('admin.login.store'), [
            'email' => 'user@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_dashboard_requires_superadmin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
