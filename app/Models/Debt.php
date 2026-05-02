<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{

    protected $fillable = [
        'sale_id',
        'customer_name',
        'customer_phone',
        'amount',
        'paid_amount',
        'remaining_amount',
        'paid_date',
        'notes',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'paid_date' => 'date',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function getTotalAmountAttribute()
    {
        return $this->amount;
    }

    public function getOutstandingAmountAttribute()
    {
        return $this->remaining_amount;
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    protected static function booted()
    {
        static::saving(function ($debt) {
            // Auto-calculate remaining amount
            $debt->remaining_amount = $debt->amount - $debt->paid_amount;

            // Auto-update status based on payment
            if ($debt->remaining_amount <= 0) {
                $debt->status = 'paid';
            } else {
                $debt->status = 'unpaid';
            }
        });
    }
}
