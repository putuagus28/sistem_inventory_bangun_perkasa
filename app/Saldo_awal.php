<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saldo_awal extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'saldo_awals';

    public function akuns()
    {
        return $this->belongsTo(Akun::class, 'akuns_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
