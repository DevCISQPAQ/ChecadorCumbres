<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $table = 'asistencias';



    protected $fillable = [
        'empleado_id',
        'fecha',
        'estado',
        'horas_trabajadas',
        'minutos_retardo',
        'observaciones'
    ];

    protected $casts = [
        'fecha' => 'date',
        'horas_trabajadas' => 'decimal:2'
    ];

    // Relación con empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function checadas()
    {
        return $this->hasMany(
            \App\Models\Checada::class,
            'empleado_id',
            'empleado_id'
        );
    }
}
