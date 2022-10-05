<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    use \App\Traits\TraitUuid;
    protected $table = 'detail_penjualans';

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualans_id', 'id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barangs_id', 'id');
    }
}
