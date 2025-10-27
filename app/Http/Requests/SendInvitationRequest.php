<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SendInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $role = $this->input('role');

        if ($role === 'teacher') {
            return $this->user()?->isAdmin() ?? false;
        }

        if ($role === 'student') {
            return $this->user()?->isTeacher() ?? false;
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
                Rule::unique('invitations', 'email')->where(fn ($query) => $query->whereNull('accepted_at')
                ),
            ],
            'role' => [
                'required',
                Rule::in(Role::cases()),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Please enter an email address.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email has already been invited or registered.',
            'role.required' => 'Please select a role.',
            'role.in' => 'Invalid role selected.',
        ];
    }
}
