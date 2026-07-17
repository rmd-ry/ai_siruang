<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Lab Komputer
        DB::table('kelas')->updateOrInsert(
            ['nama_kelas' => 'Lab Komputer'],
            [
                'id_lantai' => 1,
                'kapasitas' => 39,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        // Ruang 301-318
        for ($i = 1; $i <= 18; $i++) {
            $nomorRuangan = '3' . str_pad($i, 2, '0', STR_PAD_LEFT);

            DB::table('kelas')->updateOrInsert(
                ['nama_kelas' => $nomorRuangan],
                [
                    'id_lantai' => 2,
                    'kapasitas' => 40,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // Ruang 401-408
        for ($i = 1; $i <= 8; $i++) {
            $nomorRuangan = '4' . str_pad($i, 2, '0', STR_PAD_LEFT);

            DB::table('kelas')->updateOrInsert(
                ['nama_kelas' => $nomorRuangan],
                [
                    'id_lantai' => 3,
                    'kapasitas' => 40,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // Aula
        DB::table('kelas')->updateOrInsert(
            ['nama_kelas' => 'Aula'],
            [
                'id_lantai' => 3,
                'kapasitas' => 30,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}