<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

// FILAMENT
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable
implements FilamentUser
{
    use HasApiTokens,
        HasFactory,
        Notifiable;

    /**
     * Mass Assignable
     */
    protected $fillable = [

        'name',

        'email',

        'password',

        'user_group',

    ];

    /**
     * Hidden
     */
    protected $hidden = [

        'password',

        'remember_token',

    ];

    /**
     * Casts
     */
    protected $casts = [

        'email_verified_at' => 'datetime',

    ];

    /**
     * AKSES FILAMENT
     * HANYA ADMIN
     */
     public function canAccessPanel(
        Panel $panel
    ): bool
    {
        return auth()->check()
            && $this->user_group === 'admin';
    }
}