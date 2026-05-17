<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'n_empleado',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'departamento_id',
        'puesto',
        'email',
        'foto',
        'activo'
    ];

     protected $casts = [
        'activo' => 'boolean'
    ];

    // Relación uno a muchos con asistencias
       public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function horarios()
    {
        return $this->hasMany(HorarioEmpleado::class);
    }

    public function checadas()
    {
        return $this->hasMany(Checada::class);
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    public function vacaciones()
    {
        return $this->hasMany(Vacacion::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getNombreCompletoAttribute()
    {
        return "{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno}";
    }
}
