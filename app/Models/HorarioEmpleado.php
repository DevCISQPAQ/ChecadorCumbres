<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HorarioEmpleado extends Model
{
    protected $table = 'horarios_empleado';

    protected $fillable = [
        'empleado_id',
        'dia_semana',
        'hora_entrada',
        'hora_salida',
        'hora_salida_comida',
        'hora_regreso_comida',
        'tolerancia_minutos',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
}
