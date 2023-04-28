<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'pembayarans';

    public function service()
    {
        return $this->belongsTo(Service::class, 'services_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function detail()
    {
        return $this->hasMany(DetailPembayaran::class, 'pembayarans_id', 'id');
    }
}
