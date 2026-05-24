<?php

namespace App\Jobs;

use App\Mail\AppointmentReminder;
use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class AppointmentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Appointment $appointment,
        public int $hoursBefore = 24
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Перевірити, чи запис все ще активний
        if (!in_array($this->appointment->status, ['scheduled', 'confirmed'])) {
            return;
        }

        // Завантажити зв'язки
        $this->appointment->load(['client.user', 'employee.user', 'service']);

        // Відправити нагадування клієнту
        try {
            Mail::to($this->appointment->client->user->email)
                ->send(new AppointmentReminder($this->appointment, $this->hoursBefore));
        } catch (\Exception $e) {
            \Log::error('Failed to send appointment reminder: ' . $e->getMessage());
            throw $e;
        }
    }
}
