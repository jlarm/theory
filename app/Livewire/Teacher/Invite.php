<?php

declare(strict_types=1);

namespace App\Livewire\Teacher;

use App\Enums\Role;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use Flux\Flux;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Invite extends Component
{
    #[Rule(['required', 'email', 'unique:users,email', 'unique:invitations,email'])]
    public string $email = '';
    public bool $makeAdmin = false;

    public function sendInvitation(): void
    {
        $this->authorize('inviteTeacher', Invitation::class);

        $this->validate();

        $roles = [Role::TEACHER->value];

        if ($this->makeAdmin) {
            $roles[] = Role::ADMIN->value;
        }

        $invitation = Invitation::create([
            'email' => $this->email,
            'token' => Invitation::generateToken(),
            'roles' => $roles,
            'invited_by' => auth()->id(),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($this->email)->send(new InvitationMail($invitation));

        Flux::toast('Invitation sent successfully');

        $this->reset(['email', 'makeAdmin']);
    }

    public function render(): View
    {
        return view('livewire.teacher.invite');
    }
}
