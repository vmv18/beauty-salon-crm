<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Створюємо ролі для тестування
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'master']);
        Role::create(['name' => 'client']);
    }

    /** @test */
    public function user_can_view_login_page()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /** @test */
    public function authenticated_user_is_redirected_from_login_page()
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect(route('client.dashboard'));
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);
        $user->assignRole('client');

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('client.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function admin_user_is_redirected_to_admin_dashboard_after_login()
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);
        $user->assignRole('admin');

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    /** @test */
    public function manager_user_is_redirected_to_manager_dashboard_after_login()
    {
        $user = User::factory()->create([
            'email' => 'manager@example.com',
            'password' => Hash::make('password123'),
        ]);
        $user->assignRole('manager');

        $response = $this->post('/login', [
            'email' => 'manager@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('manager.dashboard'));
    }

    /** @test */
    public function master_user_is_redirected_to_master_dashboard_after_login()
    {
        $user = User::factory()->create([
            'email' => 'master@example.com',
            'password' => Hash::make('password123'),
        ]);
        $user->assignRole('master');

        $response = $this->post('/login', [
            'email' => 'master@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('master.dashboard'));
    }

    /** @test */
    public function client_user_is_redirected_to_client_dashboard_after_login()
    {
        $user = User::factory()->create([
            'email' => 'client@example.com',
            'password' => Hash::make('password123'),
        ]);
        $user->assignRole('client');

        $response = $this->post('/login', [
            'email' => 'client@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('client.dashboard'));
    }

    /** @test */
    public function unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function user_without_admin_role_cannot_access_admin_dashboard()
    {
        $user = User::factory()->create();
        $user->assignRole('client');

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_user_can_access_admin_dashboard()
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(200);
    }
}

