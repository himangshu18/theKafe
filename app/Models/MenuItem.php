<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class MenuItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'menu_category_id',
        'name',
        'description',
        'price',
        'food_type',
        'preparation_minutes',
        'available_from',
        'available_until',
        'image_path',
        'is_available',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'available_from' => 'datetime',
            'available_until' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'menu_category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(MenuItemImage::class)->orderBy('sort_order');
    }

    public function variations(): HasMany
    {
        return $this->hasMany(MenuVariation::class)->orderBy('sort_order');
    }

    public function addonGroups(): BelongsToMany
    {
        return $this->belongsToMany(AddonGroup::class, 'menu_item_addon_group')->orderBy('sort_order');
    }

    public function availabilitySchedules(): HasMany
    {
        return $this->hasMany(MenuAvailabilitySchedule::class);
    }

    public function todaySpecials(): HasMany
    {
        return $this->hasMany(TodaySpecial::class);
    }

    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(Offer::class, 'offer_menu_item');
    }

    public function imageUrl(): ?string
    {
        $imagePath = $this->relationLoaded('images')
            ? $this->images->first()?->image_path ?? $this->image_path
            : $this->image_path;

        if (! $imagePath) {
            return null;
        }

        return Storage::disk('public')->url($imagePath);
    }

    public function isAvailableAt(?Carbon $dateTime = null): bool
    {
        $dateTime ??= now();

        if (! $this->is_available) {
            return false;
        }

        if ($this->available_from && $dateTime->lt($this->available_from)) {
            return false;
        }

        if ($this->available_until && $dateTime->gt($this->available_until)) {
            return false;
        }

        $schedules = $this->availabilitySchedules
            ->where('day_of_week', $dateTime->dayOfWeek)
            ->where('is_available', true);

        if ($this->availabilitySchedules->isEmpty()) {
            return true;
        }

        $currentTime = $dateTime->format('H:i:s');

        return $schedules->contains(
            fn (MenuAvailabilitySchedule $schedule): bool => $schedule->starts_at <= $currentTime
                && $schedule->ends_at >= $currentTime
        );
    }
}
