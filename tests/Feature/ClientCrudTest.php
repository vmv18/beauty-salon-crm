<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ClientCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Створюємо ролі
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'client']);

        // Створюємо адміністратора
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        // Створюємо менеджера
        $this->manager = User::factory()->create();
        $this->manager->assignRole('manager');

        Storage::fake('public');
    }

    /** @test */
    public function admin_can_view_clients_list()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->admin)->get('/clients');

        $response->assertStatus(200);
        $response->assertViewIs('clients.index');
        $response->assertSee($client->user->name);
    }

    /** @test */
    public function manager_can_view_clients_list()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->manager)->get('/clients');

        $response->assertStatus(200);
        $response->assertViewIs('clients.index');
    }

    /** @test */
    public function unauthenticated_user_cannot_view_clients_list()
    {
        $response = $this->get('/clients');

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_view_create_client_form()
    {
        $response = $this->actingAs($this->admin)->get('/clients/create');

        $response->assertStatus(200);
        $response->assertViewIs('clients.create');
    }

    /** @test */
    public function admin_can_create_client()
    {
        $user = User::factory()->create();

        $clientData = [
            'user_id' => $user->id,
            'phone' => '+380501234567',
            'email' => 'newclient@example.com',
            'date_of_birth' => '1990-01-01',
            'gender' => 'female',
            'address' => 'Test Address',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->admin)->post('/clients', $clientData);

        $response->assertRedirect();
        $this->assertDatabaseHas('clients', [
            'user_id' => $user->id,
            'phone' => '+380501234567',
            'email' => 'newclient@example.com',
        ]);
    }

    /** @test */
    public function admin_can_view_client_details()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->admin)->get("/clients/{$client->id}");

        $response->assertStatus(200);
        $response->assertViewIs('clients.show');
        $response->assertSee($client->user->name);
    }

    /** @test */
    public function admin_can_view_edit_client_form()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->admin)->get("/clients/{$client->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('clients.edit');
    }

    /** @test */
    public function admin_can_update_client()
    {
        $client = Client::factory()->create();

        $updateData = [
            'phone' => '+380509876543',
            'email' => 'updated@example.com',
            'address' => 'Updated Address',
            'status' => 'inactive',
        ];

        $response = $this->actingAs($this->admin)->put("/clients/{$client->id}", $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'phone' => '+380509876543',
            'email' => 'updated@example.com',
        ]);
    }

    /** @test */
    public function admin_can_delete_client()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/clients/{$client->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('clients', [
            'id' => $client->id,
        ]);
    }

    /** @test */
    public function client_creation_requires_valid_data()
    {
        $response = $this->actingAs($this->admin)->post('/clients', []);

        $response->assertSessionHasErrors(['user_id', 'phone', 'email']);
    }

    /** @test */
    public function client_update_requires_valid_data()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->admin)->put("/clients/{$client->id}", [
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function admin_can_search_clients()
    {
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();

        $response = $this->actingAs($this->admin)->get('/clients', [
            'search' => $client1->user->name,
        ]);

        $response->assertStatus(200);
        $response->assertSee($client1->user->name);
    }

    /** @test */
    public function admin_can_filter_clients_by_status()
    {
        $activeClient = Client::factory()->create(['status' => 'active']);
        $inactiveClient = Client::factory()->create(['status' => 'inactive']);

        $response = $this->actingAs($this->admin)->get('/clients', [
            'status' => 'active',
        ]);

        $response->assertStatus(200);
    }
}

