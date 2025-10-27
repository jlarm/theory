<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Rule;
use Livewire\Volt\Component;

new class extends Component {
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

        Flux::toast('Invitation sent successfully.');

        $this->reset(['email', 'makeAdmin']);
    }

    #[Computed]
    public function pendingInvitations(): ?Collection
    {
        return Invitation::with('invitedBy')
            ->where('invited_by', auth()->id())
            ->whereJsonContains('roles', Role::TEACHER->value)
            ->whereNull('accepted_at')
            ->latest()
            ->get();
    }

    public function cancelInvitation(int $invitationId): void
    {
        $invitation = Invitation::findOrFail($invitationId);

        $this->authorize('delete', $invitation);

        $invitation->delete();

        Flux::toast('Invitation cancelled.');
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

        <flux:field>
            <flux:checkbox wire:model="makeAdmin" label="Also make this teacher an admin" />
        </flux:field>

        <flux:button type="submit" variant="primary">
            Send Invitation
        </flux:button>
    </form>

{{--    @if($this->pendingInvitations)--}}
{{--        <div class="mt-8">--}}
{{--            <flux:heading size="md">Pending Invitations</flux:heading>--}}

{{--            <div class="mt-4 space-y-2">--}}
{{--                @foreach ($this->pendingInvitations as $invitation)--}}
{{--                    <div class="flex items-center justify-between rounded-lg border p-4">--}}
{{--                        <div>--}}
{{--                            <p class="font-medium">{{ $invitation->email }}</p>--}}
{{--                            <p class="text-sm text-zinc-500">--}}
{{--                                Roles: {{ implode(', ', array_map('ucfirst', $invitation->roles)) }}--}}
{{--                            </p>--}}
{{--                            <p class="text-sm text-zinc-500">--}}
{{--                                Expires {{ $invitation->expires_at->diffForHumans() }}--}}
{{--                            </p>--}}
{{--                        </div>--}}

{{--                        <flux:button--}}
{{--                            wire:click="cancelInvitation({{ $invitation->id }})"--}}
{{--                            wire:confirm="Are you sure you want to cancel this invitation?"--}}
{{--                            variant="ghost"--}}
{{--                            size="sm"--}}
{{--                        >--}}
{{--                            Cancel--}}
{{--                        </flux:button>--}}
{{--                    </div>--}}
{{--                @endforeach--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    @endif--}}
</div>
