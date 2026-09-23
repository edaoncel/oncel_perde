<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'name',           
        'phone',         
        'gender',
        'height',
        'weight',
        'manken_kilo',
        'clothing_type',
        'message',
        'chest',
        'waist',
        'hip',
        'ekstra_olculer',
        'cizim_katmani',
        'referans_resimler',
        'kisisel_bilgiler',
        'status'
    ];

    protected $casts = [
        'referans_resimler' => 'array',
    ];
}