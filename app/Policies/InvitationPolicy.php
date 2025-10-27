<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

final class InvitationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTeacher();
    }

    public function delete(User $user, Invitation $invitation): bool
    {
        return $invitation->invited_by === $user->id;
    }

    public function inviteTeacher(User $user): bool
    {
        return $user->isAdmin();
    }

    public function inviteStudent(User $user): bool
    {
        return $user->isTeacher();
    }
}
