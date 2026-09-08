<?php

namespace App\Http\Controllers;

use App\Models\TipoTrabajo;
use Illuminate\Http\Request;

class TipoTrabajoController extends Controller
{
    public function index(Request $request)
    {
        // Auto-seed default types of work if missing
        $defaultTypes = [
            ['nombre' => 'Jornal', 'descripcion' => 'Pago por día trabajado o jornal realizado', 'estado' => 'activo'],
            ['nombre' => 'Volqueta (Por Viaje)', 'descripcion' => 'Flete / Servicio de volqueta pagado por viaje realizado', 'estado' => 'activo'],
            ['nombre' => 'Pala Cargadora (Por Hora)', 'descripcion' => 'Operación de pala cargadora / maquinaria pagada por hora', 'estado' => 'activo'],
            ['nombre' => 'Pala Cargadora (Por Volquetada)', 'descripcion' => 'Carguío con pala cargadora pagado por volquetada cargada', 'estado' => 'activo'],
            ['nombre' => 'Carguío', 'descripcion' => 'Trabajo de carguío de mineral o material', 'estado' => 'activo'],
            ['nombre' => 'Descarguío', 'descripcion' => 'Trabajo de descarguío de volqueta o sacos', 'estado' => 'activo'],
            ['nombre' => 'Transporte / Flete', 'descripcion' => 'Servicio de transporte y flete de material', 'estado' => 'activo'],
            ['nombre' => 'Maquinaria (Por Hora)', 'descripcion' => 'Operación de maquinaria pesada por hora', 'estado' => 'activo'],
            ['nombre' => 'Mantenimiento', 'descripcion' => 'Mantenimiento de herramientas y maquinaria', 'estado' => 'activo'],
            ['nombre' => 'Perforación', 'descripcion' => 'Perforación de pozo o avance de galería', 'estado' => 'activo'],
            ['nombre' => 'Limpieza', 'descripcion' => 'Limpieza de bocamina e interiores', 'estado' => 'activo'],
            ['nombre' => 'Otro', 'descripcion' => 'Otros tipos de trabajos generales', 'estado' => 'activo'],
        ];

        foreach ($defaultTypes as $type) {
            TipoTrabajo::firstOrCreate(
                ['nombre' => $type['nombre']],
                ['descripcion' => $type['descripcion'], 'estado' => $type['estado']]
            );
        }

        $query = TipoTrabajo::query();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $tiposTrabajo = $query->orderBy('nombre', 'asc')->get();

        $totalActivos = TipoTrabajo::where('estado', 'activo')->count();
        $totalInactivos = TipoTrabajo::where('estado', 'inactivo')->count();

        return view('tipos_trabajo.index', compact('tiposTrabajo', 'totalActivos', 'totalInactivos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:tipos_trabajo,nombre',
            'descripcion' => 'nullable|string|max:500',
            'estado' => 'required|in:activo,inactivo',
        ]);

        TipoTrabajo::create($data);

        return redirect()->route('tipos-trabajo.index')->with('success', 'Tipo de trabajo creado exitosamente.');
    }

    public function update(Request $request, $id)
    {
        $tipoTrabajo = TipoTrabajo::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:255|unique:tipos_trabajo,nombre,' . $tipoTrabajo->id,
            'descripcion' => 'nullable|string|max:500',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $tipoTrabajo->update($data);

        return redirect()->route('tipos-trabajo.index')->with('success', 'Tipo de trabajo actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $tipoTrabajo = TipoTrabajo::findOrFail($id);
        
        // Toggle status or delete if inactive
        if ($tipoTrabajo->estado === 'activo') {
            $tipoTrabajo->update(['estado' => 'inactivo']);
            return redirect()->route('tipos-trabajo.index')->with('success', 'Tipo de trabajo desactivado con éxito.');
        } else {
            $tipoTrabajo->delete();
            return redirect()->route('tipos-trabajo.index')->with('success', 'Tipo de trabajo eliminado con éxito.');
        }
    }
}
