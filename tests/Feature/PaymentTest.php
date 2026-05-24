<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;
    protected Client $client;
    protected Employee $employee;
    protected Service $service;
    protected Appointment $appointment;

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

        // Створюємо запис
        $this->appointment = Appointment::factory()->create([
            'client_id' => $this->client->id,
            'employee_id' => $this->employee->id,
            'service_id' => $this->service->id,
            'status' => 'completed',
            'price' => 500.00,
        ]);

        Storage::fake('public');
    }

    /** @test */
    public function admin_can_view_payments_list()
    {
        $payment = Payment::factory()->create([
            'client_id' => $this->client->id,
            'appointment_id' => $this->appointment->id,
            'amount' => 500.00,
        ]);

        $response = $this->actingAs($this->admin)->get('/payments');

        $response->assertStatus(200);
        $response->assertViewIs('payments.index');
    }

    /** @test */
    public function admin_can_view_create_payment_form()
    {
        $response = $this->actingAs($this->admin)->get('/payments/create');

        $response->assertStatus(200);
        $response->assertViewIs('payments.create');
    }

    /** @test */
    public function admin_can_create_payment_without_appointment()
    {
        $paymentData = [
            'client_id' => $this->client->id,
            'amount' => 500.00,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
            'status' => 'completed',
            'notes' => 'Test payment',
        ];

        $response = $this->actingAs($this->admin)->post('/payments', $paymentData);

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', [
            'client_id' => $this->client->id,
            'amount' => 500.00,
            'payment_method' => 'cash',
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function admin_can_create_payment_with_appointment()
    {
        $paymentData = [
            'client_id' => $this->client->id,
            'appointment_id' => $this->appointment->id,
            'amount' => 500.00,
            'payment_method' => 'card',
            'payment_date' => now()->format('Y-m-d'),
            'status' => 'completed',
        ];

        $response = $this->actingAs($this->admin)->post('/payments', $paymentData);

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', [
            'client_id' => $this->client->id,
            'appointment_id' => $this->appointment->id,
            'amount' => 500.00,
            'payment_method' => 'card',
        ]);
    }

    /** @test */
    public function payment_creation_requires_valid_data()
    {
        $response = $this->actingAs($this->admin)->post('/payments', []);

        $response->assertSessionHasErrors([
            'client_id',
            'amount',
            'payment_method',
        ]);
    }

    /** @test */
    public function payment_amount_must_be_positive()
    {
        $paymentData = [
            'client_id' => $this->client->id,
            'amount' => -100.00,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->admin)->post('/payments', $paymentData);

        $response->assertSessionHasErrors(['amount']);
    }

    /** @test */
    public function admin_can_view_payment_details()
    {
        $payment = Payment::factory()->create([
            'client_id' => $this->client->id,
            'appointment_id' => $this->appointment->id,
            'amount' => 500.00,
        ]);

        $response = $this->actingAs($this->admin)->get("/payments/{$payment->id}");

        $response->assertStatus(200);
        $response->assertViewIs('payments.show');
    }

    /** @test */
    public function admin_can_view_edit_payment_form()
    {
        $payment = Payment::factory()->create([
            'client_id' => $this->client->id,
            'appointment_id' => $this->appointment->id,
        ]);

        $response = $this->actingAs($this->admin)->get("/payments/{$payment->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('payments.edit');
    }

    /** @test */
    public function admin_can_update_payment()
    {
        $payment = Payment::factory()->create([
            'client_id' => $this->client->id,
            'appointment_id' => $this->appointment->id,
            'amount' => 500.00,
            'status' => 'pending',
        ]);

        $updateData = [
            'amount' => 600.00,
            'status' => 'completed',
            'payment_method' => 'card',
            'notes' => 'Updated payment',
        ];

        $response = $this->actingAs($this->admin)->put("/payments/{$payment->id}", $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'amount' => 600.00,
            'status' => 'completed',
        ]);
    }

    /** @test */
    public function admin_can_delete_payment()
    {
        $payment = Payment::factory()->create([
            'client_id' => $this->client->id,
            'appointment_id' => $this->appointment->id,
        ]);

        $response = $this->actingAs($this->admin)->delete("/payments/{$payment->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('payments', [
            'id' => $payment->id,
        ]);
    }

    /** @test */
    public function manager_can_create_payment()
    {
        $paymentData = [
            'client_id' => $this->client->id,
            'appointment_id' => $this->appointment->id,
            'amount' => 500.00,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
            'status' => 'completed',
        ];

        $response = $this->actingAs($this->manager)->post('/payments', $paymentData);

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', [
            'client_id' => $this->client->id,
            'appointment_id' => $this->appointment->id,
            'amount' => 500.00,
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_payment()
    {
        $response = $this->post('/payments', []);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_search_payments()
    {
        $payment = Payment::factory()->create([
            'client_id' => $this->client->id,
            'appointment_id' => $this->appointment->id,
            'amount' => 500.00,
        ]);

        $response = $this->actingAs($this->admin)->get('/payments', [
            'search' => $this->client->user->name,
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_filter_payments_by_status()
    {
        $completedPayment = Payment::factory()->create([
            'client_id' => $this->client->id,
            'appointment_id' => $this->appointment->id,
            'status' => 'completed',
        ]);

        $pendingPayment = Payment::factory()->create([
            'client_id' => $this->client->id,
            'appointment_id' => $this->appointment->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get('/payments', [
            'status' => 'completed',
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_filter_payments_by_payment_method()
    {
        $cashPayment = Payment::factory()->create([
            'client_id' => $this->client->id,
            'appointment_id' => $this->appointment->id,
            'payment_method' => 'cash',
        ]);

        $cardPayment = Payment::factory()->create([
            'client_id' => $this->client->id,
            'appointment_id' => $this->appointment->id,
            'payment_method' => 'card',
        ]);

        $response = $this->actingAs($this->admin)->get('/payments', [
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function payment_can_be_created_with_document()
    {
        $file = \Illuminate\Http\UploadedFile::fake()->create('document.pdf', 100);

        $paymentData = [
            'client_id' => $this->client->id,
            'appointment_id' => $this->appointment->id,
            'amount' => 500.00,
            'payment_method' => 'cash',
            'payment_date' => now()->format('Y-m-d'),
            'status' => 'completed',
            'document' => $file,
        ];

        $response = $this->actingAs($this->admin)->post('/payments', $paymentData);

        $response->assertRedirect();
        $payment = Payment::where('client_id', $this->client->id)->first();
        $this->assertNotNull($payment->document);
        Storage::disk('public')->assertExists($payment->document);
    }
}

