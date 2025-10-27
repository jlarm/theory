<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Joe Lohr',
            'email' => 'emailme@joelohr.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'roles' => [Role::ADMIN],
        ]);
    }
}
