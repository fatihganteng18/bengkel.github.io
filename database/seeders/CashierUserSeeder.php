<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use LogicException;

class CashierUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = Str::lower((string) config('planetban.cashier.email'));
        $password = config('planetban.cashier.password');

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new LogicException('Set a valid CASHIER_EMAIL before seeding the cashier.');
        }

        if (! is_string($password) || trim($password) === '') {
            throw new LogicException('Set CASHIER_PASSWORD in the environment before seeding the cashier.');
        }

        $cashier = User::query()->firstOrNew(['email' => $email]);
        $cashier->forceFill([
            'name' => 'Arisa Knalpot Kasir',
            'password' => Hash::make($password),
            'role' => User::ROLE_CASHIER,
        ])->save();
    }
}
