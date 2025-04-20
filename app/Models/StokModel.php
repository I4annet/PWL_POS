<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokModel extends Model
{
    protected $table = 't_stok';
    protected $primaryKey = 'stok_id';

    protected $fillable = [
        'stok_id',
        'barang_id',
        'stok_tanggal',
        'stok_jumlah',
        'stok_keterangan'
    ];

    public function barang() {
        return $this->belongsTo(BarangModel::class, 'barang_id', 'barang_id');
    }
}
