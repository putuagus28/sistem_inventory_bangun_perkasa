<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;
    use \App\Traits\TraitUuid;
    protected $table = 'penjualans';

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }

    public function customers()
    {
        return $this->belongsTo(Customer::class, 'customers_id', 'id');
    }

    public function detail()
    {
        return $this->hasMany(DetailPenjualan::class, 'penjualans_id', 'id');
    }
}
