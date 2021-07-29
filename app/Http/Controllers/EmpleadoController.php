<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Empleado;

class EmpleadoController extends Controller
{

    public function index()
    {
        $empleados = Empleado::latest()->paginate(5);

        return view('catalogos.empleado.index',compact('empleados'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('catalogos.empleado.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required',
            "nombre" => 'required',
            "apaterno" => 'required',
            "amaterno" => 'required',
            "sexo" => 'required',
            "punto_pago" => 'required',
            "sueldo" => 'required',
            "puestos" => 'required',
            "status" => 'required',
        ]);

        Empleado::create($request->all());

        return redirect()->route('empleados.index')
            ->with('success','Empleado registrado correctamente.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Campo  $campo
     * @return \Illuminate\Http\Response
     */
    public function show(Empleado $empleado)
    {
        return view('catalogos.empleado.show',compact('empleado'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Campo  $campo
     * @return \Illuminate\Http\Response
     */
    public function edit(Empleado $empleado)
    {
        return view('catalogos.empleado.edit',compact('empleado'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Campo  $campo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'id' => 'required',
            "nombre" => 'required',
            "apaterno" => 'required',
            "amaterno" => 'required',
            "sexo" => 'required',
            "punto_pago" => 'required',
            "sueldo" => 'required',
            "puestos" => 'required',
            "status" => 'required',
        ]);

        $empleado->update($request->all());

        return redirect()->route('empleados.index')
            ->with('success','Empleado actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Campo  $campo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Empleado $empleado)
    {
        $empleado->delete();

        return redirect()->route('empleados.index')
            ->with('success','Empleado borrado correctamente');
    }

}
