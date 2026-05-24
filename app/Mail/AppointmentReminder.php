<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Appointment $appointment,
        public int $hoursBefore = 24
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $hoursText = $this->hoursBefore == 24 ? '24 години' : '2 години';
        return new Envelope(
            subject: "Нагадування про запис через {$hoursText} - Beauty Salon",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointment-reminder',
            with: [
                'appointment' => $this->appointment,
                'hoursBefore' => $this->hoursBefore,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
