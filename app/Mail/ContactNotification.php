<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactNotification extends Mailable
{
    use Queueable,SerializesModels;

    public function __construct(public ContactMessage $contactMessage, public string $prefix = 'Website enquiry') {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: trim($this->prefix.' — '.($this->contactMessage->subject ?: 'New message')), replyTo: [$this->contactMessage->email]);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.contact-notification');
    }
}
