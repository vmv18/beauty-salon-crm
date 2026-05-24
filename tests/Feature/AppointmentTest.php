<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;
    protected Client $client;
    protected Employee $employee;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Створюємо ролі
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'master']);
        Role::create(['name' => 'client']);

        // Створюємо адміністратора
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        // Створюємо менеджера
        $this->manager = User::factory()->create();
        $this->manager->assignRole('manager');

        // Створюємо клієнта
        $clientUser = User::factory()->create();
        $clientUser->assignRole('client');
        $this->client = Client::factory()->create([
            'user_id' => $clientUser->id,
            'status' => 'active',
        ]);

        // Створюємо майстра
        $employeeUser = User::factory()->create();
        $employeeUser->assignRole('master');
        $this->employee = Employee::factory()->create([
            'user_id' => $employeeUser->id,
            'status' => 'active',
        ]);

        // Створюємо категорію та послугу
        $category = ServiceCategory::factory()->create();
        $this->service = Service::factory()->create([
            'category_id' => $category->id,
            'is_active' => true,
            'duration' => 60,
            'price' => 500.00,
        ]);

        Storage::fake('public');
    }

    /** @test */
    public function admin_can_view_appointments_list()
    {
        $appointment = Appointment::factory()->create([
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
        ]);

        $response = $this->actingAs($this->admin)->get('/appointments');

        $response->assertStatus(200);
        $response->assertViewIs('appointments.index');
    }

    /** @test */
    public function admin_can_view_create_appointment_form()
    {
        $response = $this->actingAs($this->admin)->get('/appointments/create');

        $response->assertStatus(200);
        $response->assertViewIs('appointments.create');
    }

    /** @test */
    public function admin_can_create_appointment()
    {
        $appointmentData = [
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
            'appointment_date' => now()->addDays(7)->format('Y-m-d'),
            'appointment_time' => '10:00',
            'duration' => 60,
            'price' => 500.00,
            'status' => 'scheduled',
            'notes' => 'Test appointment notes',
        ];

        $response = $this->actingAs($this->admin)->post('/appointments', $appointmentData);

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
            'status' => 'scheduled',
        ]);
    }

    /** @test */
    public function appointment_creation_requires_valid_data()
    {
        $response = $this->actingAs($this->admin)->post('/appointments', []);

        $response->assertSessionHasErrors([
            'client_id',
            'employee_id',
            'service_id',
            'appointment_date',
            'appointment_time',
        ]);
    }

    /** @test */
    public function admin_can_view_appointment_details()
    {
        $appointment = Appointment::factory()->create([
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
        ]);

        $response = $this->actingAs($this->admin)->get("/appointments/{$appointment->id}");

        $response->assertStatus(200);
        $response->assertViewIs('appointments.show');
    }

    /** @test */
    public function admin_can_cancel_appointment()
    {
        $appointment = Appointment::factory()->create([
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->admin)->post("/appointments/{$appointment->id}/cancel", [
            'cancellation_reason' => 'Client requested cancellation',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'cancelled',
            'cancellation_reason' => 'Client requested cancellation',
        ]);
    }

    /** @test */
    public function appointment_cancellation_requires_reason()
    {
        $appointment = Appointment::factory()->create([
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->admin)->post("/appointments/{$appointment->id}/cancel", []);

        $response->assertSessionHasErrors(['cancellation_reason']);
    }

    /** @test */
    public function admin_can_confirm_appointment()
    {
        $appointment = Appointment::factory()->create([
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($this->admin)->post("/appointments/{$appointment->id}/confirm");

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'confirmed',
        ]);
    }

    /** @test */
    public function admin_can_complete_appointment()
    {
        $appointment = Appointment::factory()->create([
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
            'status' => 'confirmed',
            'price' => 500.00,
        ]);

        $response = $this->actingAs($this->admin)->post("/appointments/{$appointment->id}/complete");

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function completed_appointment_earns_loyalty_points()
    {
        $appointment = Appointment::factory()->create([
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
            'status' => 'confirmed',
            'price' => 1000.00, // 1% = 10 балів, але мінімум 10
        ]);

        $initialPoints = $this->client->loyalty_points;

        $this->actingAs($this->admin)->post("/appointments/{$appointment->id}/complete");

        $this->client->refresh();
        $this->assertGreaterThan($initialPoints, $this->client->loyalty_points);
    }

    /** @test */
    public function manager_can_create_appointment()
    {
        $appointmentData = [
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
            'appointment_date' => now()->addDays(7)->format('Y-m-d'),
            'appointment_time' => '14:00',
            'duration' => 60,
            'price' => 500.00,
            'status' => 'scheduled',
        ];

        $response = $this->actingAs($this->manager)->post('/appointments', $appointmentData);

        $response->assertRedirect();
        $this->assertDatabaseHas('appointments', [
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_appointment()
    {
        $response = $this->post('/appointments', []);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_search_appointments()
    {
        $appointment = Appointment::factory()->create([
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
        ]);

        $response = $this->actingAs($this->admin)->get('/appointments', [
            'search' => $this->client->user->name,
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_filter_appointments_by_status()
    {
        $scheduledAppointment = Appointment::factory()->create([
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
            'status' => 'scheduled',
        ]);

        $completedAppointment = Appointment::factory()->create([
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin)->get('/appointments', [
            'status' => 'scheduled',
        ]);

        $response->assertStatus(200);
    }
}

