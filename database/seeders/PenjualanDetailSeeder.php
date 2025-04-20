<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PenjualanDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $detail = [];
        for ($i = 1; $i <= 10; $i++) { // 10 transaksi
            for ($j = 1; $j <= 3; $j++) { // 3 barang per transaksi
                $barang_id = rand(1, 15);
                $harga = DB::table('m_barang')->where('id', $barang_id)->value('harga');
                $qty = rand(1, 5);
                
                $detail[] = [
                    'penjualan_id' => $i,
                    'barang_id' => $barang_id,
                    'jumlah' => $qty,
                    'harga_satuan' => $harga,
                    'subtotal' => $harga * $qty
                ];
            }
        }

        DB::table('t_penjualan_detail')->insert($detail);
    }
}
