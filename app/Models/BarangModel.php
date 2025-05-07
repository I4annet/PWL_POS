<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;   
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class BarangModel extends Model
{
    protected $table = 'm_barang';
    protected $primaryKey = 'barang_id';

    protected $fillable = [
        'barang_id',
        'barang_kode',
        'barang_nama',
        'harga_beli',
        'harga_jual',
        'kategori_id',
        'supplier_id',
        'image'
    ];
    public function kategori() {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'kategori_id');
    }

    public function supplier() {
        return $this->belongsTo(SupplierModel::class, 'supplier_id', 'supplier_id');
    }

    public function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => url('storage/posts/' . $image),
        );
    }
    public function getStok(): int
    {
        $stokMasuk = DB::table('t_stok')
            ->where('barang_id', $this->barang_id)
            ->sum('stok_jumlah');  // 21

        $stokKeluar = DB::table('t_penjualan_detail')
            ->where('barang_id', $this->barang_id)
            ->sum('jumlah'); // 4

        return $stokMasuk - $stokKeluar;
    }
    
}
