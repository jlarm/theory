<?php

declare(strict_types=1);

use App\Models\{Invitation, User};
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitationMail;
use Livewire\Volt\Component;
use Livewire\Attributes\{Rule, Computed};

new class extends Component
{
    #[Rule(['required', 'email', 'unique:users,email', 'unique:invitations,email'])]
    public string $email = '';

    public function sendInvitation(): void
    {
        $this->authorize('inviteStudent', Invitation::class);

        $this->validate();

        $invitation = Invitation::create([
            'email' => $this->email,
            'token' => Invitation::generateToken(),
            'role' => 'student',
            'invited_by' => auth()->id(),
            'teacher_id' => auth()->id(),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($this->email)->send(new InvitationMail($invitation));

        session()->flash('success', 'Student invitation sent successfully.');

        $this->reset('email');
        $this->dispatch('invitation-sent');
    }

    #[Computed]
    public function myStudents()
    {
        return auth()->user()->students()->latest()->get();
    }

    #[Computed]
    public function pendingInvitations()
    {
        return Invitation::where('invited_by', auth()->id())
            ->where('role', 'student')
            ->whereNull('accepted_at')
            ->latest()
            ->get();
    }

    public function cancelInvitation(int $invitationId): void
    {
        $invitation = Invitation::findOrFail($invitationId);

        $this->authorize('delete', $invitation);

        $invitation->delete();

        $this->dispatch('invitation-cancelled');
    }
}; ?>

<div>
    <flux:heading size="lg">Invite Student</flux:heading>

    <form wire:submit="sendInvitation" class="mt-6 space-y-6">
        <flux:field>
            <flux:label>Student Email Address</flux:label>
            <flux:input
                wire:model="email"
                type="email"
                placeholder="student@example.com"
            />
            <flux:error name="email" />
        </flux:field>

        <flux:button type="submit" variant="primary">
            Send Invitation
        </flux:button>
    </form>

    @if ($this->myStudents->isNotEmpty())
        <div class="mt-8">
            <flux:heading size="md">My Students</flux:heading>

            <div class="mt-4 space-y-2">
                @foreach ($this->myStudents as $student)
                    <div class="rounded-lg border p-4">
                        <p class="font-medium">{{ $student->name }}</p>
                        <p class="text-sm text-zinc-500">{{ $student->email }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

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
