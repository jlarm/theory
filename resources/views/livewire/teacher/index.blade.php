<flux:table>
    <flux:table.columns>
        <flux:table.column>Name</flux:table.column>
        <flux:table.column>Date</flux:table.column>
        <flux:table.column>Status</flux:table.column>
        <flux:table.column>Amount</flux:table.column>
    </flux:table.columns>

    <flux:table.rows>
        @foreach($teachers as $teacher)
            <flux:table.row>
                <flux:table.cell>{{ $teacher->name }}</flux:table.cell>
                <flux:table.cell>Jul 27, 9:30 AM</flux:table.cell>
                <flux:table.cell><flux:badge color="green" size="sm" inset="top bottom">Paid</flux:badge></flux:table.cell>
                <flux:table.cell variant="strong">$31.00</flux:table.cell>
            </flux:table.row>
        @endforeach
    </flux:table.rows>
</flux:table>
