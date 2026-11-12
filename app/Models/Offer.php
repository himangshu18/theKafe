<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Offer extends Model
{
    protected $fillable = [
        'name',
        'description',
        'discount_type',
        'discount_value',
        'is_active',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'offer_menu_item');
    }

    public function isAvailableAt(?Carbon $dateTime = null): bool
    {
        $dateTime ??= now();

        return $this->is_active
            && (! $this->starts_at || $dateTime->gte($this->starts_at))
            && (! $this->ends_at || $dateTime->lte($this->ends_at));
    }
}
