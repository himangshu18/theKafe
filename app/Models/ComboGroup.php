<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ComboGroup extends Model
{
    protected $fillable = [
        'combo_id',
        'name',
        'min_select',
        'max_select',
        'included_quantity',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'min_select' => 'integer',
            'max_select' => 'integer',
            'included_quantity' => 'integer',
        ];
    }

    public function combo(): BelongsTo
    {
        return $this->belongsTo(Combo::class);
    }

    public function eligibleItems(): BelongsToMany
    {
        return $this->belongsToMany(MenuItem::class, 'combo_group_items')->withPivot('price_adjustment');
    }
}
