<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'services';


    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggans_id', 'id');
    }

    public function teknisi()
    {
        return $this->belongsTo(User::class, 'teknisi_id', 'id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'services_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
