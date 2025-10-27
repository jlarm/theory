<x-layouts.app :title="__('Teachers')">
    <div class="flex justify-between items-center">
        <flux:heading size="xl">Teachers</flux:heading>
        <livewire:teacher.invite />
    </div>
    <flux:separator variant="subtle" class="my-4" />
    <div>
        <flux:header class="bg-white dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-600 mb-4">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" />
            <flux:navbar class="max-lg:hidden -mb-px">
                <flux:navbar.item href="{{ route('teacher.index') }}">Registered</flux:navbar.item>
                <flux:navbar.item href="#">Invitations</flux:navbar.item>
                <flux:navbar.item href="#">Delete</flux:navbar.item>
            </flux:navbar>
        </flux:header>

        <livewire:teacher.index />
    </div>
</x-layouts.app>
