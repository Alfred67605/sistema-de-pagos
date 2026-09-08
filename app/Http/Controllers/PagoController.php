<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Trabajador;
use App\Models\Anticipo;
use App\Models\Contrato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pago::with(['trabajador']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('trabajador', function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%");
            });
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $pagos = $query->orderBy('fecha', 'desc')->get();

        // Calculate totals for summary cards
        $hasFondos = \App\Models\FondoPago::count() > 0;
        if ($hasFondos) {
            $total_recargado = \App\Models\FondoPago::sum('monto');
            $total_gastado_pagos = Pago::sum('monto_pagado');
            $total_gastado_anticipos = \App\Models\Anticipo::sum('monto');
            $total_gastado = $total_gastado_pagos + $total_gastado_anticipos;
            $saldo_caja = $total_recargado - $total_gastado;
        } else {
            $total_recargado = 0;
            $total_gastado_pagos = 0;
            $total_gastado_anticipos = 0;
            $total_gastado = 0;
            $saldo_caja = 0;
        }

        $trabajadores = Trabajador::with('bocamina')->where('estado', 'activo')->orderBy('nombre')->get();

        return view('pagos.index', compact('pagos', 'total_recargado', 'total_gastado_pagos', 'total_gastado_anticipos', 'total_gastado', 'saldo_caja', 'trabajadores'));
    }

    public function fondosIndex(Request $request)
    {
        $rango = $request->rango;
        $fecha_desde = $request->fecha_desde;
        $fecha_hasta = $request->fecha_hasta;

        if ($rango === '1_mes') {
            $fecha_desde = now()->subMonth()->toDateString();
            $fecha_hasta = now()->toDateString();
        } elseif ($rango === '3_meses') {
            $fecha_desde = now()->subMonths(3)->toDateString();
            $fecha_hasta = now()->toDateString();
        } elseif ($rango === 'este_mes') {
            $fecha_desde = now()->startOfMonth()->toDateString();
            $fecha_hasta = now()->endOfMonth()->toDateString();
        }

        $query = \App\Models\FondoPago::query();
        if ($fecha_desde) {
            $query->whereDate('fecha', '>=', $fecha_desde);
        }
        if ($fecha_hasta) {
            $query->whereDate('fecha', '<=', $fecha_hasta);
        }

        // Fetch chronological order to compute running balances & carryover
        $allFondos = \App\Models\FondoPago::orderBy('fecha', 'asc')->get();

        $remanente_acumulado = 0;
        $processedFondos = collect();

        foreach ($allFondos as $index => $fondo) {
            $fechaInicio = $fondo->fecha;
            $sigFondo = isset($allFondos[$index + 1]) ? $allFondos[$index + 1] : null;

            // Expenses during this funding interval
            $queryPagos = Pago::whereDate('fecha', '>=', $fechaInicio);
            $queryAnticipos = \App\Models\Anticipo::whereDate('fecha', '>=', $fechaInicio);

            if ($sigFondo) {
                $queryPagos->whereDate('fecha', '<', $sigFondo->fecha);
                $queryAnticipos->whereDate('fecha', '<', $sigFondo->fecha);
            }

            $gastosPagos = $queryPagos->sum('monto_pagado');
            $gastosAnticipos = $queryAnticipos->sum('monto');
            $gastadoIntervalo = $gastosPagos + $gastosAnticipos;

            $fondo->remanente_anterior = $remanente_acumulado;
            $fondo->total_disponible = $remanente_acumulado + $fondo->monto;
            $fondo->gastado_periodo = $gastadoIntervalo;
            $fondo->sobrante_remanente = max(0, $fondo->total_disponible - $gastadoIntervalo);

            // Carryover to next recarga
            $remanente_acumulado = $fondo->sobrante_remanente;

            // Filter by requested date range if set
            $inRange = true;
            if ($fecha_desde && $fondo->fecha->format('Y-m-d') < $fecha_desde) $inRange = false;
            if ($fecha_hasta && $fondo->fecha->format('Y-m-d') > $fecha_hasta) $inRange = false;

            if ($inRange) {
                $processedFondos->push($fondo);
            }
        }

        // Display newest first
        $fondos = $processedFondos->reverse();

        $hasFondos = \App\Models\FondoPago::count() > 0;
        if ($hasFondos) {
            $total_recargado = \App\Models\FondoPago::sum('monto');
            $total_gastado_pagos = Pago::sum('monto_pagado');
            $total_gastado_anticipos = \App\Models\Anticipo::sum('monto');
            $total_gastado = $total_gastado_pagos + $total_gastado_anticipos;
            $saldo_caja = $total_recargado - $total_gastado;
        } else {
            $total_recargado = 0;
            $total_gastado_pagos = 0;
            $total_gastado_anticipos = 0;
            $total_gastado = 0;
            $saldo_caja = 0;
        }

        return view('pagos.fondos', compact(
            'fondos',
            'total_recargado',
            'total_gastado_pagos',
            'total_gastado_anticipos',
            'total_gastado',
            'saldo_caja',
            'rango',
            'fecha_desde',
            'fecha_hasta'
        ));
    }

    public function storeFondo(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'monto' => 'required|numeric|min:0.01',
            'observacion' => 'nullable|string|max:255',
        ]);

        \App\Models\FondoPago::create($request->all());

        return redirect()->route('fondos-caja.index')->with('success', 'Recarga de fondo de caja registrada con éxito.');
    }

    public function destroyFondo($id)
    {
        $fondo = \App\Models\FondoPago::findOrFail($id);
        $fondo->delete();

        return redirect()->route('fondos-caja.index')->with('success', 'Registro de recarga eliminado con éxito.');
    }

    public function reiniciarCaja(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'nullable|numeric|min:0',
            'observacion' => 'nullable|string|max:255',
        ]);

        \App\Models\FondoPago::truncate();

        $montoInicial = $request->filled('monto_inicial') ? (float)$request->monto_inicial : 0;
        if ($montoInicial > 0) {
            \App\Models\FondoPago::create([
                'fecha' => now()->toDateString(),
                'monto' => $montoInicial,
                'observacion' => $request->observacion ?: 'Fondeo Inicial — Reinicio de Caja Chica',
            ]);
        }

        return redirect()->route('fondos-caja.index')->with('success', 'Caja chica reiniciada con éxito. Se ha empezado de cero un nuevo período.');
    }

    public function limpiarAnteriores(Request $request)
    {
        $fechaCorte = $request->fecha_corte ?: now()->toDateString();
        \App\Models\FondoPago::whereDate('fecha', '<=', $fechaCorte)->delete();

        return redirect()->route('fondos-caja.index')->with('success', 'Recargas anteriores eliminadas con éxito.');
    }

    public function create()
    {
        $trabajadores = Trabajador::with(['bocamina', 'tipoContrato'])
                                  ->where('estado', 'activo')
                                  ->orderBy('nombre')
                                  ->get();
        $bocaminas = \App\Models\Bocamina::all();
        
        $total_recargado = \App\Models\FondoPago::sum('monto');
        $total_gastado_pagos = Pago::sum('monto_pagado');
        $total_gastado_anticipos = Anticipo::sum('monto');
        $saldo_caja = $total_recargado - ($total_gastado_pagos + $total_gastado_anticipos);

        return view('pagos.create', compact('trabajadores', 'bocaminas', 'saldo_caja'));
    }

    public function getTrabajadorData($id)
    {
        $trabajador = Trabajador::with(['bocamina', 'tipoContrato'])->findOrFail($id);
        
        // Pending advances
        $anticipos = Anticipo::where('trabajador_id', $id)
                              ->where('saldo', '>', 0)
                              ->orderBy('fecha', 'asc')
                              ->get();

        // Pending balances from previous payments (where owner paid less)
        $saldosPendientes = Pago::where('trabajador_id', $id)
                                ->where('saldo_pendiente', '>', 0)
                                ->where('saldo_liquidado', false)
                                ->get();

        return response()->json([
            'trabajador' => $trabajador,
            'bocamina_nombre' => $trabajador->bocamina ? $trabajador->bocamina->nombre : 'Sin Bocamina Asignada',
            'cargo' => $trabajador->rol ?: 'ayudante',
            'tipo_contrato_nombre' => $trabajador->tipoContrato ? $trabajador->tipoContrato->nombre : 'Sin Tipo de Contrato',
            'tarifa_acordada' => $trabajador->tarifa_acordada ? (float)$trabajador->tarifa_acordada : 0.00,
            'anticipos' => $anticipos,
            'total_anticipos_pendientes' => (float)$anticipos->sum('saldo'),
            'saldos_pendientes' => $saldosPendientes,
            'total_saldos_pendientes' => (float)$saldosPendientes->sum('saldo_pendiente'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'trabajador_id' => 'required|exists:trabajadores,id',
            'fecha' => 'required|date',
            'tarifa_pago' => 'nullable|numeric|min:0',
            'cantidad_trabajada' => 'nullable|numeric|min:0',
            'tipo_contrato_nombre' => 'nullable|string|max:255',
            'items' => 'nullable|array',
            'items.*.tipo_trabajo' => 'nullable|string',
            'items.*.cantidad' => 'nullable|numeric|min:0',
            'items.*.precio_unitario' => 'nullable|numeric|min:0',
            'items.*.subtotal' => 'nullable|numeric|min:0',
            'bonos' => 'required|numeric|min:0',
            'descuentos' => 'required|numeric|min:0',
            'monto_pagado' => 'nullable|numeric|min:0',
            'tipo_cambio' => 'required|numeric|min:0.01',
            'observacion' => 'nullable|string',
            'deducciones_anticipos' => 'nullable|array',
            'deducciones_anticipos.*' => 'nullable|numeric|min:0',
            'metodo_pago' => 'nullable|string|in:efectivo,cheque,transferencia',
            'entregado_por' => 'nullable|string|max:255',
            'dias_debe' => 'nullable|numeric|min:0',
            'adelanto_observacion' => 'nullable|string|max:255',
        ]);

        $trabajadorId = $request->trabajador_id;
        
        // Calculate subtotal from items if present, or from single inputs
        if ($request->has('items') && is_array($request->items) && count($request->items) > 0) {
            $subtotal = 0;
            $totalCant = 0;
            $firstTarifa = 0;
            $firstTipo = 'Jornales';
            foreach ($request->items as $it) {
                $subtotal += (float)($it['subtotal'] ?? 0);
                $totalCant += (float)($it['cantidad'] ?? 0);
                if ($firstTarifa == 0 && !empty($it['precio_unitario'])) {
                    $firstTarifa = (float)$it['precio_unitario'];
                    $firstTipo = $it['tipo_trabajo'] ?? 'Jornales';
                }
            }
            $tarifaPago = $request->filled('tarifa_pago') ? (float)$request->tarifa_pago : $firstTarifa;
            $cantidadTrabajada = $request->filled('cantidad_trabajada') ? (float)$request->cantidad_trabajada : $totalCant;
            $tipoContratoNombre = $request->filled('tipo_contrato_nombre') ? $request->tipo_contrato_nombre : $firstTipo;
        } else {
            $tarifaPago = (float) ($request->tarifa_pago ?? 0);
            $cantidadTrabajada = (float) ($request->cantidad_trabajada ?? 0);
            $tipoContratoNombre = $request->tipo_contrato_nombre ?? 'General';
            $subtotal = $tarifaPago * $cantidadTrabajada;
        }
        
        $bonos = (float) $request->bonos;
        $descuentos = (float) $request->descuentos;
        $montoPagado = $request->has('monto_pagado') && $request->monto_pagado !== null ? (float) $request->monto_pagado : null;
        $tipoCambio = (float) $request->tipo_cambio;

        if ($descuentos > 0.01 && empty(trim($request->observacion))) {
            return back()->withErrors(['observacion' => 'Debe ingresar una observación explicando el motivo del descuento.'])->withInput();
        }

        // Perform the entire payment process inside a transaction
        $pago = DB::transaction(function() use ($trabajadorId, $subtotal, $tarifaPago, $cantidadTrabajada, $tipoContratoNombre, $bonos, $descuentos, $montoPagado, $tipoCambio, $request) {
            
            // Load outstanding pending balances from previous payments
            $prevSaldos = Pago::where('trabajador_id', $trabajadorId)
                              ->where('saldo_pendiente', '>', 0)
                              ->where('saldo_liquidado', false)
                              ->get();
            
            $totalSaldosPrev = (float) $prevSaldos->sum('saldo_pendiente');

            // Capacidad de pago includes previous week's pending balances!
            $capacidadPago = $subtotal + $bonos - $descuentos + $totalSaldosPrev;
            
            if ($capacidadPago < 0) {
                throw new \Exception('La capacidad de pago total no puede ser negativa.');
            }

            // Get outstanding advances
            $anticipos = Anticipo::where('trabajador_id', $trabajadorId)
                                  ->where('saldo', '>', 0)
                                  ->orderBy('fecha', 'asc')
                                  ->get();

            $totalDeducido = 0.0;
            $deduccionesDetalle = [];
            $customDeducciones = $request->input('deducciones_anticipos', []);

            foreach ($anticipos as $anticipo) {
                $descuentoDeseado = isset($customDeducciones[$anticipo->id]) ? (float)$customDeducciones[$anticipo->id] : 0.0;
                
                if ($descuentoDeseado <= 0) {
                    continue;
                }

                // Make sure we don't deduct more than the advance's balance
                $descuento = min((float)$anticipo->saldo, $descuentoDeseado);

                // Enforce that the sum of deductions does not exceed capacity
                if ($totalDeducido + $descuento > $capacidadPago) {
                    $descuento = $capacidadPago - $totalDeducido;
                    if ($descuento <= 0) {
                        break;
                    }
                }
                
                $anticipo->saldo = (float)$anticipo->saldo - $descuento;
                if ($anticipo->saldo <= 0.01) {
                    $anticipo->saldo = 0;
                    $anticipo->pagado = true;
                }
                $anticipo->save();

                $totalDeducido += $descuento;

                $deduccionesDetalle[$anticipo->id] = ['monto_descontado' => $descuento];
            }

            // Calculated net payout due: subtotal + bonos - descuentos - anticipos + previous unpaid balances
            $neto = $subtotal + $bonos - $descuentos - $totalDeducido + $totalSaldosPrev;

            $actualMontoPagado = $montoPagado === null ? $neto : $montoPagado;

            // Calculate the difference between calculated net due and actual cash paid
            $diferencia = $neto - $actualMontoPagado;
            
            $saldoPendiente = 0.0;
            $saldoLiquidado = true;

            if ($diferencia > 0.01) {
                // Owner paid less: remaining is saved as a debt to the worker
                $saldoPendiente = $diferencia;
                $saldoLiquidado = false;
            } elseif ($diferencia < -0.01) {
                // Owner paid more / worker received an advance: extra cash becomes a new advance (anticipo)
                $extra = abs($diferencia);
                $diasDebe = $request->filled('dias_debe') && (float)$request->dias_debe > 0
                    ? (float)$request->dias_debe
                    : ($tarifaPago > 0 ? round($extra / $tarifaPago, 2) : 0);

                $observacionAdelanto = $request->filled('adelanto_observacion')
                    ? $request->adelanto_observacion
                    : 'Adelanto / Excedente (' . ($request->numero_nota ?: 'Liquidación #' . date('Ymd')) . ($diasDebe > 0 ? ' - Debe ' . $diasDebe . ' días' : '') . ')';

                Anticipo::create([
                    'trabajador_id' => $trabajadorId,
                    'fecha' => $request->fecha,
                    'monto' => $extra,
                    'saldo' => $extra,
                    'pagado' => false,
                    'observacion' => $observacionAdelanto,
                    'dias_debe' => $diasDebe,
                ]);
            }

            // Create Pago record
            $pago = Pago::create([
                'trabajador_id' => $trabajadorId,
                'fecha' => $request->fecha,
                'tarifa_pago' => $tarifaPago,
                'cantidad_trabajada' => $cantidadTrabajada,
                'tipo_contrato_nombre' => $tipoContratoNombre,
                'subtotal' => $subtotal,
                'bonos' => $bonos,
                'descuentos' => $descuentos,
                'anticipos_descontados' => $totalDeducido,
                'neto' => $neto,
                'monto_pagado' => $actualMontoPagado,
                'saldo_pendiente' => $saldoPendiente,
                'saldo_liquidado' => $saldoLiquidado,
                'tipo_cambio' => $tipoCambio,
                'observacion' => $request->observacion,
                'metodo_pago' => $request->input('metodo_pago', 'efectivo'),
                'entregado_por' => $request->input('entregado_por') ?: (auth()->user()->name ?? 'Administración TORMAN'),
            ]);

            // Save individual items if provided
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $it) {
                    if (!empty($it['tipo_trabajo']) || !empty($it['subtotal'])) {
                        $pago->items()->create([
                            'contrato_id' => !empty($it['contrato_id']) ? $it['contrato_id'] : null,
                            'tipo_trabajo' => $it['tipo_trabajo'] ?? 'General',
                            'descripcion' => $it['descripcion'] ?? null,
                            'cantidad' => (float)($it['cantidad'] ?? 1),
                            'precio_unitario' => (float)($it['precio_unitario'] ?? 0),
                            'subtotal' => (float)($it['subtotal'] ?? 0),
                        ]);
                    }
                }
            }

            // Mark previous week pending balances as liquidated
            if ($totalSaldosPrev > 0) {
                Pago::where('trabajador_id', $trabajadorId)
                    ->where('saldo_pendiente', '>', 0)
                    ->where('saldo_liquidado', false)
                    ->update(['saldo_liquidado' => true]);
            }

            // Link advances to Pago in pivot table
            if (!empty($deduccionesDetalle)) {
                $pago->anticipos()->attach($deduccionesDetalle);
            }

            return $pago;
        });

        return redirect()->route('pagos.show', $pago->id)->with('success', 'Pago procesado exitosamente.');
    }

    public function show(Pago $pago)
    {
        $pago->load([
            'trabajador.tipoContrato',
            'anticipos' => function($q) {
                $q->withPivot('monto_descontado');
            }
        ]);

        return view('pagos.recibo', compact('pago'));
    }

    public function edit(Pago $pago)
    {
        $pago->load('trabajador.bocamina');
        return view('pagos.edit', compact('pago'));
    }

    public function update(Request $request, Pago $pago)
    {
        $request->validate([
            'fecha' => 'required|date',
            'monto_pagado' => 'required|numeric|min:0',
            'observacion' => 'nullable|string|max:255',
        ]);

        $nuevoMontoPagado = (float)$request->monto_pagado;
        $diferencia = $pago->neto - $nuevoMontoPagado;

        $pago->update([
            'fecha' => $request->fecha,
            'monto_pagado' => $nuevoMontoPagado,
            'saldo_pendiente' => max(0, $diferencia),
            'saldo_liquidado' => $diferencia <= 0.01,
            'observacion' => $request->observacion ?: $pago->observacion,
            'es_editado' => true,
        ]);

        return redirect()->route('pagos.index')->with('success', 'Pago actualizado con éxito. El comprobante fue marcado como editado.');
    }

    public function destroy(Pago $pago)
    {
        DB::transaction(function() use ($pago) {
            // 1. Restaurar anticipos que fueron descontados en este pago
            foreach ($pago->anticipos as $anticipo) {
                $montoDescontado = (float)($anticipo->pivot->monto_descontado ?? 0);
                if ($montoDescontado > 0) {
                    $anticipo->saldo = (float)$anticipo->saldo + $montoDescontado;
                    $anticipo->pagado = false;
                    $anticipo->save();
                }
            }

            // 2. Si este pago generó un adelanto automático (cuando monto_pagado > neto)
            if ($pago->monto_pagado > $pago->neto) {
                $extra = $pago->monto_pagado - $pago->neto;
                $autoAnticipo = Anticipo::where('trabajador_id', $pago->trabajador_id)
                    ->whereDate('fecha', $pago->fecha)
                    ->where('monto', $extra)
                    ->where('saldo', $extra)
                    ->first();
                if ($autoAnticipo) {
                    $autoAnticipo->delete();
                }
            }

            // 3. Desvincular anticipos en tabla pivote
            $pago->anticipos()->detach();

            // 4. Eliminar ítems de detalle del pago
            $pago->items()->delete();

            // 5. Eliminar el registro del pago
            $pago->delete();
        });

        return redirect()->route('pagos.index')->with('success', 'Registro de pago eliminado con éxito. Los anticipos descontados fueron restaurados en la cuenta del trabajador.');
    }
}
