<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Asistencia extends Model
{
    static function ObtenerInformacion($intEmpleadoId){
        $result = DB::table('empleados')
            ->select(DB::raw('id, puesto, actividad_id, concat(nombre," ", apaterno," ", amaterno) as nombre'))
            ->where('id','=', $intEmpleadoId)
            ->orderby('id','asc')
            ->get();
        return $result;
    }


    static function ObtenerAsistencia($intEmpleadoId){
        $result = DB::table('asistencias')
            ->select(DB::raw('id'))
            ->where('empleado_id','=', $intEmpleadoId)
            ->whereRaw('fecha = CURDATE()')
            ->where('entrada_id','=', '1')
            ->orderby('id','asc')
            ->get();
            return $result;
    }

    static function guardarAsistencia($asistencia, $actividad){
        $id = 0;
        $result = [];
        if ($asistencia['entrada_id'] == 1) {
            $result = self::ObtenerAsistencia($asistencia['empleado_id']);
        };

        if (count($result) == 0){
            $id = DB::table('asistencias')
                ->insertGetId($asistencia);

            if ($asistencia['entrada_id'] == "1") {
                $id2 = DB::table('asistencias_actividades')
                    ->insertGetId($actividad);
            }
        }
        else
        {
            $id = 4;
        };
        return $id;
    }

    static function descargaAsistencia($fecha){
        $result = DB::select("select empleados.id,
                               empleados.nombre,
                               empleados.apaterno,
                               empleados.amaterno,
                               asistencias.hora as horaEntrada,
                               areas.nombre,
                               puestos.id as puesto_id,
                               puestos.puesto,
                               actividades.id as actividades_id,
                               actividades.nombre as actividad,
                               asistencias_actividades.lote_id
                        from empleados
                        inner join asistencias on empleados.id = asistencias.empleado_id and asistencias.entrada_id = 1 and asistencias.fecha = '".$fecha."'
                        inner join areas on asistencias.area_id = areas.id
                        inner join puestos on empleados.puesto = puestos.id
                        inner join actividades on empleados.actividad_id = actividades.id
                        inner join asistencias_actividades on empleados.id = asistencias_actividades.empleado_id and asistencias_actividades.fecha = '".$fecha."' ");
        return $result;
    }

    static function APIdescargaActividad(){
        $result = DB::table('actividades')
            ->select(DB::raw('id, nombre, status'))
            ->orderby('id','asc')
            ->get();
        return $result;
    }

    static function APIdescargaLotes(){
        $result = DB::table('lotes')
            ->select(DB::raw('id, lote, status'))
            ->orderby('id','asc')
            ->get();
        return $result;
    }
}
