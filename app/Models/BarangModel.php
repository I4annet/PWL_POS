<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangModel extends Model
{
    public function kategori() {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'kategori_id');
    }
}
