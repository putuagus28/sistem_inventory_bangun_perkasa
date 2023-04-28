<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'barangs';

    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
