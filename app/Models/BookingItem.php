<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingItem extends Model
{
    protected $fillable = [
        'product_id',
        'product_name',
        'unit_price',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
