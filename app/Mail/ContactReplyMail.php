<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * お問い合わせ者への返信メール。
 */
class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * 新しいメールインスタンスを作成する。
     */
    public function __construct(
        public readonly Contact $contact,
        public readonly string $body,
    ) {}

    /**
     * メールの件名を定義する。
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Re: {$this->contact->subject}",
        );
    }

    /**
     * メール本文を定義する。
     */
    public function content(): Content
    {
        return new Content(
            text: 'emails.contact-reply',
        );
    }
}
