<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $fillable = ['id','name','email','is_active',];

    public function users()
    {
        return $this->hasMany(Users::class);
    }
}