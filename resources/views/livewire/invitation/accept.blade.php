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
            viewable
        />
        <flux:error name="password" />
    </flux:field>

    <flux:field>
        <flux:label>Confirm Password</flux:label>
        <flux:input
            wire:model="password_confirmation"
            type="password"
            placeholder="Confirm your password"
            viewable
        />
    </flux:field>

    <flux:button type="submit" variant="primary" class="w-full">
        Create Account
    </flux:button>
</form>
