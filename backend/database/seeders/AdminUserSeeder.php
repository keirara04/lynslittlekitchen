<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = config('app.admin_email');
        $password = config('app.admin_password');

        if (! app()->environment('local', 'testing') && ($email === 'admin@example.com' || $password === 'password')) {
            throw new \RuntimeException(
                'Refusing to seed admin user with default credentials outside local/testing. Set ADMIN_EMAIL and ADMIN_PASSWORD in .env.',
            );
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('app.admin_name'),
                'password' => $password,
                'role' => UserRole::Admin,
            ],
        );
    }
}
