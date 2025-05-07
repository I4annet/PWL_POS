<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('t_penjualan')->insert([
            [
                'user_id' => 5,
                'pembeli' => 'Agus',
                'penjualan_kode'=> 'PJ-001',
                'penjualan_tanggal' => now(),
            ],
        ]);
    }
    
}
