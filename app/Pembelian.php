<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'pembelians';

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function detail()
    {
        return $this->hasMany(DetailPembelian::class, 'pembelians_id', 'id');
    }
}
