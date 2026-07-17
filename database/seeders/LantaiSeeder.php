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

        for ($i = 2; $i <= 4; $i++) {
            DB::table('lantai')->updateOrInsert(
                [
                    'nama_lantai' => 'Lantai ' . $i,
                ],
                [
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}