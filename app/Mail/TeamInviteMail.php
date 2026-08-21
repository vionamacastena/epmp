<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TeamInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $role;
    public $inviter;
    public $company;

    public function __construct($email, $role, $inviter, $company)
    {
        $this->email = $email;
        $this->role = $role;
        $this->inviter = $inviter;
        $this->company = $company;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You're invited to join {$this->company} on EPMP",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.team-invite',
            with: [
                'email' => $this->email,
                'role' => $this->role,
                'inviter' => $this->inviter,
                'company' => $this->company,
                'acceptUrl' => url('/accept-invite?email=' . $this->email),
            ],
        );
    }
}
