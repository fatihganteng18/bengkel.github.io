<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'method',
        'amount',
        'status',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->method) {
            'outlet_cash' => 'Bayar di outlet · Cash',
            'outlet_qris' => 'Bayar di outlet · QRIS',
            'bank_bca' => 'Transfer bank · BCA',
            'bank_mandiri' => 'Transfer bank · Mandiri',
            'ewallet_gopay' => 'E-Wallet · GoPay',
            'ewallet_ovo' => 'E-Wallet · OVO',
            'ewallet_dana' => 'E-Wallet · DANA',
            default => 'Metode pembayaran lainnya',
        };
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
