<?php

namespace App\Http\Controllers;

use App\Asistencia;
use App\Area;
use App\Puesto;
use App\Entrada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class AsistenciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $area = $user->area_id;
        $areas = Area::ObtenerActivosAsistencia($area);
        $entradas = Entrada::obtenerActivos();
        return view('contenido.asistencia',['areas' => $areas,
                                                  'entradas' => $entradas]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data['strMensaje'] = '';
        $data['intResultado'] = 0;
        $data['intEmpleadoId'] = [];
        $data['strNombre'] = '';

        $empleado = Asistencia::ObtenerInformacion($request->intEmpleadoId);
        if(count($empleado) > 0){
            $time = getdate();
            $data['strNombre'] = $empleado[0]->nombre;
            $asistencia['empleado_id'] = $request->intEmpleadoId;
            $asistencia['area_id'] = $request->intAreaId;
            $asistencia['entrada_id'] = $request->intEntradaId;
            $asistencia['fecha'] = date('Y-m-d');
            $asistencia['hora'] = $time['hours'].":".$time["minutes"].":".$time["seconds"];

            $actividad['empleado_id'] = $request->intEmpleadoId;
            $actividad['actividad_id'] = $empleado[0]->actividad_id;
            $actividad['fecha'] = date('Y-m-d');

            $Reg = Asistencia::guardarAsistencia($asistencia, $actividad);


            if($Reg > 0 && $Reg != 4){
                $data['strMensaje'] = 'La asistencia se registró correctamente';
                $data['intResultado'] = 1;
                $data['rows'] = $empleado;
            }else{
                $data['intResultado'] = 0;
                $data['strMensaje'] = 'Ocurrio un error al registrar la asistencia';
            }

            if ($Reg == 4){
                $data['strMensaje'] = 'El Empleado ya tiene asistencia registrada';
                $data['intResultado'] = 1;
                $data['rows'] = $empleado;
            };

        }
        else{
            $data['intResultado'] = 0;
            $data['strMensaje'] = 'El empleado no existe';
        }
        echo json_encode($data);
    }

    public function guardarActividad(Request $request)
    {
        $data['strMensaje'] = '';
        $data['intResultado'] = 0;

        $result = DB::table('asistencias_actividades')
                  ->where('empleado_id','=',$request->intEmpleado)
                  ->where(DB::raw("date_format(fecha , '%Y-%m-%d')"), '=', $request->strFecha)
                  ->update(['actividad_id' => $request->intArea]);

        if($result > 0){
            $data['strMensaje'] = 'La asistencia se registró correctamente';
            $data['intResultado'] = 1;
            $data['rows'] = $result;
        }else{
            $data['intResultado'] = 0;
            $data['strMensaje'] = 'Ocurrio un error al registrar la asistencia';
        }

    }

    public function descargaAsistencia(Request $request)
    {
        $data['rows'] = Asistencia::descargaAsistencia($request->fecha);
        echo json_encode($data);
    }

    public function APIdescargaActividad()
    {
        $data['rows'] = Asistencia::APIdescargaActividad();
        echo json_encode($data);
    }

    public function APIdescargaLotes()
    {
        $data['rows'] = Asistencia::APIdescargaLotes();
        echo json_encode($data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Asistencia  $asistencia
     * @return \Illuminate\Http\Response
     */
    public function show(Asistencia $asistencia)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Asistencia  $asistencia
     * @return \Illuminate\Http\Response
     */
    public function edit(Asistencia $asistencia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Asistencia  $asistencia
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Asistencia $asistencia)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Asistencia  $asistencia
     * @return \Illuminate\Http\Response
     */
    public function destroy(Asistencia $asistencia)
    {
        //
    }
}
