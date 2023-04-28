<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembayaran extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'detail_pembayarans';

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'services_id', 'id');
    }

    public function jenisjasa()
    {
        return $this->belongsTo(JenisJasa::class, 'jasa_barang_id', 'id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'jasa_barang_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
