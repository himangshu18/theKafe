<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'menu_item_id',
        'item_name',
        'menu_variation_id',
        'combo_id',
        'offer_id',
        'variation_name',
        'customizations',
        'combo_name',
        'combo_selections',
        'quantity',
        'unit_price',
        'line_total',
        'discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'customizations' => 'array',
            'combo_selections' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(MenuVariation::class, 'menu_variation_id');
    }

    public function combo(): BelongsTo
    {
        return $this->belongsTo(Combo::class);
    }

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }
}
