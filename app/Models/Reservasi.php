<?php

namespace App\Models;

use App\StatusReservasi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    use HasFactory;

    protected $table = 'reservasi';
    protected $guarded = ['id'];

    protected $casts = [
        'status' => StatusReservasi::class,
        'tanggal' => 'date',
        'read_at' => 'datetime',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
}
