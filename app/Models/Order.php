<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'public_ref',
        'customer_name',
        'customer_phone',
        'customer_note',
        'total_amount',
        'discount_amount',
        'status',
        'source',
        'order_type',
        'payment_status',
        'idempotency_key',
        'received_at',
        'accepted_at',
        'estimated_ready_at',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'received_at' => 'datetime',
            'accepted_at' => 'datetime',
            'estimated_ready_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayment::class)->orderByDesc('paid_at');
    }
}
