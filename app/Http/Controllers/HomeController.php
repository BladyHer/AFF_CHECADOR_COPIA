<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        //DB::enableQueryLog();

        $area = DB::table('asistencias')
            ->join('empleados', 'asistencias.empleado_id', '=', 'empleados.id')
            ->join('areas', 'empleados.punto_pago', '=', 'areas.id')
            ->select(
                DB::raw('areas.nombre'),
                DB::raw('count(asistencias.id) as asistencia'))
            ->where('asistencias.entrada_id', '=', '1')
            ->where('asistencias.fecha', DB::raw('SUBSTRING(NOW(),1,10)'))
            ->groupBy('areas.nombre')
            ->get();

        $tmpContador = DB::table('asistencias')
            ->join('empleados', 'asistencias.empleado_id', '=', 'empleados.id')
            ->select(
                DB::raw('count(asistencias.id) as contador'))
            ->where('asistencias.entrada_id',  '3')
            ->where(DB::raw('SUBSTRING(NOW(),1,7)'), DB::raw('SUBSTRING(asistencias.fecha,1,7)'))
            ->get();

        $tmpPermiso = DB::table('asistencias')
            ->join('empleados', 'asistencias.empleado_id', '=', 'empleados.id')
            ->select(
                DB::raw('count(asistencias.id) as contador'))
            ->where('asistencias.entrada_id',  '4')
            ->where(DB::raw('SUBSTRING(NOW(),1,7)'), DB::raw('SUBSTRING(asistencias.fecha,1,7)'))
            ->get();

        $contador = DB::table('asistencias')
            ->join('empleados', 'asistencias.empleado_id', '=', 'empleados.id')
            ->select(
                DB::raw('count(asistencias.id) as contador'))
            ->where('asistencias.entrada_id', '1')
            ->where('asistencias.fecha', DB::raw('SUBSTRING(NOW(),1,10)'))
            ->get();

        //dd(DB::getQueryLog());

        return view('home', ['area' => $area,
                                   'tmpContador' => $tmpContador,
                                   'tmpPermiso' => $tmpPermiso,
                                   'contador' => $contador]);
    }
}
