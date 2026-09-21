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
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'status',
    'no_hp',
    'image',
    'last_read_verifikasi',
    'last_read_keluhan_admin',
    'last_read_tagihan_penghuni',
    'last_read_keluhan_penghuni',
    'last_read_pengumuman_penghuni',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory,Notifiable;

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
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function Penghunian(): HasMany
    {
        return $this->hasMany(Penghunian::class);
    }

    public function Pengumuman(): HasMany
    {
        return $this->hasMany(Pengumuman::class, 'admin_id');
    }

    public function Keluhan(): HasMany
    {
        return $this->hasMany(Keluhan::class, 'user_id');
    }

    public function Notification(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
