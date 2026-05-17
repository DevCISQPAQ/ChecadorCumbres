<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checada extends Model
{
    protected $fillable = [
        'empleado_id',
        'fecha_hora',
        'tipo'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
