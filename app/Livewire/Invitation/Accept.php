<?php

declare(strict_types=1);

namespace App\Livewire\Invitation;

use App\Models\Invitation;
use App\Models\User;
use Auth;
use Flux;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Accept extends Component
{
    #[Locked]
    public Invitation $invitation;
    #[Rule(['required', 'string', 'max:255'])]
    public string $name = '';
    #[Rule(['required', 'string', 'min:8', 'confirmed'])]
    public string $password = '';
    public string $password_confirmation = '';

    public function acceptInvitation(): void
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->invitation->email,
            'password' => bcrypt($this->password),
            'roles' => $this->invitation->roles,
            'teacher_id' => $this->invitation->teacher_id,
            'email_verified_at' => now(),
        ]);

        $this->invitation->update([
            'accepted_at' => now(),
        ]);

        Auth::login($user);

        Flux::toast('Welcome! Your account has been created.');

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.invitation.accept');
    }
}
