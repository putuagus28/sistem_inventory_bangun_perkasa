<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pelanggan extends Authenticatable
{
    use Notifiable;
    use \App\Traits\TraitUuid;
    protected $table = 'pelanggans';

    protected $fillable = [
        'nama', 'role', 'username', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function users()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
