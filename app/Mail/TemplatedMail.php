<?php

namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TemplatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, string|null>  $data
     */
    public function __construct(
        public EmailTemplate $template,
        public array $data = [],
    ) {}

    public function envelope(): Envelope
    {
        $rendered = $this->template->render($this->data);

        return new Envelope(
            subject: $rendered['subject'],
        );
    }

    public function content(): Content
    {
        $html = $this->template->render($this->data, asHtml: true);
        $text = $this->template->render($this->data);

        return new Content(
            html: 'emails.html',
            text: 'emails.plain',
            with: [
                'subject' => $html['subject'],
                'body' => $html['body'],
                'textBody' => $text['body'],
            ],
        );
    }
}
