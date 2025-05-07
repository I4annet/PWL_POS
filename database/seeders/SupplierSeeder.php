<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('m_supplier')->insert([
            [
                'supplier_kode' => 'SP-001',
                'nama_supplier' => 'PT. Astra Honda Motor',
                'supplier_alamat' => 'Jl. Raya No. 1, Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supplier_kode' => 'SP-002',
                'nama_supplier' => 'PT. Mayora',
                'supplier_alamat' => 'Jl. Raya No. 2, Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'supplier_kode' => 'SP-003',
                'nama_supplier' => 'PT. Indofood',
                'supplier_alamat' => 'Jl. Raya No. 3, Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
