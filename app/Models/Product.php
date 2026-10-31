<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected function casts(): array
    {
        return [
            'gallery_images' => 'array',
        ];
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->has_discount
            ? (float) $this->sale_price
            : (float) $this->price;
    }

    public function getHasDiscountAttribute(): bool
    {
        return $this->sale_price !== null
            && (float) $this->sale_price > 0
            && (float) $this->sale_price < (float) $this->price;
    }

    public function getDiscountPercentageAttribute(): int
    {
        if (! $this->has_discount || (float) $this->price <= 0) {
            return 0;
        }

        return (int) round((((float) $this->price - (float) $this->sale_price) / (float) $this->price) * 100);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
