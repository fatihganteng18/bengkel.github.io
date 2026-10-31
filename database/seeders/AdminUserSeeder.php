<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use LogicException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = Str::lower((string) config('planetban.admin.email'));
        $password = config('planetban.admin.password');

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new LogicException('Set a valid ADMIN_EMAIL before seeding the administrator.');
        }

        if (! is_string($password) || trim($password) === '') {
            throw new LogicException('Set ADMIN_PASSWORD in the environment before seeding the administrator.');
        }

        $admin = User::query()->firstOrNew(['email' => $email]);
        $admin->forceFill([
            'name' => 'Planet Ban Admin',
            'password' => Hash::make($password),
            'role' => 'admin',
        ])->save();
    }
}
