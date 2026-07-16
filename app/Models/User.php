<?php

namespace App\Models;

use App\UserRole;
use Filament\Models\Contracts\HasName;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Panel;

class User extends Authenticatable implements HasName, FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'nim',
        'username',
        'email',
        'password',
        'role',
        'agama',
        'jenis_kelamin',
        'Angkatan',
        'Kelas',
        'Status',
        'program_studi',
    ];

    protected $guarded = ['id'];
    protected $casts = [
        'password' => 'hashed',
        'role' => UserRole::class,
    ];
    public function getFilamentName(): string
    {
        return $this->nama ?? 'Tanpa Nama';
    }
    public function reservasi()
    {
        return $this->hasMany(Reservasi::class, 'id_user');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_user');
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === UserRole::ADMIN;
    }
}
