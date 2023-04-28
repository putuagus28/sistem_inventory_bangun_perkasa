<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'jurnals';

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akuns_id', 'id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
