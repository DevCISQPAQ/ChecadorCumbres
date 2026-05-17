<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacacion extends Model
{
    protected $table = 'vacaciones';

     protected $fillable = [
        'empleado_id',
        'fecha_inicio',
        'fecha_fin',
        'motivo'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
