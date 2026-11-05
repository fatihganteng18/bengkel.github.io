<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->string('category')->change();
            $table->string('sub_category', 160)->nullable();
            $table->string('inlet_size', 30)->nullable();
            $table->string('sound_type', 80)->nullable();
            $table->string('material', 80)->nullable();
            $table->string('engine_cc', 80)->nullable();
            $table->index(['category', 'sub_category']);
        });
    }

    public function down(): void
    {
        DB::table('products')
            ->where('category', 'exhaust')
            ->update(['category' => 'sparepart']);

        Schema::table('products', function (Blueprint $table): void {
            $table->dropIndex(['category', 'sub_category']);
            $table->dropColumn(['sub_category', 'inlet_size', 'sound_type', 'material', 'engine_cc']);
            $table->enum('category', ['tire', 'oil', 'sparepart', 'service'])->change();
        });
    }
};
