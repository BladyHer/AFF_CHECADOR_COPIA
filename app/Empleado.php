<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = "empleados";
    protected $fillable = [
        'id', "nombre", "apaterno", "amaterno", "sexo", "punto_pago", "sueldo", "puestos", "status"
    ];
}
