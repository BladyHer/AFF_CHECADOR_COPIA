<?php

namespace App\Http\Controllers;

use App\Puesto;
use App\Reporte;
use App\Actividad;
use App\Area;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;
use DB;

class ReporteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function asistencia(Request $request){
        $data['rows'] = Reporte::asistencia($request->strFecha, $request->intAreaId, $request->intPuestoId);
        $data['intNumRows'] = count($data['rows']);
        echo json_encode($data);
    }

    public function reporteGral(){
        $areas = Area::obtenerActivos();
        $puestos = Puesto::obtenerActivos();
        return view('reportes.reporteEntradaSalida', ['areas' => $areas,
            'puestos' => $puestos]);
    }

    public function reporteEntradaSalidaPDF($fecha, $area, $puesto){
        $data['rows'] = Reporte::reporteEntradaSalidaPDF($fecha, $area, $puesto);
        $data['intNumRows'] = count($data['rows']);
        $data['strFecha'] = $fecha;
        $data['intArea'] = $area;
        $data['intPuesto'] = $puesto;
        $pdf = PDF::loadView('reportes.pdf.reporteEntradaSalidaPDF', $data);
        return $pdf->stream();
    }

    //REPORTE ENTRADA / SALIDA COMPLETO

    public function reporteEntradaSalidaCom(){
        $areas = Area::obtenerActivos();
        $puestos = Puesto::obtenerActivos();
        return view('reportes.reporteEntradaSalidaCom', ['areas' => $areas,
            'puestos' => $puestos]);
    }

    public function asistenciaCom(Request $request){
        $data['rows'] = Reporte::asistenciaCom($request->strFecha, $request->intAreaId);
        $data['intNumRows'] = count($data['rows']);
        echo json_encode($data);
    }

    public function reporteEntradaSalidaComPDF($fecha, $area){
        $data['rows'] = Reporte::reporteEntradaSalidaComPDF($fecha, $area);
        $data['intNumRows'] = count($data['rows']);
        $data['strFecha'] = $fecha;
        $data['intArea'] = $area;
        $pdf = PDF::loadView('reportes.pdf.reporteEntradaSalidaComPDF', $data);
        return $pdf->stream();
    }
    //FIN REPORTE ENTRADA / SALIDA COMPLETO

    //REPORTE DE PASE TEMPORAL
    public function reporteTmp(){
        $areas = Area::obtenerActivos();
        $puestos = Puesto::obtenerActivos();
        return view('reportes.reporteTmp', ['areas' => $areas,
            'puestos' => $puestos]);
    }

    public function reporteTemporal(Request $request){
        $data['rows'] = Reporte::reporteTemporal($request->intAreaId, $request->strFechaSalida, $request->strFechaEntrada);
        $data['intNumRows'] = count($data['rows']);
        echo json_encode($data);
    }

    public function reporteTemporalPDF($area,$fechaSalida, $fechaEntrada){
        $data['rows'] = Reporte::reporteTemporalPDF($area, $fechaSalida, $fechaEntrada);
        $data['intNumRows'] = count($data['rows']);
        $data['fechaSalida'] = $fechaSalida;
        $data['intArea'] = $area;
        $pdf = PDF::loadView('reportes.pdf.reporteTmpPDF', $data);
        return $pdf->stream();
    }

    //FIN DE REPORTE TEMPORAL

    //REPORTE DE PERMISO DE ENTRADA

    public function reportePermisoEntrada(){
        $areas = Area::obtenerActivos();
        $puestos = Puesto::obtenerActivos();
        return view('reportes.reportePermisoEntrada', ['areas' => $areas,
            'puestos' => $puestos]);
    }

    public function asistenciaPermiso(Request $request){
        $data['rows'] = Reporte::asistenciaPermiso($request->strFecha, $request->intAreaId, $request->intPuestoId);
        $data['intNumRows'] = count($data['rows']);
        echo json_encode($data);
    }

    public function asistenciaPermisoPDF($fecha, $area, $puesto){
        $data['rows'] = Reporte::asistenciaPermisoPDF($fecha, $area, $puesto);
        $data['intNumRows'] = count($data['rows']);
        $data['strFecha'] = $fecha;
        $data['intPuesto'] = $puesto;
        $pdf = PDF::loadView('reportes.pdf.reportePermisoEntradaPDF', $data);
        return $pdf->stream();
    }

    //FIN DE REPORTE DE PERMISO DE ENTRADA

    //REPORTE ENTRADA / SALIDA COMISION

    public function reportePersonalComision(){
        $areas = Area::obtenerActivos();
        $puestos = Puesto::obtenerActivos();
        return view('reportes.reportePersonalComision', ['areas' => $areas,
            'puestos' => $puestos]);
    }

    public function asistenciaComision(Request $request){
        $data['rows'] = Reporte::asistenciaCom($request->strFecha, $request->intAreaId);
        $data['intNumRows'] = count($data['rows']);
        echo json_encode($data);
    }

    public function reportePersonalComisionPDF($fecha, $area){
        $data['rows'] = Reporte::reportePersonalComisionPDF($fecha, $area);
        $data['intNumRows'] = count($data['rows']);
        $data['strFecha'] = $fecha;
        $data['intArea'] = $area;
        $pdf = PDF::loadView('reportes.pdf.reportePersonalComisionPDF', $data);
        return $pdf->stream();
    }
    //FIN REPORTE ENTRADA / SALIDA COMISION

    //REPORTE ENTRADA / SALIDA ESPECIAL

    public function reportePersonalSalidaEspecial(){
        $areas = Area::obtenerActivos();
        $puestos = Puesto::obtenerActivos();
        return view('reportes.reportePersonalSalidaEspecial', ['areas' => $areas,
            'puestos' => $puestos]);
    }

    public function asistenciaSalidaEspecial(Request $request){
        $data['rows'] = Reporte::asistenciaSalidaEspecial($request->strFecha, $request->intAreaId);
        $data['intNumRows'] = count($data['rows']);
        echo json_encode($data);
    }

    public function reportePersonalSalidaEspecialPDF($fecha, $area){
        $data['rows'] = Reporte::reportePersonalSalidaEspecialPDF($fecha, $area);
        $data['intNumRows'] = count($data['rows']);
        $data['strFecha'] = $fecha;
        $data['intArea'] = $area;
        $pdf = PDF::loadView('reportes.pdf.reportePersonalComisionPDF', $data);
        return $pdf->stream();
    }
    //FIN REPORTE ENTRADA / SALIDA ESPECIAL


    //REPORTE INASISTENCIA

    public function reporteInasistencia(){
        $areas = Area::obtenerActivos();
        $puestos = Puesto::obtenerActivos();
        return view('reportes.reporteInasistencia', ['areas' => $areas,
            'puestos' => $puestos]);
    }

    public function inasistencia(Request $request){
        $data['rows'] = Reporte::inasistencia($request->strFecha, $request->intAreaId);
        $data['intNumRows'] = count($data['rows']);
        echo json_encode($data);
    }

    public function reporteInasistenciaPDF($fecha, $area){
        $data['rows'] = Reporte::reporteInasistenciaPDF($fecha, $area);
        $data['intNumRows'] = count($data['rows']);
        $data['strFecha'] = $fecha;
        $data['intArea'] = $area;
        $pdf = PDF::loadView('reportes.pdf.reporteInasistenciaPDF', $data);
        return $pdf->stream();
    }
    //FIN REPORTE INASISTENCIA

    //REPORTE REGISTROS DE TRABAJOR

    public function reportePersonalRegistro(){
        return view('reportes.reportePersonalRegistro');
    }

    public function asistenciaRegistro(Request $request){
        $data['rows'] = Reporte::asistenciaRegistro($request->strFecha,$request->strFecha2, $request->intEmpleadoID);
        $data['intNumRows'] = count($data['rows']);
        echo json_encode($data);
    }

    public function reportePersonalRegistroPDF($fecha, $fecha2, $intEmpleadoID){
        $data['rows'] = Reporte::reportePersonalRegistroPDF($fecha,$fecha2, $intEmpleadoID);
        $data['intNumRows'] = count($data['rows']);
        $data['strFecha'] = $fecha;
        $pdf = PDF::loadView('reportes.pdf.reportePersonalRegistroPDF', $data);
        return $pdf->stream();
    }
    //FIN REPORTE REGISTROS DE TRABAJOR

    //REPORTE RETARDOS

    public function reportePersonalRetardos(){
        $areas = Area::obtenerActivos();
        $puestos = Puesto::obtenerActivos();
        return view('reportes.reportePersonalRetardos', ['areas' => $areas, 'puestos' => $puestos]);
    }

    public function asistenciaRetardos(Request $request){
        $data['rows'] = Reporte::asistenciaRetardos($request->strFecha, $request->intAreaId, $request->intPuestoId);
        $data['intNumRows'] = count($data['rows']);
        echo json_encode($data);
    }

    public function reportePersonalRetardosPDF($fecha, $area, $puesto){
        $data['rows'] = Reporte::reportePersonalRetardosPDF($fecha, $area, $puesto);
        $data['intNumRows'] = count($data['rows']);
        $data['strFecha'] = $fecha;
        $data['intArea'] = $area;
        $data['intPuesto'] = $puesto;
        $pdf = PDF::loadView('reportes.pdf.reportePersonalRetardosPDF', $data);
        return $pdf->stream();
    }
    //FIN REPORTE RETARDOS

    //REPORTE ACTIVIDADES

    public function asistenciaActividades(Request $request)
    {
        $data['rows'] = Reporte::asistenciaActividades($request->strFecha, $request->intAreaId);
        $data['intNumRows'] = count($data['rows']);
        echo json_encode($data);
    }

    public function reporteActividades(){
        $areas = Area::obtenerActivos();
        $actividades = Actividad::obtenerActivos();
        return view('reportes.reporteActividades', ['areas' => $areas, 'actividades' => $actividades]);
    }

    public function reporteActividadesPDF($fecha, $area){
        $data['rows'] = Reporte::asistenciaActividades($fecha, $area);
        $data['intNumRows'] = count($data['rows']);
        $data['strFecha'] = $fecha;
        $data['intArea'] = $area;
        $pdf = PDF::loadView('reportes.pdf.reporteActividadesPDF', $data);
        return $pdf->stream();
    }

    //FIN REPORTE ACTIVIDADES

    //REPORTE ESPECIAL

    public function asistenciaRegistroEspecial(Request $request){
        $data['rows'] = Reporte::asistenciaRegistroEspecial($request->strFecha);
        $data['intNumRows'] = count($data['rows']);
        echo json_encode($data);
    }

    public function reporteRegistroEspecial(){
        return view('reportes.reporteEntradasEspecial');
    }

    public function reporteRegistroEspecialPDF($fecha){
        $data['rows'] = Reporte::asistenciaRegistroEspecial($fecha);
        $data['intNumRows'] = count($data['rows']);
        $data['strFecha'] = $fecha;
        $pdf = PDF::loadView('reportes.pdf.reporteEntradasEspecialPDF', $data);
        return $pdf->stream();
    }

    //FIN REPORTE ESPECIAL
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Reporte  $reporte
     * @return \Illuminate\Http\Response
     */
    public function show(Reporte $reporte)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Reporte  $reporte
     * @return \Illuminate\Http\Response
     */
    public function edit(Reporte $reporte)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Reporte  $reporte
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Reporte $reporte)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Reporte  $reporte
     * @return \Illuminate\Http\Response
     */
    public function destroy(Reporte $reporte)
    {
        //
    }
}
