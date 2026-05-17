<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiaFestivo extends Model
{
     protected $table = 'dias_festivos';

    protected $fillable = [
        'nombre',
        'fecha',
        'oficial'
    ];

    protected $casts = [
        'fecha' => 'date',
        'oficial' => 'boolean'
    ];
}
