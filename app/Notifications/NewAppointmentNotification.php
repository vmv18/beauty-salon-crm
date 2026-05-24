<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class NewAppointmentNotification extends Notification
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
        // Визначити URL залежно від ролі користувача
        $url = route('appointments.show', $this->appointment);
        if ($notifiable->hasRole('client')) {
            $url = route('client.appointments.show', $this->appointment);
        } elseif ($notifiable->hasRole('master')) {
            $url = route('master.appointments.show', $this->appointment);
        }

        // Визначити повідомлення залежно від ролі користувача
        if ($notifiable->hasRole('client')) {
            $message = "Ваш запис на {$this->appointment->service->name} створено";
        } else {
            $message = "Новий запис від {$this->appointment->client->user->name} на {$this->appointment->service->name}";
        }

        return [
            'type' => 'new_appointment',
            'appointment_id' => $this->appointment->id,
            'client_name' => $this->appointment->client->user->name,
            'service_name' => $this->appointment->service->name,
            'appointment_date' => $this->appointment->appointment_date->format('d.m.Y'),
            'appointment_time' => $this->appointment->appointment_time ? substr($this->appointment->appointment_time, 0, 5) : '',
            'message' => $message,
            'url' => $url,
        ];
    }
}
