<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';
    protected $guarded = ['id'];

    public function lantai()
    {
        return $this->belongsTo(Lantai::class, 'id_lantai');
    }

    public function reservasi()
    {
        return $this->hasMany(Reservasi::class, 'id_kelas');
    }
}
