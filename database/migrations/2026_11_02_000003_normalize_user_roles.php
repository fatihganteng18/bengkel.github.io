<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->where(function ($query): void {
                $query->whereNull('role')
                    ->orWhereNotIn('role', ['admin', 'cashier', 'user']);
            })
            ->update(['role' => 'user']);
    }

    public function down(): void
    {
    }
};
