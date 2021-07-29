<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Area extends Model
{

    static function ObtenerActivos(){
        $result = DB::table('areas')
            ->select('id','nombre')
            ->where('status',1)
            ->orderby('nombre','asc')
            ->get();
        return $result;
    }

static function ObtenerActivosAsistencia($intAreaId){

        $query = DB::table('areas')
            ->select('id','nombre')
            ->where('status',1)
            ->orderby('nombre','asc');

        if ($intAreaId != 0 && $intAreaId != 3)
        { $query->where('areas.id', '=', $intAreaId); }

        if ($intAreaId == 3 )
        { $query->whereIn('areas.id',[3,6]); }

    	$result = $query->get();

        return $result;
    }

}
