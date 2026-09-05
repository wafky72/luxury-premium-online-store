<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

#[Fillable(['name', 'email', 'phone', 'password', 'role', 'is_active', 'avatar', 'tenant_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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
        ];
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // null-safe: is_active defaults to true if column missing/null
        $isActive = $this->is_active ?? true;
        $canAccess = $this->role === 'admin' && (bool) $isActive;
        
        $debugText = date('Y-m-d H:i:s') . " - Login Attempt by {$this->email} | Role: {$this->role} | IsActive: {$isActive} | CanAccess: {$canAccess}\n";
        @file_put_contents(__DIR__ . '/../../../public/login_debug.txt', $debugText, FILE_APPEND);

        return $canAccess;
    }
}
