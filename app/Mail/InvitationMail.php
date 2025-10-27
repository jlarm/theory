<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class InvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $role;

    public function __construct(
        public Invitation $invitation,
    ){
        $this->role = $this->getRole($this->invitation->roles);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You've been invited to join as a {$this->role}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
            with: [
                'acceptUrl' => URL::route('invitation.accept', [
                    'token' => $this->invitation->token,
                ]),
                'invitedBy' => $this->invitation->invitedBy->name,
                'expiresAt' => $this->invitation->expires_at,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }

    private function getRole($roles): string
    {
        $nonAdminRole = array_diff($roles, ['admin']);

        return implode(',', $nonAdminRole);
    }
}
