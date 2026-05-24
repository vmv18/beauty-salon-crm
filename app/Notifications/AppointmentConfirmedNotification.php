<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppointmentConfirmedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Appointment $appointment
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
            'type' => 'appointment_confirmed',
            'appointment_id' => $this->appointment->id,
            'service_name' => $this->appointment->service->name,
            'appointment_date' => $this->appointment->appointment_date->format('d.m.Y'),
            'appointment_time' => $this->appointment->appointment_time ? substr($this->appointment->appointment_time, 0, 5) : '',
            'message' => "Ваш запис на {$this->appointment->service->name} підтверджено",
            'url' => route('client.appointments.show', $this->appointment),
        ];
    }
}

