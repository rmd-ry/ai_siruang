<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LantaiSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $dataLantai = [];
        for ($i = 2; $i <= 4; $i++) {
            $dataLantai[] = [
                'nama_lantai' => 'Lantai ' . $i,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('lantai')->insert($dataLantai);
    }
}
