<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{

    // La clave primaria personalizada (número de empleado)
    protected $primaryKey = 'id';
    // La clave primaria NO es autoincremental
    public $incrementing = false;
    // El tipo de la clave primaria (int, porque usas unsignedBigInteger)
    protected $keyType = 'int';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'id',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'departamento',
        'puesto',
        'email',
        'tipo_horario',
        'foto',


    ];
    // Relación uno a muchos con asistencias
    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'empleado_id', 'id');
    }
}
