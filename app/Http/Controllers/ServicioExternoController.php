<?php

namespace App\Http\Controllers;

use App\Models\ServicioExterno;
use App\Models\Bocamina;
use App\Models\Pago;
use App\Models\Anticipo;
use App\Models\FondoPago;
use Illuminate\Http\Request;

class ServicioExternoController extends Controller
{
    public function index(Request $request)
    {
        $query = ServicioExterno::with('bocamina');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('chofer_operador', 'like', "%{$buscar}%")
                  ->orWhere('placa_maquinaria', 'like', "%{$buscar}%")
                  ->orWhere('numero_comprobante', 'like', "%{$buscar}%")
                  ->orWhere('origen_destino', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('tipo_servicio')) {
            $query->where('tipo_servicio', $request->tipo_servicio);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $servicios = $query->orderBy('fecha', 'desc')->orderBy('id', 'desc')->get();

        $total_recargado = FondoPago::sum('monto');
        $total_gastado_pagos = Pago::sum('monto_pagado');
        $total_gastado_anticipos = Anticipo::sum('monto');
        $total_gastado_servicios = ServicioExterno::sum('monto_total');
        $saldo_caja = $total_recargado - ($total_gastado_pagos + $total_gastado_anticipos + $total_gastado_servicios);

        return view('servicios_externos.index', compact(
            'servicios',
            'total_gastado_servicios',
            'saldo_caja'
        ));
    }

    public function create()
    {
        $bocaminas = Bocamina::orderBy('nombre')->get();
        
        $total_recargado = FondoPago::sum('monto');
        $total_gastado_pagos = Pago::sum('monto_pagado');
        $total_gastado_anticipos = Anticipo::sum('monto');
        $total_gastado_servicios = ServicioExterno::sum('monto_total');
        $saldo_caja = $total_recargado - ($total_gastado_pagos + $total_gastado_anticipos + $total_gastado_servicios);

        return view('servicios_externos.create', compact('bocaminas', 'saldo_caja'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha'              => 'required|date',
            'numero_comprobante' => 'nullable|string|max:100',
            'tipo_servicio'      => 'required|string|max:255',
            'chofer_operador'    => 'required|string|max:255',
            'placa_maquinaria'   => 'required|string|max:100', // Fundamental
            'bocamina_id'        => 'nullable|exists:bocaminas,id',
            'cantidad'           => 'required|numeric|min:0.01',
            'unidad_medida'      => 'required|string|max:50',
            'precio_unitario'    => 'required|numeric|min:0',
            'monto_total'        => 'required|numeric|min:0.01',
            'origen_destino'     => 'nullable|string|max:255',
            'metodo_pago'        => 'required|string|in:efectivo,cheque,transferencia',
            'entregado_por'      => 'nullable|string|max:255',
            'observacion'        => 'nullable|string',
        ]);

        if (empty($data['entregado_por'])) {
            $data['entregado_por'] = auth()->user()->name ?? 'Administración General';
        }

        $servicio = ServicioExterno::create($data);

        return redirect()->route('servicios-externos.show', $servicio->id)->with('success', 'Pago de servicio externo registrado con éxito.');
    }

    public function show(ServicioExterno $servicios_externo)
    {
        $servicio = $servicios_externo->load('bocamina');
        return view('servicios_externos.recibo', compact('servicio'));
    }

    public function edit(ServicioExterno $servicios_externo)
    {
        $servicio = $servicios_externo;
        $bocaminas = Bocamina::orderBy('nombre')->get();
        return view('servicios_externos.edit', compact('servicio', 'bocaminas'));
    }

    public function update(Request $request, ServicioExterno $servicios_externo)
    {
        $data = $request->validate([
            'fecha'              => 'required|date',
            'numero_comprobante' => 'nullable|string|max:100',
            'tipo_servicio'      => 'required|string|max:255',
            'chofer_operador'    => 'required|string|max:255',
            'placa_maquinaria'   => 'required|string|max:100',
            'bocamina_id'        => 'nullable|exists:bocaminas,id',
            'cantidad'           => 'required|numeric|min:0.01',
            'unidad_medida'      => 'required|string|max:50',
            'precio_unitario'    => 'required|numeric|min:0',
            'monto_total'        => 'required|numeric|min:0.01',
            'origen_destino'     => 'nullable|string|max:255',
            'metodo_pago'        => 'required|string|in:efectivo,cheque,transferencia',
            'entregado_por'      => 'nullable|string|max:255',
            'observacion'        => 'nullable|string',
        ]);

        $servicios_externo->update($data);

        return redirect()->route('servicios-externos.index')->with('success', 'Pago de servicio externo actualizado con éxito.');
    }

    public function destroy(ServicioExterno $servicios_externo)
    {
        $servicios_externo->delete();
        return redirect()->route('servicios-externos.index')->with('success', 'Registro de servicio externo eliminado con éxito.');
    }
}
