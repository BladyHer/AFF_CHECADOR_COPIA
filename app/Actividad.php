<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Actividad extends Model
{
    protected $table = 'actividades';

    static function ObtenerActivos(){
        $result = DB::table('actividades')
            ->select('id','nombre')
            ->where('status',1)
            ->orderby('nombre','asc')
            ->get();
        return $result;
    }
}
