<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('m_barang')->insert([
            [
            'barang_kode' => 'MKN-001',
            'barang_nama' => 'Indomie Goreng',
            'kategori_id' => 1,
            'supplier_id' => 3,
            'harga_beli' => 2500,
            'harga_jual' => 1000,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'barang_kode' => 'MIN-001',
            'barang_nama' => 'Susu Indomilk',
            'kategori_id' => 2,
            'supplier_id' => 3,
            'harga_beli' => 6000,
            'harga_jual' => 5000,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'barang_kode' => 'MKN-003',
            'barang_nama' => 'Indomie Rasa Soto',
            'kategori_id' => 1,
            'supplier_id' => 3,
            'harga_beli' => 2500,
            'harga_jual' => 1000,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'barang_kode' => 'MKN-004',
            'barang_nama' => 'Susu Kental Manis Indomilk',
            'kategori_id' => 2,
            'supplier_id' => 3,
            'harga_beli' => 6000,
            'harga_jual' => 5000,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'barang_kode' => 'MKN-005',
            'barang_nama' => 'Indomie Rasa Ayam Bawang',
            'kategori_id' => 1,
            'supplier_id' => 3,
            'harga_beli' => 2500,
            'harga_jual' => 1000,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'barang_kode' => 'KND-001',
            'barang_nama' => 'CBR 150R',
            'kategori_id' => 3,
            'supplier_id' => 1,
            'harga_beli' => 25000000,
            'harga_jual' => 10000000,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'barang_kode' => 'KND-002',
            'barang_nama' => 'CBR 250RR',
            'kategori_id' => 3,
            'supplier_id' => 1,
            'harga_beli' => 50000000,
            'harga_jual' => 40000000,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'barang_kode' => 'KND-003',
            'barang_nama' => 'CB 150R',
            'kategori_id' => 3,
            'supplier_id' => 1,
            'harga_beli' => 20000000,
            'harga_jual' => 15000000,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'barang_kode' => 'KND-004',
            'barang_nama' => 'PCX 160',
            'kategori_id' => 3,
            'supplier_id' => 1,
            'harga_beli' => 30000000,
            'harga_jual' => 10000000,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'barang_kode' => 'KND-005',
            'barang_nama' => 'Vario 150',
            'kategori_id' => 3,
            'supplier_id' => 1,
            'harga_beli' => 25000000,
            'harga_jual' => 20000000,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'barang_kode' => 'MKN-006',
            'barang_nama' => 'Kopiko',
            'kategori_id' => 2,
            'supplier_id' => 2,
            'harga_beli' => 2000,
            'harga_jual' => 100,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'barang_kode' => 'MIN-002',
            'barang_nama' => 'Teh Pucuk Harum',
            'kategori_id' => 2,
            'supplier_id' => 2,
            'harga_beli' => 3000,
            'harga_jual' => 100,
            'created_at' => now(),
            'updated_at' => now(),
            ],
        ]);
        }
    } 
