<?php

namespace App\Console\Commands;

use App\Jobs\AppointmentReminderJob;
use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders {--hours=24 : Hours before appointment}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send appointment reminders to clients';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $hoursBefore = (int) $this->option('hours');
        
        if (!in_array($hoursBefore, [24, 2])) {
            $this->error('Hours must be either 24 or 2');
            return 1;
        }

        $this->info("Sending {$hoursBefore}-hour reminders...");

        // Розрахувати час запису
        $targetDateTime = Carbon::now()->addHours($hoursBefore);
        $targetDate = $targetDateTime->toDateString();
        $targetTime = $targetDateTime->format('H:i');

        // Знайти записи, які потрібно нагадати
        $appointments = Appointment::where('appointment_date', $targetDate)
            ->whereTime('appointment_time', '>=', $targetTime)
            ->whereTime('appointment_time', '<=', $targetDateTime->copy()->addMinutes(30)->format('H:i'))
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->with(['client.user', 'employee.user', 'service'])
            ->get();

        $count = 0;

        foreach ($appointments as $appointment) {
            // Перевірити, чи не було вже відправлено нагадування за цей час
            // (можна додати поле в таблицю appointments для відстеження)
            
            try {
                AppointmentReminderJob::dispatch($appointment, $hoursBefore);
                $count++;
                $this->line("Queued reminder for appointment #{$appointment->id} - {$appointment->client->user->name}");
            } catch (\Exception $e) {
                $this->error("Failed to queue reminder for appointment #{$appointment->id}: " . $e->getMessage());
            }
        }

        $this->info("Queued {$count} reminder(s) for {$hoursBefore}-hour reminders.");

        return 0;
    }
}
