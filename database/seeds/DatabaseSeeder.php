<?php

namespace Database\Seeders;

use AkunSeeder;
use Database\Seeders\JenisSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AkunSeeder::class);
        $this->call(JenisSeeder::class);
    }
}
