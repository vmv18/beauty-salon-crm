<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewClientNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Client $client
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
            'type' => 'new_client',
            'client_id' => $this->client->id,
            'client_name' => $this->client->user->name,
            'client_email' => $this->client->user->email,
            'client_phone' => $this->client->phone,
            'message' => "Новий клієнт: {$this->client->user->name}",
            'url' => route('clients.show', $this->client),
        ];
    }
}
