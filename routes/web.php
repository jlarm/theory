<?php

declare(strict_types=1);

use App\Http\Controllers\AcceptInvitationController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('profile.edit');
    Route::get('settings/password', Password::class)->name('user-password.edit');
    Route::get('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

// Admin routes
Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::view('teachers', 'teacher.index')->name('teacher.index');

    Route::get('/admin/invite-teacher', function () {
        abort_unless(auth()->user()?->isAdmin(), 403);
        return view('livewire.teacher-invite');
    })->name('admin.invite-teacher');
});

// Teacher routes
Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/teacher/invite-student', function () {
        abort_unless(auth()->user()?->isTeacher(), 403);
        return view('livewire.student-invite');
    })->name('teacher.invite-student');

    Route::get('/teacher/students', function () {
        abort_unless(auth()->user()?->isTeacher(), 403);
        return view('teacher.students');
    })->name('teacher.students');
});

// Public invitation acceptance
Route::get('/invitation/{token}', AcceptInvitationController::class)->name('invitation.accept');
