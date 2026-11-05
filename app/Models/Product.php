<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public const LOW_STOCK_THRESHOLD = 10;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'category',
        'sub_category',
        'price',
        'sale_price',
        'stock',
        'sold_count',
        'description',
        'image',
        'gallery_images',
        'sni_number',
        'inlet_size',
        'sound_type',
        'material',
        'engine_cc',
    ];

    protected function casts(): array
    {
        return [
            'gallery_images' => 'array',
            'sold_count' => 'integer',
        ];
    }

    public function getEffectivePriceAttribute(): float
    {
        return $this->has_discount
            ? (float) $this->sale_price
            : (float) $this->price;
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'exhaust' => 'Knalpot Mobil',
            'tire' => 'Ban Motor',
            'oil' => 'Oli Motor',
            'sparepart' => 'Sparepart',
            'service' => 'Servis',
            default => 'Lainnya',
        };
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

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock > 0 && $this->stock <= self::LOW_STOCK_THRESHOLD;
    }
}
