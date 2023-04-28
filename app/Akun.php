<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akun extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'akuns';

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
