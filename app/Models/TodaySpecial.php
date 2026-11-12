<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class TodaySpecial extends Model
{
    protected $fillable = [
        'menu_item_id',
        'title',
        'special_price',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'special_price' => 'decimal:2',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function isAvailableAt(?Carbon $dateTime = null): bool
    {
        $dateTime ??= now();

        return $this->is_active
            && (! $this->starts_at || $dateTime->gte($this->starts_at))
            && (! $this->ends_at || $dateTime->lte($this->ends_at));
    }
}
