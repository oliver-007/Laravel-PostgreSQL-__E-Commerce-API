<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'name',
        'mail',
        'password',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
