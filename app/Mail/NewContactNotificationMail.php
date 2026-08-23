<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * 新規お問い合わせ受付を管理者へ通知するメール。
 */
class NewContactNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * 新しいメールインスタンスを作成する。
     */
    public function __construct(
        public readonly Contact $contact,
    ) {}

    /**
     * メールの件名を定義する。
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "【お問い合わせ】{$this->contact->subject}",
        );
    }

    /**
     * メール本文を定義する。
     */
    public function content(): Content
    {
        return new Content(
            text: 'emails.new-contact-notification',
        );
    }
}
