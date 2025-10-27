<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\Invitation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{Mail, URL};
use Livewire\Attributes\Rule;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    #[Rule(['required', 'email', 'unique:users,email', 'unique:invitations,email'])]
    public string $email = '';

    public function sendInvitation(): void
    {
        $this->authorize('inviteTeacher', Invitation::class);

        $this->validate();

        $invitation = Invitation::create([
            'email' => $this->email,
            'token' => Invitation::generateToken(),
            'role' => Role::TEACHER,
            'invited_by' => auth()->id(),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($this->email)->send(new InvitationMail($invitation));

        $this->reset('email');
    }

    #[Computed]
    public function pendingInvitations(): Collection
    {
        return Invitation::with('invitedBy')
            ->where('invited_by', auth()->id())
            ->where('role', Role::TEACHER)
            ->whereNull('accepted_at')
            ->latest()
            ->get();
    }

    public function cancelInvitation(int $invitationId): void
    {
        $invitation = Invitation::findOrFail($invitationId);

        $this->authorize('delete', $invitation);

        $invitation->delete();
    }
} ?>

<div>
    <flux:heading size="lg">Invite Teacher</flux:heading>

    <form wire:submit.prevent="sendInvitation" class="mt-6 space-y-6">
        <flux:field>
            <flux:label>Email Address</flux:label>
            <flux:input
                wire:model="email"
                type="email"
                required
            />
            <flux:error name="email" />
        </flux:field>

        <flux:button type="submit" variant="primary">
            Send Invitation
        </flux:button>
    </form>

    @if ($this->pendingInvitations->isNotEmpty())
        <div class="mt-8">
            <flux:heading size="md">Pending Invitations</flux:heading>

            <div class="mt-4 space-y-2">
                @foreach ($this->pendingInvitations as $invitation)
                    <div class="flex items-center justify-between rounded-lg border p-4">
                        <div>
                            <p class="font-medium">{{ $invitation->email }}</p>
                            <p class="text-sm text-zinc-500">
                                Expires {{ $invitation->expires_at->diffForHumans() }}
                            </p>
                        </div>

                        <flux:button
                            wire:click="cancelInvitation({{ $invitation->id }})"
                            wire:confirm="Are you sure you want to cancel this invitation?"
                            variant="ghost"
                            size="sm"
                        >
                            Cancel
                        </flux:button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
