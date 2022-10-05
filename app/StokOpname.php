<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokOpname extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'stok_opnames';

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barangs_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
