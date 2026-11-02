<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->date('queue_date');
            $table->unsignedInteger('queue_number');
            $table->enum('status', ['waiting', 'in_service', 'completed'])->default('waiting');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['store_id', 'queue_date', 'queue_number']);
            $table->index(['store_id', 'queue_date', 'status']);
        });

        $confirmedBookings = DB::table('bookings')
            ->whereIn('status', ['confirmed', 'completed'])
            ->orderBy('store_id')
            ->orderBy('booking_date')
            ->orderBy('id')
            ->get();

        $sequences = [];
        foreach ($confirmedBookings as $booking) {
            $key = $booking->store_id.'|'.$booking->booking_date;
            $sequences[$key] = ($sequences[$key] ?? 0) + 1;
            $isCompleted = $booking->status === 'completed';

            DB::table('service_queues')->insert([
                'booking_id' => $booking->id,
                'store_id' => $booking->store_id,
                'queue_date' => $booking->booking_date,
                'queue_number' => $sequences[$key],
                'status' => $isCompleted ? 'completed' : 'waiting',
                'completed_at' => $isCompleted ? $booking->updated_at : null,
                'created_at' => $booking->created_at,
                'updated_at' => $booking->updated_at,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_queues');
    }
};
