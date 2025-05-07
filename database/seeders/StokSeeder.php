<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('t_stok')->insert([
            [
                'supplier_id' => 1,
                'barang_id' => 1,
                'user_id' => 2,
                'stok_tanggal' => now(),
                'stok_jumlah' => 10,
            ],
            [
                'supplier_id' => 1,
                'barang_id' => 2,
                'user_id' => 2,
                'stok_tanggal' => now(),
                'stok_jumlah' => 20,
            ],
        ]);
    }
}
