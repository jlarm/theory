<div>
    <p>You have been invited by {{ $invitedBy }} to join Theory Charts</p>
    <a href="{{ $acceptUrl }}">Click Here to Register</a>
    <p>Your invitation will expire on {{ $expiresAt->format('F d, Y') }}</p>
    <p>If the link does not work, copy this link: {{ $acceptUrl }}</p>
</div>
