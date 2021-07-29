<?php

namespace App;
use DB;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    //

    static function asistencia1($strFecha, $intAreaId){

        $result = DB::table('asistencias')
            ->select(
                DB::raw('id'),
                DB::raw('empleado_id as nombre'))
                ->get();
        return $result;
    }


    static function asistencia($strFecha, $intAreaId, $intPuestoId){
        $result = DB::table('asistencias')
            ->join('empleados', 'asistencias.empleado_id', '=', 'empleados.id')
            ->join('entradas', 'asistencias.entrada_id', '=', 'entradas.id')
            ->join('puestos', 'empleados.puesto', '=', 'puestos.id')
            ->select(
                DB::raw('empleados.id'),
                DB::raw('entradas.nombre'),
                DB::raw('puestos.puesto'),
                DB::raw("CONCAT(empleados.nombre,' ',empleados.apaterno,' ',empleados.amaterno) As nombres"),
                DB::raw('asistencias.hora'))
            ->where(DB::raw("date_format(asistencias.fecha , '%Y-%m-%d')"), '=',$strFecha)
            ->whereIn('asistencias.entrada_id',[1,2])
            ->orderBy('asistencias.hora')
            ->orderBy('empleados.nombre');


        if ($intAreaId != 0)
        { $result->where('asistencias.area_id', '=', $intAreaId); }

        if ($intPuestoId != 0)
        { $result->where('empleados.puesto', '=', $intPuestoId); }

        $query = $result->get();

        return $query;
    }

    static function asistenciaActividades($strFecha, $intAreaId){
        $result = DB::table('asistencias')
            ->join('empleados', 'asistencias.empleado_id', '=', 'empleados.id')
            ->join('entradas', 'asistencias.entrada_id', '=', 'entradas.id')
            ->join('puestos', 'empleados.puesto', '=', 'puestos.id')
            ->join('asistencias_actividades', DB::raw("date_format(asistencias_actividades.fecha , '%Y-%m-%d') = '".$strFecha."' and empleados.id "),"=","asistencias_actividades.empleado_id" )
            ->select(
                DB::raw('empleados.id'),
                DB::raw('entradas.nombre'),
                DB::raw('puestos.puesto'),
                DB::raw("CONCAT(empleados.nombre,' ',empleados.apaterno,' ',empleados.amaterno) As nombres"))
            ->where(DB::raw("date_format(asistencias.fecha , '%Y-%m-%d')"), '=', $strFecha)
            ->where(DB::raw("asistencias_actividades.actividad_id"), '=',0)
            ->whereIn('asistencias.entrada_id',[1])
            ->orderBy('asistencias.hora');


        if ($intAreaId != 0)
        { $result->where('asistencias.area_id', '=', $intAreaId); }

        $query = $result->get();

        return $query;
    }



    static function reporteEntradaSalidaPDF($strFecha, $intAreaId, $intPuestoId)
    {
        $result = DB::table('asistencias')
            ->join('empleados', 'asistencias.empleado_id', '=', 'empleados.id')
            ->join('entradas', 'asistencias.entrada_id', '=', 'entradas.id')
            ->join('puestos', 'empleados.puesto', '=', 'puestos.id')
            ->select(
                DB::raw('empleados.id'),
                DB::raw('entradas.nombre'),
                DB::raw('puestos.puesto'),
                DB::raw("CONCAT(empleados.nombre,' ',empleados.apaterno,' ',empleados.amaterno) As nombres"),
                DB::raw('asistencias.hora'))
            ->where(DB::raw("date_format(asistencias.fecha , '%Y-%m-%d')"), '=', $strFecha)
            ->whereIn('asistencias.entrada_id', [1, 2])
            ->orderBy('asistencias.hora');

        if ($intAreaId != 0) {
            $result->where('asistencias.area_id', '=', $intAreaId);
        }

        if ($intPuestoId != 0)
        { $result->where('empleados.puesto', '=', $intPuestoId); }

        $query = $result->get();

        return $query;

    }

    // Reporte Entrada Salida Completo
    static function asistenciaCom($strFecha, $intAreaId){

        $result = DB::select("SELECT
                        empleados.id,
                        CONCAT(empleados.apaterno, ' ', empleados.amaterno, ' ', empleados.nombre) as nombres,
                        puestos.puesto,
                        ifnull(horaInicio.hora,'') as horainicial,
                        ifnull(horaFinal.hora,'') as horafinal,
			if(ifnull(TIMEDIFF(horaFinal.hora,horaInicio.hora),'0') >0, ifnull(TIMEDIFF(horaFinal.hora,horaInicio.hora),'0'), 0) as horas
                        FROM
                        empleados inner join
                        areas on empleados.punto_pago = areas.id inner join
                        puestos on empleados.puesto = puestos.id  left outer join
                        (select empleados.id,
                                    hora
                            from asistencias inner join
                            empleados on  empleados.id = asistencias.empleado_id
                            where date_format(fecha, '%Y-%m-%d') = '".$strFecha."'
                                and entrada_id = '1'
                                and empleado_id = empleados.id
                                and asistencias.hora <> ''
                                and asistencias.area_id = '".$intAreaId."'
                                ) as horaInicio on empleados.id = horaInicio.id left outer join
                        (select empleados.id,
                                    hora
                            from asistencias inner join
                            empleados on  empleados.id = asistencias.empleado_id
                            where date_format(fecha, '%Y-%m-%d') = '".$strFecha."'
                                and entrada_id = '2'
                                and empleado_id = empleados.id
                                and asistencias.hora <> ''
                                and asistencias.area_id = '".$intAreaId."'
                                ) as horaFinal on empleados.id = horaFinal.id
                        where empleados.status = 1
                        and (horaInicio.hora <> '' or horaFinal.hora <> '')
                        order by nombres asc");

        return $result;
    }

    static function reporteEntradaSalidaComPDF($strFecha, $intAreaId)
    {
        $result = DB::select("SELECT
                        empleados.id,
                        CONCAT(empleados.apaterno, ' ', empleados.amaterno, ' ', empleados.nombre) as nombres,                        puestos.puesto,
                        ifnull(horaInicio.hora,'') as horainicial,
                        ifnull(horaFinal.hora,'') as horafinal,
			if(ifnull(TIMEDIFF(horaFinal.hora,horaInicio.hora),'0') >0, ifnull(TIMEDIFF(horaFinal.hora,horaInicio.hora),'0'), 0) as horas
                        FROM
                        empleados inner join
                        areas on empleados.punto_pago = areas.id inner join
                        puestos on empleados.puesto = puestos.id  left outer join
                        (select empleados.id,
                                    hora
                            from asistencias inner join
                            empleados on  empleados.id = asistencias.empleado_id
                            where date_format(fecha, '%Y-%m-%d') = '".$strFecha."'
                                and entrada_id = '1'
                                and empleado_id = empleados.id
                                and asistencias.hora <> ''
                                and asistencias.area_id = '".$intAreaId."'
                                ) as horaInicio on empleados.id = horaInicio.id left outer join
                        (select empleados.id,
                                    hora
                            from asistencias inner join
                            empleados on  empleados.id = asistencias.empleado_id
                            where date_format(fecha, '%Y-%m-%d') = '".$strFecha."'
                                and entrada_id = '2'
                                and empleado_id = empleados.id
                                and asistencias.hora <> ''
                                and asistencias.area_id = '".$intAreaId."'
                                ) as horaFinal on empleados.id = horaFinal.id
                        where empleados.status = 1
                        and (horaInicio.hora <> '' or horaFinal.hora <> '')
                        order by horaInicio.hora asc");

        return $result;

    }

    static function reporteTemporal($intAreaId, $strFechaSalida, $strFechaEntrada){

        $result = DB::select("SELECT
                        empleados.id,
                        CONCAT(empleados.nombre, ' ', empleados.apaterno, ' ', empleados.amaterno) as nombres,
                        puestos.puesto,
                        ifnull(horaInicio.hora,'') as horainicial,
                        ifnull(horaFinal.hora,'') as horafinal,
                        areas.nombre,
                        horaInicio.fecha
                        FROM
                        empleados inner join
                        areas on empleados.punto_pago = areas.id inner join
                        puestos on empleados.puesto = puestos.id  left outer join
                        (select empleados.id,
                                    hora,
                                    fecha
                            from asistencias inner join
                            empleados on  empleados.id = asistencias.empleado_id
                            where date_format(fecha, '%Y-%m-%d') >= '".$strFechaSalida."' and date_format(fecha, '%Y-%m-%d') <= '".$strFechaEntrada."'
                                and entrada_id = '17'
                                and empleado_id = empleados.id
                                and asistencias.hora <> ''
                                ) as horaInicio on empleados.id = horaInicio.id left outer join
                        (select empleados.id,
                                    hora
                            from asistencias inner join
                            empleados on  empleados.id = asistencias.empleado_id
                            where date_format(fecha, '%Y-%m-%d') >= '".$strFechaSalida."' and date_format(fecha, '%Y-%m-%d') <= '".$strFechaEntrada."'
                                and entrada_id = '3'
                                and empleado_id = empleados.id
                                and asistencias.hora <> ''
                                ) as horaFinal on empleados.id = horaFinal.id
                        where empleados.status = 1
                        and (horaInicio.hora <> '' or horaFinal.hora <> '')
                        and areas.id = '".$intAreaId."'
                        order by horaInicio.fecha asc");

        return $result;
    }
    //FIN DE ENTRADA SALIDA COMPLETO

    //REPORTE DE PERMISOS TEMPORALES

    static function reporteTemporalPDF($intAreaId, $strFechaSalida, $strFechaEntrada){

        $result = DB::select("SELECT
                        empleados.id,
                        CONCAT(empleados.nombre, ' ', empleados.apaterno, ' ', empleados.amaterno) as nombres,
                        puestos.puesto,
                        ifnull(horaInicio.hora,'') as horainicial,
                        ifnull(horaFinal.hora,'') as horafinal,
                        areas.nombre,
                        horaInicio.fecha
                        FROM
                        empleados inner join
                        areas on empleados.punto_pago = areas.id inner join
                        puestos on empleados.puesto = puestos.id  left outer join
                        (select empleados.id,
                                    hora,
                                    fecha
                            from asistencias inner join
                            empleados on  empleados.id = asistencias.empleado_id
                            where date_format(fecha, '%Y-%m-%d') >= '".$strFechaSalida."' and date_format(fecha, '%Y-%m-%d') <= '".$strFechaEntrada."'
                                and entrada_id = '17'
                                and empleado_id = empleados.id
                                and asistencias.hora <> ''
                                ) as horaInicio on empleados.id = horaInicio.id left outer join
                        (select empleados.id,
                                    hora
                            from asistencias inner join
                            empleados on  empleados.id = asistencias.empleado_id
                            where date_format(fecha, '%Y-%m-%d') >= '".$strFechaSalida."' and date_format(fecha, '%Y-%m-%d') <= '".$strFechaEntrada."'
                                and entrada_id = '3'
                                and empleado_id = empleados.id
                                and asistencias.hora <> ''
                                ) as horaFinal on empleados.id = horaFinal.id
                        where empleados.status = 1
                        and (horaInicio.hora <> '' or horaFinal.hora <> '')
                        and areas.id = '".$intAreaId."'
                        order by horaInicio.fecha asc");

        return $result;
    }

    //REPORTE DE PERMISOS ENTRADA


    static function asistenciaPermiso($strFecha, $intAreaId, $intPuestoId){
        $result = DB::table('asistencias')
            ->join('empleados', 'asistencias.empleado_id', '=', 'empleados.id')
            ->join('entradas', 'asistencias.entrada_id', '=', 'entradas.id')
            ->join('puestos', 'empleados.puesto', '=', 'puestos.id')
            ->select(
                DB::raw('empleados.id'),
                DB::raw('entradas.nombre'),
                DB::raw('puestos.puesto'),
                DB::raw("CONCAT(empleados.nombre,' ',empleados.apaterno,' ',empleados.amaterno) As nombres"),
                DB::raw('asistencias.hora'))
            ->where(DB::raw("date_format(asistencias.fecha , '%Y-%m-%d')"), '=',$strFecha)
            ->whereIn('asistencias.entrada_id',[4])
            ->orderBy('asistencias.hora')
            ->orderBy('empleados.nombre');


        if ($intAreaId != 0)
        { $result->where('asistencias.area_id', '=', $intAreaId); }

        if ($intPuestoId != 0)
        { $result->where('empleados.puesto', '=', $intPuestoId); }

        $query = $result->get();

        return $query;
    }

    static function asistenciaPermisoPDF($strFecha, $intAreaId, $intPuestoId)
    {
        $result = DB::table('asistencias')
            ->join('empleados', 'asistencias.empleado_id', '=', 'empleados.id')
            ->join('entradas', 'asistencias.entrada_id', '=', 'entradas.id')
            ->join('puestos', 'empleados.puesto', '=', 'puestos.id')
            ->select(
                DB::raw('empleados.id'),
                DB::raw('puestos.puesto'),
                DB::raw("CONCAT(empleados.nombre,' ',empleados.apaterno,' ',empleados.amaterno) As nombres"),
                DB::raw('asistencias.hora'))
            ->where(DB::raw("date_format(asistencias.fecha , '%Y-%m-%d')"), '=', $strFecha)
            ->whereIn('asistencias.entrada_id', [4])
            ->orderBy('asistencias.hora');

        if ($intAreaId != 0) {
            $result->where('asistencias.area_id', '=', $intAreaId);
        }

        if ($intPuestoId != 0)
        { $result->where('empleados.puesto', '=', $intPuestoId); }

        $query = $result->get();

        return $query;

    }

    // Reporte Entrada Comision de Trabajo
    static function asistenciaComision($strFecha, $intAreaId){

        $result = DB::select("SELECT
                        empleados.id,
                        CONCAT(empleados.apaterno, ' ', empleados.amaterno, ' ', empleados.nombre) as nombres,
                        puestos.puesto,
                        ifnull(horaInicio.hora,'') as horainicial,
                        ifnull(horaFinal.hora,'') as horafinal,
			            if(ifnull(TIMEDIFF(horaFinal.hora,horaInicio.hora),'0') >0, ifnull(TIMEDIFF(horaFinal.hora,horaInicio.hora),'0'), 0) as horas
                        FROM
                        empleados inner join
                        areas on empleados.punto_pago = areas.id inner join
                        puestos on empleados.puesto = puestos.id  left outer join
                        (select empleados.id,
                                    hora
                            from asistencias inner join
                            empleados on  empleados.id = asistencias.empleado_id
                            where date_format(fecha, '%Y-%m-%d') = '".$strFecha."'
                                and entrada_id = '18'
                                and empleado_id = empleados.id
                                and asistencias.hora <> ''
                                and asistencias.area_id = '".$intAreaId."'
                                ) as horaInicio on empleados.id = horaInicio.id left outer join
                        (select empleados.id,
                                    hora
                            from asistencias inner join
                            empleados on  empleados.id = asistencias.empleado_id
                            where date_format(fecha, '%Y-%m-%d') = '".$strFecha."'
                                and entrada_id = '5'
                                and empleado_id = empleados.id
                                and asistencias.hora <> ''
                                and asistencias.area_id = '".$intAreaId."'
                                ) as horaFinal on empleados.id = horaFinal.id
                        where empleados.status = 1
                        and (horaInicio.hora <> '' or horaFinal.hora <> '')
                        order by nombres asc");

        return $result;
    }

    static function reportePersonalComisionPDF($strFecha, $intAreaId)
    {
        $result = DB::select("SELECT
                        empleados.id,
                        CONCAT(empleados.apaterno, ' ', empleados.amaterno, ' ', empleados.nombre) as nombres,
                        puestos.puesto,
                        ifnull(horaInicio.hora,'') as horainicial,
                        ifnull(horaFinal.hora,'') as horafinal,
			if(ifnull(TIMEDIFF(horaFinal.hora,horaInicio.hora),'0') >0, ifnull(TIMEDIFF(horaFinal.hora,horaInicio.hora),'0'), 0) as horas
                        FROM
                        empleados inner join
                        areas on empleados.punto_pago = areas.id inner join
                        puestos on empleados.puesto = puestos.id  left outer join
                        (select empleados.id,
                                    hora
                            from asistencias inner join
                            empleados on  empleados.id = asistencias.empleado_id
                            where date_format(fecha, '%Y-%m-%d') = '".$strFecha."'
                                and entrada_id = '18'
                                and empleado_id = empleados.id
                                and asistencias.hora <> ''
                                and asistencias.area_id = '".$intAreaId."'
                                ) as horaInicio on empleados.id = horaInicio.id left outer join
                        (select empleados.id,
                                    hora
                            from asistencias inner join
                            empleados on  empleados.id = asistencias.empleado_id
                            where date_format(fecha, '%Y-%m-%d') = '".$strFecha."'
                                and entrada_id = '5'
                                and empleado_id = empleados.id
                                and asistencias.hora <> ''
                                and asistencias.area_id = '".$intAreaId."'
                                ) as horaFinal on empleados.id = horaFinal.id
                        where empleados.status = 1
                        and (horaInicio.hora <> '' or horaFinal.hora <> '')
                        order by horaInicio.hora asc");

        return $result;
    }

    //FIN de Reporte Entrada Comision de Trabajo

    // Reporte Entrada Salida Especial

    static function asistenciaSalidaEspecial($strFecha, $intAreaId){

        $result = DB::select("SELECT
                        empleados.id,
                        CONCAT(empleados.apaterno, ' ', empleados.amaterno, ' ', empleados.nombre) as nombres,
                        puestos.puesto,
                        ifnull(horaInicio.hora,'') as horainicial,
                        ifnull(horaFinal.hora,'') as horafinal,
			            if(ifnull(TIMEDIFF(horaFinal.hora,horaInicio.hora),'0') >0, ifnull(TIMEDIFF(horaFinal.hora,horaInicio.hora),'0'), 0) as horas
                        FROM
                        empleados inner join
                        areas on empleados.punto_pago = areas.id inner join
                        puestos on empleados.puesto = puestos.id  left outer join
                        (select empleados.id,
                                    hora
                            from asistencias inner join
                            empleados on  empleados.id = asistencias.empleado_id
                            where date_format(fecha, '%Y-%m-%d') = '".$strFecha."'
                                and entrada_id = '1'
                                and empleado_id = empleados.id
                                and asistencias.hora <> ''
                                and asistencias.area_id = '".$intAreaId."'
                                ) as horaInicio on empleados.id = horaInicio.id left outer join
                        (select empleados.id,
                                    hora
                            from asistencias inner join
                            empleados on  empleados.id = asistencias.empleado_id
                            where date_format(fecha, '%Y-%m-%d') = '".$strFecha."'
                                and entrada_id = '6'
                                and empleado_id = empleados.id
                                and asistencias.hora <> ''
                                and asistencias.area_id = '".$intAreaId."'
                                ) as horaFinal on empleados.id = horaFinal.id
                        where empleados.status = 1
                        and (horaInicio.hora <> '' or horaFinal.hora <> '')
                        order by nombres asc");

        return $result;
    }

    static function reportePersonalSalidaEspecialPDF($strFecha, $intAreaId)
    {
        $result = DB::select("SELECT
                        empleados.id,
                        CONCAT(empleados.apaterno, ' ', empleados.amaterno, ' ', empleados.nombre) as nombres,
                        puestos.puesto,
                        ifnull(horaInicio.hora,'') as horainicial,
                        ifnull(horaFinal.hora,'') as horafinal,
			if(ifnull(TIMEDIFF(horaFinal.hora,horaInicio.hora),'0') >0, ifnull(TIMEDIFF(horaFinal.hora,horaInicio.hora),'0'), 0) as horas
                        FROM
                        empleados inner join
                        areas on empleados.punto_pago = areas.id inner join
                        puestos on empleados.puesto = puestos.id  left outer join
                        (select empleados.id,
                                    hora
                            from asistencias inner join
                            empleados on  empleados.id = asistencias.empleado_id
                            where date_format(fecha, '%Y-%m-%d') = '".$strFecha."'
                                and entrada_id = '1'
                                and empleado_id = empleados.id
                                and asistencias.hora <> ''
                                and asistencias.area_id = '".$intAreaId."'
                                ) as horaInicio on empleados.id = horaInicio.id left outer join
                        (select empleados.id,
                                    hora
                            from asistencias inner join
                            empleados on  empleados.id = asistencias.empleado_id
                            where date_format(fecha, '%Y-%m-%d') = '".$strFecha."'
                                and entrada_id = '6'
                                and empleado_id = empleados.id
                                and asistencias.hora <> ''
                                and asistencias.area_id = '".$intAreaId."'
                                ) as horaFinal on empleados.id = horaFinal.id
                        where empleados.status = 1
                        and (horaInicio.hora <> '' or horaFinal.hora <> '')
                        order by horaInicio.hora asc");

        return $result;
    }

    //REPORTE DE INASISTENCIA
    static function inasistencia($strFecha, $intAreaId){
        $result = DB::table('asistencias')
            ->join('empleados', 'asistencias.empleado_id', '=', 'empleados.id')
            ->join('entradas', 'asistencias.entrada_id', '=', 'entradas.id')
            ->join('puestos', 'empleados.puesto', '=', 'puestos.id')
            ->select(
                DB::raw('empleados.id'),
                DB::raw('entradas.nombre'),
                DB::raw('puestos.puesto'),
                DB::raw("CONCAT(empleados.nombre,' ',empleados.apaterno,' ',empleados.amaterno) As nombres"),
                DB::raw('asistencias.hora'))
            ->where(DB::raw("date_format(asistencias.fecha , '%Y-%m-%d')"), '=',$strFecha)
            ->whereIn('asistencias.entrada_id',[1,2])
            ->orderBy('asistencias.hora')
            ->orderBy('empleados.nombre');


        if ($intAreaId != 0)
        { $result->where('asistencias.area_id', '=', $intAreaId); }


        $query = $result->get();

        return $query;
    }

    static function reporteInasistenciaPDF($strFecha, $intAreaId)
    {
        $result = DB::table('asistencias')
            ->join('empleados', 'asistencias.empleado_id', '=', 'empleados.id')
            ->join('entradas', 'asistencias.entrada_id', '=', 'entradas.id')
            ->join('puestos', 'empleados.puesto', '=', 'puestos.id')
            ->select(
                DB::raw('empleados.id'),
                DB::raw('entradas.nombre'),
                DB::raw('puestos.puesto'),
                DB::raw("CONCAT(empleados.nombre,' ',empleados.apaterno,' ',empleados.amaterno) As nombres"),
                DB::raw('asistencias.hora'))
            ->where(DB::raw("date_format(asistencias.fecha , '%Y-%m-%d')"), '=', $strFecha)
            ->whereIn('asistencias.entrada_id', [1, 2])
            ->orderBy('asistencias.hora');

        if ($intAreaId != 0) {
            $result->where('asistencias.area_id', '=', $intAreaId);
        }

        $query = $result->get();

        return $query;

    }

    //FIN DE REPORTE DE INASISTENCIA

    //REPORTE DE REGISTRO PERSONAL

    static function asistenciaRegistro($strFecha,$strFecha2, $intEmpleadoID){
        $result = DB::table('asistencias')
            ->join('empleados', 'asistencias.empleado_id', '=', 'empleados.id')
            ->join('entradas', 'asistencias.entrada_id', '=', 'entradas.id')
            ->join('puestos', 'empleados.puesto', '=', 'puestos.id')
            ->select(
                DB::raw('empleados.id'),
                DB::raw('entradas.nombre'),
                DB::raw('puestos.puesto'),
                DB::raw("CONCAT(empleados.nombre,' ',empleados.apaterno,' ',empleados.amaterno) As nombres"),
                DB::raw('asistencias.fecha'),
                DB::raw('asistencias.hora'))
            ->whereBetween(DB::raw("date_format(asistencias.fecha , '%Y-%m-%d')"),[$strFecha,$strFecha2] )
            ->orderBy('asistencias.fecha')
            ->orderBy('asistencias.hora')
            ->orderBy('empleados.nombre');


        if ($intEmpleadoID != 0)
        { $result->where('asistencias.empleado_id', '=', $intEmpleadoID); }


        $query = $result->get();

        return $query;
    }

    static function reportePersonalRegistroPDF($strFecha, $strFecha2, $intEmpleadoID)
    {
        $result = DB::table('asistencias')
            ->join('empleados', 'asistencias.empleado_id', '=', 'empleados.id')
            ->join('entradas', 'asistencias.entrada_id', '=', 'entradas.id')
            ->join('puestos', 'empleados.puesto', '=', 'puestos.id')
            ->select(
                DB::raw('empleados.id'),
                DB::raw('entradas.nombre'),
                DB::raw('puestos.puesto'),
                DB::raw("CONCAT(empleados.nombre,' ',empleados.apaterno,' ',empleados.amaterno) As nombres"),
                DB::raw('asistencias.fecha'),
                DB::raw('asistencias.hora'))
                ->whereBetween(DB::raw("date_format(asistencias.fecha , '%Y-%m-%d')"),[$strFecha,$strFecha2] )
            ->orderBy('asistencias.hora');

        if ($intEmpleadoID != 0) {
            $result->where('asistencias.empleado_id', '=', $intEmpleadoID);
        }

        $query = $result->get();

        return $query;

    }

    //FIN DE REPORTE DE REGISTRO DE PERSONAL

    //REPORTE DE REGISTRO PERSONAL RETARDOS

    static function asistenciaRetardos($strFecha, $intAreaId,$intPuestoId ){
        $result = DB::table('asistencias')
            ->join('empleados', 'asistencias.empleado_id', '=', 'empleados.id')
            ->join('entradas', 'asistencias.entrada_id', '=', 'entradas.id')
            ->join('puestos', 'empleados.puesto', '=', 'puestos.id')
            ->select(
                DB::raw('empleados.id'),
                DB::raw('entradas.nombre'),
                DB::raw('puestos.puesto'),
                DB::raw("CONCAT(empleados.nombre,' ',empleados.apaterno,' ',empleados.amaterno) As nombres"),
                DB::raw('asistencias.hora'))
            ->where(DB::raw("date_format(asistencias.fecha , '%Y-%m-%d')"), '=',$strFecha)
            ->whereRaw("asistencias.hora >= TIME('08:10:01')")
            ->whereIn('asistencias.entrada_id', [1])
            ->orderBy('asistencias.hora')
            ->orderBy('empleados.nombre');


        if ($intAreaId != 0)
        { $result->where('asistencias.area_id', '=', $intAreaId); }

        if ($intPuestoId != 0)
        { $result->where('empleados.puesto', '=', $intPuestoId); }


        $query = $result->get();

        return $query;
    }

    static function reportePersonalRetardoPDF($strFecha, $intAreaId,$intPuestoId )
    {
        $result = DB::table('asistencias')
            ->join('empleados', 'asistencias.empleado_id', '=', 'empleados.id')
            ->join('entradas', 'asistencias.entrada_id', '=', 'entradas.id')
            ->join('puestos', 'empleados.puesto', '=', 'puestos.id')
            ->select(
                DB::raw('empleados.id'),
                DB::raw('entradas.nombre'),
                DB::raw('puestos.puesto'),
                DB::raw("CONCAT(empleados.nombre,' ',empleados.apaterno,' ',empleados.amaterno) As nombres"),
                DB::raw('asistencias.hora'))
            ->where(DB::raw("date_format(asistencias.fecha , '%Y-%m-%d')"), '=', $strFecha)
            ->whereIn('asistencias.entrada_id', [1])
            ->where(DB::raw("asistencias.hora >= time('08:10:01')"))
            ->orderBy('asistencias.hora');

        if ($intAreaId != 0)
        { $result->where('asistencias.area_id', '=', $intAreaId); }

        if ($intPuestoId != 0)
        { $result->where('empleados.puesto', '=', $intPuestoId); }

        $query = $result->get();

        return $query;

    }

    //FIN DE REPORTE DE REGISTRO PERSONAL RETARDOS

    //REPORTE ESPECIAL

    static function asistenciaRegistroEspecial($strFecha){
        $result = DB::table('asistencias')
            ->join('empleados', 'asistencias.empleado_id', '=', 'empleados.id')
            ->join('entradas', 'asistencias.entrada_id', '=', 'entradas.id')
            ->join('puestos', 'empleados.puesto', '=', 'puestos.id')
            ->select(
                DB::raw('empleados.id'),
                DB::raw('entradas.nombre'),
                DB::raw('puestos.puesto'),
                DB::raw("CONCAT(empleados.nombre,' ',empleados.apaterno,' ',empleados.amaterno) As nombres"),
                DB::raw('asistencias.hora'))
            ->where(DB::raw("date_format(asistencias.fecha , '%Y-%m-%d')"), '=',$strFecha)
            ->whereIn('asistencias.empleado_id',[30,40, 45, 46, 85])
            ->orderBy('empleados.nombre')
            ->orderBy('asistencias.hora');

        $query = $result->get();

        return $query;
    }

    //FIN REPORTE ESPECIAL

}
