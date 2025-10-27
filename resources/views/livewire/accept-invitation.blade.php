<?php

declare(strict_types=1);

use App\Models\Invitation;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\{Auth, Hash};
use Livewire\Attributes\Locked;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;

new class extends Component {
    #[Locked]
    public ?Invitation $invitation = null;

    #[Rule(['required', 'string', 'max:255'])]
    public string $name = '';

    #[Rule(['required', 'string', 'min:8', 'confirmed'])]
    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        if ($this->invitation->isExpired()) {
            abort(410, 'This invitation has expired.');
        }
    }

    public function acceptInvitation(): void
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->invitation->email,
            'password' => Hash::make($this->password),
            'roles' => $this->invitation->roles,
            'teacher_id' => $this->invitation->teacher_id,
            'email_verified_at' => now(),
        ]);

        $this->invitation->update([
            'accepted_at' => now(),
        ]);

        Auth::login($user);

        Flux::toast('Welcome! Your account has been created.');

        $this->redirect('/dashboard', navigate: true);
    }
} ?>

<div>
    <flux:heading size="xl">Accept Invitation</flux:heading>

    <div class="mt-4">
        <flux:text>
            You've been invited to join as a <strong>{{ implode(' & ', array_map('ucfirst', $invitation->roles)) }}</strong>.
        </flux:text>

        @if (in_array('student', $invitation->roles) && $invitation->teacher)
            <flux:text class="mt-2">
                You'll be assigned to <strong>{{ $invitation->teacher->name }}</strong>.
            </flux:text>
        @endif
    </div>

    <form wire:submit="acceptInvitation" class="mt-6 space-y-6">
        <flux:field>
            <flux:label>Name</flux:label>
            <flux:input
                wire:model="name"
                type="text"
                placeholder="Your full name"
            />
            <flux:error name="name" />
        </flux:field>

        <flux:field>
            <flux:label>Email</flux:label>
            <flux:input
                type="email"
                :value="$invitation->email"
                disabled
            />
        </flux:field>

        <flux:field>
            <flux:label>Password</flux:label>
            <flux:input
                wire:model="password"
                type="password"
                placeholder="Create a secure password"
            />
            <flux:error name="password" />
        </flux:field>

        <flux:field>
            <flux:label>Confirm Password</flux:label>
            <flux:input
                wire:model="password_confirmation"
                type="password"
                placeholder="Confirm your password"
            />
        </flux:field>

        <flux:button type="submit" variant="primary" class="w-full">
            Create Account
        </flux:button>
    </form>
</div>
