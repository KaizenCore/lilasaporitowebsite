<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminBookingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking
    ) {}

    public function envelope(): Envelope
    {
        $classTitle = $this->booking->artClass?->title ?? 'Art Class';
        $customerName = $this->booking->user?->name ?? 'Someone';

        return new Envelope(
            subject: 'New Booking! ' . $customerName . ' booked ' . $classTitle,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-booking-notification',
            with: [
                'booking' => $this->booking,
                'artClass' => $this->booking->artClass,
                'customer' => $this->booking->user,
                'ticketCode' => $this->booking->ticket_code,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
