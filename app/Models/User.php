<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

final class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function hasRole(Role $role): bool
    {
        return in_array($role->value, $this->roles ?? []);
    }

    public function hasAnyRole(array $roles): bool
    {
        $roleValues = array_map(fn (Role $role) => $role->value, $roles);

        return ! empty(array_intersect($roleValues, $this->roles ?? []));
    }

    public function addRole(Role $role): void
    {
        $roles = $this->roles ?? [];

        if (! in_array($role->value, $roles)) {
            $roles[] = $role->value;
            $this->roles = $roles;
            $this->save();
        }
    }

    public function removeRole(Role $role): void
    {
        $roles = $this->roles ?? [];
        $this->roles = array_values(array_filter($roles, fn ($r) => $r !== $role->value));
        $this->save();
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }

    public function isTeacher(): bool
    {
        return in_array(Role::TEACHER, $this->roles);
    }

    public function isStudent(): bool
    {
        return $this->hasRole(Role::STUDENT);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(self::class, 'teacher_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(self::class, 'teacher_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'invited_by');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'roles' => 'array',
        ];
    }
}
