<div>
    <flux:modal.trigger name="edit-profile">
        <flux:button size="sm" variant="primary">Invite Teacher</flux:button>
    </flux:modal.trigger>

    <flux:modal name="edit-profile" class="md:w-96">
        <form wire:submit.prevent="sendInvitation" class="space-y-6">
            <div>
                <flux:heading size="lg">Invite Teacher</flux:heading>
            </div>

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

            <div class="flex">
                <flux:spacer />

                <flux:button type="submit" variant="primary">Send Invitation</flux:button>
            </div>
        </form>
    </flux:modal>

</div>
