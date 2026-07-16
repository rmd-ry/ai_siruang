<?php

namespace App;

enum StatusReservasi: string
{
    case PENDING = 'pending';
    case DITERIMA = 'diterima';
    case DITOLAK = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu Konfirmasi',
            self::DITERIMA => 'Disetujui',
            self::DITOLAK => 'Maaf, Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::DITERIMA => 'success',
            self::DITOLAK => 'danger',
        };
    }
}
