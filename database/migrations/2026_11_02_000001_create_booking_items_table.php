<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->decimal('unit_price', 12, 2);
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
        });

        DB::table('bookings')
            ->whereNotNull('product_id')
            ->orderBy('id')
            ->each(function (object $booking): void {
                $product = DB::table('products')->where('id', $booking->product_id)->first();

                if (! $product) {
                    return;
                }

                $price = $product->sale_price !== null
                    && (float) $product->sale_price > 0
                    && (float) $product->sale_price < (float) $product->price
                        ? $product->sale_price
                        : $product->price;

                DB::table('booking_items')->insert([
                    'booking_id' => $booking->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $price,
                    'quantity' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_items');
    }
};
