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
                'barang_id' => 1,
                'stok_tanggal' => now(),
                'stok_jumlah' => 10,
                'stok_keterangan' => 'Tersedia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barang_id' => 2,
                'stok_tanggal' => now(),
                'stok_jumlah' => 20,
                'stok_keterangan' => 'Tersedia',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
