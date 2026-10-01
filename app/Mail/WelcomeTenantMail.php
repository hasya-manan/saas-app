<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeTenantMail extends Mailable implements ShouldQueue 
{
    use Queueable, SerializesModels;

     /**
     * Number of times the job may be attempted.
     */
    public int $tries = 5;

    /**
     * Backoff schedule (seconds) between retries.
     */
    public function backoff(): array
    {
        return [60, 300, 900, 1800]; // 1m, 5m, 15m, 30m
    }
    /**
     * Create a new message instance.
     */
    public function __construct(public string $companyName, public string $adminName,public string $adminEmail, public string $resetUrl)
    {
        
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
             to: $this->adminEmail,
            subject: 'Welcome to ' . config('app.name') . ' — Your HR System is Ready',
        );
    }

    /**
     * Get the message content definition.
     */
     public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-tenant',
            with: [
                'companyName' => $this->companyName,
                'adminName'   => $this->adminName,
                'adminEmail'  => $this->adminEmail,
                'resetUrl'    => $this->resetUrl,
                'appName'     => config('app.name'),
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
