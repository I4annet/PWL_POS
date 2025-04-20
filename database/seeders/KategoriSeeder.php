<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    
        DB::table('m_kategori')->insert([
            [
                'kategori_kode' => 'MKN',
                'kategori_nama' => 'Makanan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_kode' => 'MIN',
                'kategori_nama' => 'Minuman',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_kode' => 'KND',
                'kategori_nama' => 'Kendaraan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_kode' => 'ELK',
                'kategori_nama' => 'Elektronik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kategori_kode' => 'PKN',
                'kategori_nama' => 'Pakaian',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

    }
}
