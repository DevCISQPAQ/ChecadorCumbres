<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuraciones';
    protected $fillable = ['clave', 'valor'];
    public $timestamps = false;

    public static function getValor($clave, $default = null)
    {
        return optional(static::where('clave', $clave)->first())->valor ?? $default;
    }
}
