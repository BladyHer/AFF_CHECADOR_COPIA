<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Entrada extends Model
{
    static function ObtenerActivos(){
        $result = DB::table('entradas')
            ->select('id','nombre')
            ->where('status',1)
            ->orderby('nombre','asc')
            ->get();
        return $result;
    }
}
