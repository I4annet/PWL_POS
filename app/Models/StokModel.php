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
        'supplier_id',
        'barang_id',
        'stok_tanggal',
        'stok_jumlah',
        'user_id'
    ];

    public function barang() {
        return $this->belongsTo(BarangModel::class, 'barang_id', 'barang_id');
    }

    public function supplier() {
        return $this->belongsTo(SupplierModel::class, 'supplier_id', 'supplier_id');
    }

    public function user() {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}
