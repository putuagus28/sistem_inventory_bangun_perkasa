<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    use \App\Traits\TraitUuid;
    protected $table = 'detail_pembelians';

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelians_id', 'id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barangs_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'suppliers_id', 'id');
    }
}
