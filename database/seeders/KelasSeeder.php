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
        $dataKelas = [];

        $dataKelas[] = [
            'id_lantai' => 1,
            'nama_kelas' => 'Lab Komputer',
            'kapasitas' => 39,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        for ($i = 1; $i <= 18; $i++) {
            $nomorRuangan = '3' . str_pad($i, 2, '0', STR_PAD_LEFT);

            $dataKelas[] = [
                'id_lantai' => 2,
                'nama_kelas' => $nomorRuangan,
                'kapasitas' => 40,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        for ($i = 1; $i <= 8; $i++) {
            $nomorRuangan = '4' . str_pad($i, 2, '0', STR_PAD_LEFT);

            $dataKelas[] = [
                'id_lantai' => 3,
                'nama_kelas' => $nomorRuangan,
                'kapasitas' => 40,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $dataKelas[] = [
            'id_lantai' => 3,
            'nama_kelas' => 'Aula',
            'kapasitas' => 30,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        DB::table('kelas')->insert($dataKelas);
    }
}
