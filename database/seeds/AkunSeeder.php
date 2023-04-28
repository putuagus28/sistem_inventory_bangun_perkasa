<?php

use App\Pelanggan;
use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\DB;

class AkunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $role = array("admin", "teknisi", "owner");

        for ($i = 0; $i < count($role); $i++) {
            User::create([
                'name' => $role[$i],
                'username' => $role[$i],
                'password' => bcrypt($role[$i]),
                'role' => $role[$i],
                'email' => $role[$i] . '@gmail.com',
            ]);
        }

        Pelanggan::create([
            'kode' => 'PLG001',
            'nama' => 'pelanggan',
            'username' => 'pelanggan',
            'password' => bcrypt('pelanggan'),
            'alamat' => 'denpasar',
            'role' => 'pelanggan',
            'no_telp' => '081929189212',
        ]);
    }
}
