<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Puesto extends Model
{
    static function ObtenerActivos(){
        $result = DB::table('puestos')
            ->select('id','puesto')
            ->where('status',1)
            ->orderby('puesto','asc')
            ->get();
        return $result;
    }
}
