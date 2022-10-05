<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'barangs';

    public function jenis()
    {
        return $this->belongsTo(Jenis::class);
    }

    public function d_penjualan()
    {
        return $this->hasMany(DetailPenjualan::class, 'barangs_id', 'id');
    }

    public function d_pembelian()
    {
        return $this->hasMany(DetailPembelian::class, 'barangs_id', 'id');
    }
}
