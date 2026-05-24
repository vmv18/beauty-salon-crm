<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppointmentRescheduledNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Appointment $appointment,
        public string $oldDate,
        public string $oldTime
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'appointment_rescheduled',
            'appointment_id' => $this->appointment->id,
            'service_name' => $this->appointment->service->name,
            'old_date' => $this->oldDate,
            'old_time' => $this->oldTime,
            'new_date' => $this->appointment->appointment_date->format('d.m.Y'),
            'new_time' => $this->appointment->appointment_time ? substr($this->appointment->appointment_time, 0, 5) : '',
            'message' => "Ваш запис на {$this->appointment->service->name} перенесено",
            'url' => route('client.appointments.show', $this->appointment),
        ];
    }
}

