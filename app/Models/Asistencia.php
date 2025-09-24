<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    protected $table = 'asistencias';

    

    protected $fillable = [
        'empleado_id',
        'hora_entrada',
        'hora_salida',
        'retardo'
    ];

     protected $casts = [
        'hora_entrada' => 'datetime',
        'hora_salida' => 'datetime',
    ];

    // Relación con empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id', 'id');
    }
}
