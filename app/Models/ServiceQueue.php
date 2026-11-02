<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceQueue extends Model
{
    public const STATUS_WAITING = 'waiting';

    public const STATUS_IN_SERVICE = 'in_service';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'booking_id',
        'store_id',
        'queue_date',
        'queue_number',
        'status',
        'called_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'queue_date' => 'date',
            'called_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function getQueueCodeAttribute(): string
    {
        return 'A-'.str_pad((string) $this->queue_number, 3, '0', STR_PAD_LEFT);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
