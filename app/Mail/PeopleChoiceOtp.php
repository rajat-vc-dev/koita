<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PeopleChoiceOtp extends Mailable
{
    public function __construct(private readonly string $code) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your People’s Choice Awards verification code');
    }

    public function content(): Content
    {
        return new Content(text: 'people-choice.otp-email', with: ['code' => $this->code]);
    }
}
