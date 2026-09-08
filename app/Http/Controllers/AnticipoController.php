<?php

namespace App\Http\Controllers;

use App\Models\Anticipo;
use App\Models\Trabajador;
use App\Models\Bocamina;
use Illuminate\Http\Request;

class AnticipoController extends Controller
{
    public function index(Request $request)
    {
        $query = Anticipo::with(['trabajador.bocamina', 'trabajador.tipoContrato']);

        if ($request->filled('trabajador_id')) {
            $query->where('trabajador_id', $request->trabajador_id);
        }

        if ($request->filled('bocamina_id')) {
            $query->whereHas('trabajador', function($q) use ($request) {
                $q->where('bocamina_id', $request->bocamina_id);
            });
        }

        if ($request->filled('estado')) {
            if ($request->estado === 'pendiente') {
                $query->where('saldo', '>', 0);
            } elseif ($request->estado === 'pagado') {
                $query->where('saldo', '=', 0);
            }
        }

        $anticipos = $query->orderBy('fecha', 'desc')->get();
        $trabajadores = Trabajador::where('estado', 'activo')->orderBy('nombre')->get();
        $bocaminas = Bocamina::orderBy('nombre')->get();

        return view('anticipos.index', compact('anticipos', 'trabajadores', 'bocaminas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'fecha' => 'required|date',
            'monto' => 'required|numeric|min:0.01',
            'observacion' => 'nullable|string|max:255',
            'dias_debe' => 'nullable|numeric|min:0',
        ]);

        \App\Models\Anticipo::create([
            'trabajador_id' => $request->trabajador_id,
            'fecha' => $request->fecha,
            'monto' => $request->monto,
            'saldo' => $request->monto,
            'observacion' => $request->observacion,
            'dias_debe' => $request->dias_debe ?? 0,
        ]);

        return redirect()->back()->with('success', 'Adelanto / Anticipo registrado con éxito.');
    }

    public function update(Request $request, Anticipo $anticipo)
    {
        $request->validate([
            'fecha' => 'required|date',
            'monto' => 'required|numeric|min:0.01',
            'observacion' => 'nullable|string|max:255',
            'dias_debe' => 'nullable|numeric|min:0',
        ]);

        $diferencia = $request->monto - $anticipo->monto;
        $nuevoSaldo = max(0, $anticipo->saldo + $diferencia);

        $anticipo->update([
            'fecha' => $request->fecha,
            'monto' => $request->monto,
            'saldo' => $nuevoSaldo,
            'observacion' => $request->observacion,
            'dias_debe' => $request->dias_debe ?? $anticipo->dias_debe,
        ]);

        return redirect()->back()->with('success', 'Adelanto actualizado con éxito.');
    }

    public function destroy(Anticipo $anticipo)
    {
        \Illuminate\Support\Facades\DB::transaction(function() use ($anticipo) {
            $anticipo->pagos()->detach();
            $anticipo->delete();
        });
        return redirect()->back()->with('success', 'Adelanto / Anticipo eliminado con éxito.');
    }

    public function recibo(Anticipo $anticipo)
    {
        $anticipo->load('trabajador.bocamina');
        return view('anticipos.recibo', compact('anticipo'));
    }
}
