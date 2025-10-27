<?php

declare(strict_types=1);

namespace App\Livewire\Teacher;

use App\Enums\Role;
use App\Models\User;
use Illuminate\View\View;
use Livewire\Component;

class Index extends Component
{
    public function render(): View
    {
        return view('livewire.teacher.index', [
            'teachers' => User::query()
                ->whereJsonContains('roles', Role::TEACHER)
                ->get(),
        ]);
    }
}
