<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'is_admin', 'admin_role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function adminNotifications(): HasMany
    {
        return $this->hasMany(AdminNotification::class)->latest();
    }

    public function hasAdminRole(string ...$roles): bool
    {
        return $this->is_admin && in_array($this->admin_role, $roles, true);
    }

    public function canManageMenu(): bool
    {
        return $this->hasAdminRole('owner', 'manager');
    }

    public function canManageOrders(): bool
    {
        return $this->hasAdminRole('owner', 'manager', 'cashier', 'kitchen');
    }

    public function canManageBookings(): bool
    {
        return $this->hasAdminRole('owner', 'manager', 'cashier');
    }

    public function canViewAccounting(): bool
    {
        return $this->hasAdminRole('owner', 'manager', 'accountant');
    }
}
