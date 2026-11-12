<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Combo extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'kind',
        'image_path',
        'is_active',
        'starts_at',
        'ends_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function groups(): HasMany
    {
        return $this->hasMany(ComboGroup::class)->orderBy('sort_order');
    }

    public function isAvailableAt(?Carbon $dateTime = null): bool
    {
        $dateTime ??= now();

        return $this->is_active
            && (! $this->starts_at || $dateTime->gte($this->starts_at))
            && (! $this->ends_at || $dateTime->lte($this->ends_at));
    }
}
