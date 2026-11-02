<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ServiceQueue;
use Illuminate\Support\Facades\DB;

class ServiceQueueService
{
    public function ensureForConfirmedBooking(Booking $booking): ?ServiceQueue
    {
        if ($booking->booking_type === Booking::TYPE_COUNTER_SALE
            || ! in_array($booking->status, ['confirmed', 'completed'], true)) {
            return null;
        }

        return DB::transaction(function () use ($booking): ServiceQueue {
            $booking->store()->lockForUpdate()->firstOrFail();

            $existingQueue = ServiceQueue::query()
                ->where('booking_id', $booking->id)
                ->first();

            if ($existingQueue) {
                return $existingQueue;
            }

            $queueDate = $booking->booking_date;
            $nextNumber = (int) ServiceQueue::query()
                ->where('store_id', $booking->store_id)
                ->whereDate('queue_date', $queueDate)
                ->max('queue_number') + 1;

            $isCompleted = $booking->status === 'completed';

            return ServiceQueue::create([
                'booking_id' => $booking->id,
                'store_id' => $booking->store_id,
                'queue_date' => $queueDate,
                'queue_number' => $nextNumber,
                'status' => $isCompleted ? ServiceQueue::STATUS_COMPLETED : ServiceQueue::STATUS_WAITING,
                'completed_at' => $isCompleted ? now() : null,
            ]);
        });
    }
}
