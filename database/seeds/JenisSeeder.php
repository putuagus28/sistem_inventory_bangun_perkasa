<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Jenis;
use Illuminate\Support\Facades\DB;

class JenisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Jenis::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        for ($i = 0; $i < 4; $i++) {
            Jenis::create([
                'nama_jenis' => 'jenis' . ($i + 1),
            ]);
        }
    }
}
