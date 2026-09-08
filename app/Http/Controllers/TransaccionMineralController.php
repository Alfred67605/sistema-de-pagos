<?php

namespace App\Http\Controllers;

use App\Models\TransaccionMineral;
use App\Models\LoteAnalisis;
use App\Models\Bocamina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaccionMineralController extends Controller
{
    public function index(Request $request)
    {
        $query = TransaccionMineral::with(['bocamina', 'lote', 'analisis']);

        // Base filters for search
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('presentacion')) {
            $query->where('presentacion', $request->presentacion);
        }

        if ($request->filled('bocamina_id')) {
            $query->where('bocamina_id', $request->bocamina_id);
        }

        // Date range preset filter: hoy, semanal, mensual, personalizado
        if ($request->filled('rango_fecha')) {
            $rango = $request->rango_fecha;
            if ($rango === 'hoy') {
                $query->whereDate('fecha', now()->toDateString());
            } elseif ($rango === 'semanal') {
                $query->whereBetween('fecha', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()]);
            } elseif ($rango === 'mensual') {
                $query->whereMonth('fecha', now()->month)->whereYear('fecha', now()->year);
            } elseif ($rango === 'personalizado') {
                if ($request->filled('fecha_desde')) {
                    $query->whereDate('fecha', '>=', $request->fecha_desde);
                }
                if ($request->filled('fecha_hasta')) {
                    $query->whereDate('fecha', '<=', $request->fecha_hasta);
                }
            }
        } else {
            if ($request->filled('fecha_desde')) {
                $query->whereDate('fecha', '>=', $request->fecha_desde);
            }
            if ($request->filled('fecha_hasta')) {
                $query->whereDate('fecha', '<=', $request->fecha_hasta);
            }
        }

        // Provider/Client search
        if ($request->filled('cliente_proveedor')) {
            $query->where('cliente_proveedor', 'like', '%' . $request->cliente_proveedor . '%');
        }

        // Mineral filter (If filtering by mineral, show lotes/sales with that mineral)
        if ($request->filled('mineral')) {
            $query->where(function($q) use ($request) {
                // If it is a purchase (lote) containing the mineral
                $q->whereHas('analisis', function ($sq) use ($request) {
                    $sq->where('mineral', 'like', '%' . $request->mineral . '%');
                })
                // Or if it is a sale, and its parent lote contains the mineral
                ->orWhereHas('lote.analisis', function ($sq) use ($request) {
                    $sq->where('mineral', 'like', '%' . $request->mineral . '%');
                });
            });
        }

        // Ley filter
        if ($request->filled('ley')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('analisis', function ($sq) use ($request) {
                    $sq->where('ley', '>=', $request->ley);
                })
                ->orWhereHas('lote.analisis', function ($sq) use ($request) {
                    $sq->where('ley', '>=', $request->ley);
                });
            });
        }

        $transacciones = $query->orderBy('fecha', 'desc')->orderBy('id', 'desc')->get();

        // Calculate totals dynamically based on filters
        $total_ingresos = $transacciones->where('tipo', 'venta')->sum('monto_total');
        $total_egresos = $transacciones->where('tipo', 'compra')->sum('monto_total');
        $balance = $total_ingresos - $total_egresos;

        $bocaminas = Bocamina::orderBy('nombre')->get();

        // Load all active purchases (Lotes) with remaining stock for sales drop-down
        $lotesDisponibles = TransaccionMineral::with('analisis')
            ->where('tipo', 'compra')
            ->where(function($q) {
                $q->where('peso_disponible', '>', 0)
                  ->orWhere('cantidad_disponible', '>', 0);
            })
            ->orderBy('fecha', 'desc')
            ->get();

        // Calculate Stock Metrics for the Stock Tab
        $lotesDisponiblesCount = TransaccionMineral::where('tipo', 'compra')
            ->where('peso_disponible', '>', 0)
            ->count();

        $pesoDisponibleSum = TransaccionMineral::where('tipo', 'compra')
            ->sum('peso_disponible');

        $valorTotalStock = TransaccionMineral::where('tipo', 'compra')
            ->where('peso_disponible', '>', 0)
            ->select(DB::raw('SUM(peso_disponible * precio_unidad) as total_valor'))
            ->first()
            ->total_valor ?? 0;

        $comprasDelMes = TransaccionMineral::where('tipo', 'compra')
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->count();

        $ventasDelMes = TransaccionMineral::where('tipo', 'venta')
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->count();

        // Load all purchases (Lotes) for Stock list
        $todosLosLotes = TransaccionMineral::with(['analisis', 'bocamina', 'ventas.analisis'])
            ->where('tipo', 'compra')
            ->orderBy('fecha', 'desc')
            ->get();

        // Caja del Módulo 2 Metrics & Unified Ledger (Independiente de la caja de personal)
        $totalRecargadoCaja = \App\Models\CajaMineralRecarga::sum('monto');
        $cajaRecargas = \App\Models\CajaMineralRecarga::orderBy('fecha', 'desc')->orderBy('id', 'desc')->get();
        $totalComprasModulo = TransaccionMineral::where('tipo', 'compra')->sum('monto_total');
        $totalVentasModulo = TransaccionMineral::where('tipo', 'venta')->sum('monto_total');
        $saldoCajaModulo = ($totalRecargadoCaja + $totalVentasModulo) - $totalComprasModulo;

        // Unified Financial Ledger Movimientos for Caja del Módulo 2
        $movsList = collect();

        foreach ($cajaRecargas as $rec) {
            $movsList->push((object)[
                'id' => 'rec_' . $rec->id,
                'db_id' => $rec->id,
                'fecha' => \Carbon\Carbon::parse($rec->fecha),
                'tipo' => 'recarga',
                'glosa' => $rec->observacion ?: 'Recarga de fondo operativo de caja',
                'monto' => (float)$rec->monto,
                'es_ingreso' => true,
                'delete_route' => route('caja-minerales.destroy-recarga', $rec->id),
                'created_at' => $rec->created_at,
            ]);
        }

        $comprasAll = TransaccionMineral::where('tipo', 'compra')->get();
        foreach ($comprasAll as $comp) {
            $movsList->push((object)[
                'id' => 'comp_' . $comp->id,
                'db_id' => $comp->id,
                'fecha' => \Carbon\Carbon::parse($comp->fecha),
                'tipo' => 'compra',
                'glosa' => 'Compra Lote LOT-' . str_pad($comp->id, 5, '0', STR_PAD_LEFT) . ' (' . $comp->cliente_proveedor . ')',
                'monto' => (float)$comp->monto_total,
                'es_ingreso' => false,
                'delete_route' => null,
                'created_at' => $comp->created_at,
            ]);
        }

        $ventasAll = TransaccionMineral::where('tipo', 'venta')->get();
        foreach ($ventasAll as $vent) {
            $movsList->push((object)[
                'id' => 'vent_' . $vent->id,
                'db_id' => $vent->id,
                'fecha' => \Carbon\Carbon::parse($vent->fecha),
                'tipo' => 'venta',
                'glosa' => 'Venta de Mineral a ' . $vent->cliente_proveedor,
                'monto' => (float)$vent->monto_total,
                'es_ingreso' => true,
                'delete_route' => null,
                'created_at' => $vent->created_at,
            ]);
        }

        $movsSortedAsc = $movsList->sortBy(function($m) {
            return $m->fecha->format('Y-m-d') . '_' . $m->created_at->format('H:i:s') . '_' . $m->id;
        })->values();

        $runningSaldo = 0;
        foreach ($movsSortedAsc as $m) {
            if ($m->es_ingreso) {
                $runningSaldo += $m->monto;
            } else {
                $runningSaldo -= $m->monto;
            }
            $m->saldo_resultante = $runningSaldo;
        }

        $movimientosCaja = $movsSortedAsc->reverse()->values();

        $totalFondosDisponibles = $totalRecargadoCaja + $totalVentasModulo;
        $porcentajeUsoCaja = $totalFondosDisponibles > 0 
            ? min(100, round(($totalComprasModulo / $totalFondosDisponibles) * 100)) 
            : 0;

        return view('transacciones.index', compact(
            'transacciones',
            'total_ingresos',
            'total_egresos',
            'balance',
            'bocaminas',
            'lotesDisponibles',
            'lotesDisponiblesCount',
            'pesoDisponibleSum',
            'valorTotalStock',
            'comprasDelMes',
            'ventasDelMes',
            'todosLosLotes',
            'totalRecargadoCaja',
            'cajaRecargas',
            'totalComprasModulo',
            'totalVentasModulo',
            'saldoCajaModulo',
            'movimientosCaja',
            'porcentajeUsoCaja'
        ));
    }

    public function store(Request $request)
    {
        $rules = [
            'fecha' => 'required|date',
            'tipo' => 'required|in:compra,venta',
            'cliente_proveedor' => 'required|string|max:255',
            'bocamina_id' => 'nullable|exists:bocaminas,id',
            'observacion' => 'nullable|string',
            'destino' => 'nullable|string|max:255',
        ];

        if ($request->tipo === 'compra') {
            $rules['presentacion'] = 'required|string';
            $rules['presentacion_otro'] = 'nullable|string|max:100';
            $rules['cantidad'] = 'required|numeric|min:0';
            $rules['peso_bruto'] = 'required|numeric|min:0';
            $rules['humedad_porcentaje'] = 'required|numeric|min:0|max:100';
            $rules['peso_neto_seco'] = 'required|numeric|min:0';
            $rules['precio_unidad'] = 'required|numeric|min:0';
            $rules['monto_total'] = 'required|numeric|min:0';
            
            $rules['analisis'] = 'nullable|array';
            $rules['analisis.*.mineral'] = 'required_with:analisis|string|max:100';
            $rules['analisis.*.ley'] = 'required_with:analisis|numeric|min:0|max:100';
        } else {
            // It is a Venta (check if it's multi-lote array or fallback single-lote)
            if ($request->has('lotes') && is_array($request->lotes) && count($request->lotes) > 0) {
                $rules['lotes'] = 'required|array|min:1';
                $rules['lotes.*.lote_id'] = 'required|exists:transacciones_minerales,id';
                $rules['lotes.*.cantidad'] = 'required|numeric|min:0';
                $rules['lotes.*.peso_neto_seco'] = 'required|numeric|min:0';
                $rules['lotes.*.precio_unidad'] = 'required|numeric|min:0';
                $rules['lotes.*.monto_total'] = 'required|numeric|min:0';
                $rules['lotes.*.humedad_porcentaje'] = 'required|numeric|min:0|max:100';
            } else {
                $rules['lote_id'] = 'required|exists:transacciones_minerales,id';
                $rules['cantidad'] = 'required|numeric|min:0';
                $rules['peso_neto_seco'] = 'required|numeric|min:0';
                $rules['precio_unidad'] = 'required|numeric|min:0';
                $rules['monto_total'] = 'required|numeric|min:0';
                $rules['humedad_porcentaje'] = 'required|numeric|min:0|max:100';
            }
        }

        $request->validate($rules);

        return DB::transaction(function() use ($request) {
            $data = $request->only([
                'fecha', 'tipo', 'cliente_proveedor', 'bocamina_id',
                'cantidad', 'peso_neto_seco', 'precio_unidad', 'monto_total',
                'observacion', 'destino', 'humedad_porcentaje', 'peso_bruto'
            ]);

            if (empty($data['bocamina_id'])) {
                $data['bocamina_id'] = null;
            }

            if ($request->tipo === 'compra') {
                $data['presentacion'] = $request->presentacion;
                if ($request->presentacion === 'Otro') {
                    $data['presentacion_otro'] = $request->presentacion_otro;
                }

                $pesoBruto = (float) $request->input('peso_bruto', 0);
                $humedadPct = (float) $request->input('humedad_porcentaje', 0);
                // Humidity is deducted directly from total cost (money), not from weight
                $pesoNetoSeco = $pesoBruto;

                $data['peso_bruto'] = $pesoBruto;
                $data['humedad_porcentaje'] = $humedadPct;
                $data['peso_neto_seco'] = $pesoNetoSeco;
                $data['cantidad_disponible'] = $request->cantidad;
                $data['peso_disponible'] = $pesoNetoSeco;

                $transaccion = TransaccionMineral::create($data);

                // Save analysis records
                if ($request->has('analisis')) {
                    foreach ($request->analisis as $an) {
                        if (!empty($an['mineral']) && isset($an['ley'])) {
                            $transaccion->analisis()->create([
                                'mineral' => $an['mineral'],
                                'ley' => $an['ley'],
                            ]);
                        }
                    }
                }
            } else {
                // If it is a multi-lote sale request:
                if ($request->has('lotes') && is_array($request->lotes) && count($request->lotes) > 0) {
                    foreach ($request->lotes as $item) {
                        $lote = TransaccionMineral::findOrFail($item['lote_id']);

                        if ($item['peso_neto_seco'] > $lote->peso_disponible) {
                            return back()->withErrors(['error' => "El peso vendido supera el disponible en el lote LOT-" . str_pad($lote->id, 5, '0', STR_PAD_LEFT) . " ({$lote->peso_disponible} Kg disponibles)."])->withInput();
                        }
                        if ($item['cantidad'] > $lote->cantidad_disponible) {
                            return back()->withErrors(['error' => "La cantidad vendida supera la disponible en el lote LOT-" . str_pad($lote->id, 5, '0', STR_PAD_LEFT) . " ({$lote->cantidad_disponible} disponibles)."])->withInput();
                        }

                        // Decrement stock
                        $lote->decrement('peso_disponible', $item['peso_neto_seco']);
                        $lote->decrement('cantidad_disponible', $item['cantidad']);

                        TransaccionMineral::create([
                            'fecha' => $request->fecha,
                            'tipo' => 'venta',
                            'cliente_proveedor' => $request->cliente_proveedor,
                            'destino' => $request->destino,
                            'lote_id' => $lote->id,
                            'presentacion' => $lote->presentacion,
                            'presentacion_otro' => $lote->presentacion_otro,
                            'cantidad' => $item['cantidad'],
                            'peso_neto_seco' => $item['peso_neto_seco'],
                            'precio_unidad' => $item['precio_unidad'],
                            'monto_total' => $item['monto_total'],
                            'humedad_porcentaje' => $item['humedad_porcentaje'] ?? 0,
                            'observacion' => $request->observacion,
                        ]);
                    }
                } else {
                    // For Single Sales, load the lot and validate stock
                    $lote = TransaccionMineral::findOrFail($request->lote_id);

                    if ($request->peso_neto_seco > $lote->peso_disponible) {
                        return back()->withErrors(['peso_neto_seco' => "El peso vendido supera el disponible en el lote ({$lote->peso_disponible} Kg)"])->withInput();
                    }
                    if ($request->cantidad > $lote->cantidad_disponible) {
                        return back()->withErrors(['cantidad' => "La cantidad vendida supera la disponible en el lote ({$lote->cantidad_disponible})"])->withInput();
                    }

                    // Decrement stock
                    $lote->decrement('peso_disponible', $request->peso_neto_seco);
                    $lote->decrement('cantidad_disponible', $request->cantidad);

                    $data['lote_id'] = $lote->id;
                    // A sale inherits the parent lote presentation
                    $data['presentacion'] = $lote->presentacion;
                    $data['presentacion_otro'] = $lote->presentacion_otro;

                    $venta = TransaccionMineral::create($data);

                    // Save sale-specific lab analysis without modifying purchase lot's analysis
                    if ($request->has('analisis') && is_array($request->analisis)) {
                        foreach ($request->analisis as $an) {
                            if (!empty($an['mineral']) && isset($an['ley'])) {
                                $venta->analisis()->create([
                                    'mineral' => $an['mineral'],
                                    'ley' => $an['ley'],
                                ]);
                            }
                        }
                    }
                }
            }

            return redirect()->route('transacciones-minerales.index')->with('success', 'Transacción registrada con éxito.');
        });
    }

    public function storeRecarga(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'monto' => 'required|numeric|min:0.01',
            'observacion' => 'nullable|string|max:255',
        ]);

        \App\Models\CajaMineralRecarga::create([
            'fecha' => $request->fecha,
            'monto' => $request->monto,
            'observacion' => $request->observacion,
        ]);

        return redirect()->route('transacciones-minerales.index')->with('success', 'Recarga de caja del módulo registrada con éxito.');
    }

    public function destroyRecarga($id)
    {
        $recarga = \App\Models\CajaMineralRecarga::findOrFail($id);
        $recarga->delete();

        return redirect()->route('transacciones-minerales.index')->with('success', 'Registro de recarga de caja eliminado.');
    }

    public function show($id)
    {
        $transaccion = TransaccionMineral::with(['bocamina', 'lote', 'analisis', 'ventas'])->findOrFail($id);
        return response()->json($transaccion);
    }

    public function ticket($id)
    {
        $transaccion = TransaccionMineral::with(['bocamina', 'lote', 'analisis', 'ventas'])->findOrFail($id);
        return view('transacciones.ticket', compact('transaccion'));
    }

    public function update(Request $request, TransaccionMineral $transacciones_minerale)
    {
        $rules = [
            'fecha' => 'required|date',
            'tipo' => 'required|in:compra,venta',
            'cliente_proveedor' => 'required|string|max:255',
            'bocamina_id' => 'nullable|exists:bocaminas,id',
            'observacion' => 'nullable|string',
            'destino' => 'nullable|string|max:255',
            'cantidad' => 'required|numeric|min:0',
            'peso_neto_seco' => 'required|numeric|min:0',
            'precio_unidad' => 'required|numeric|min:0',
            'monto_total' => 'required|numeric|min:0',
            'humedad_porcentaje' => 'required|numeric|min:0|max:100',
        ];

        if ($request->tipo === 'compra') {
            $rules['presentacion'] = 'required|string';
            $rules['presentacion_otro'] = 'nullable|string|max:100';
            
            $rules['analisis'] = 'nullable|array';
            $rules['analisis.*.mineral'] = 'required_with:analisis|string|max:100';
            $rules['analisis.*.ley'] = 'required_with:analisis|numeric|min:0|max:100';
        } else {
            $rules['lote_id'] = 'required|exists:transacciones_minerales,id';
        }

        $request->validate($rules);

        return DB::transaction(function() use ($request, $transacciones_minerale) {
            $data = $request->only([
                'fecha', 'tipo', 'cliente_proveedor', 'bocamina_id',
                'cantidad', 'peso_neto_seco', 'precio_unidad', 'monto_total',
                'observacion', 'destino', 'humedad_porcentaje', 'peso_bruto'
            ]);

            if (empty($data['bocamina_id'])) {
                $data['bocamina_id'] = null;
            }

            if ($transacciones_minerale->tipo === 'venta') {
                // Reset old sale impact on stock
                $oldLote = $transacciones_minerale->lote;
                if ($oldLote) {
                    $oldLote->increment('peso_disponible', $transacciones_minerale->peso_neto_seco);
                    $oldLote->increment('cantidad_disponible', $transacciones_minerale->cantidad);
                }

                // If lot has changed or values updated, load new lot and deduct stock
                $newLote = TransaccionMineral::findOrFail($request->lote_id ?? $transacciones_minerale->lote_id);
                if ($request->peso_neto_seco > $newLote->peso_disponible) {
                    return back()->withErrors(['peso_neto_seco' => "El peso vendido supera el disponible en el lote ({$newLote->peso_disponible} Kg)"])->withInput();
                }
                if ($request->cantidad > $newLote->cantidad_disponible) {
                    return back()->withErrors(['cantidad' => "La cantidad vendida supera la disponible en el lote ({$newLote->cantidad_disponible})"])->withInput();
                }

                $newLote->decrement('peso_disponible', $request->peso_neto_seco);
                $newLote->decrement('cantidad_disponible', $request->cantidad);

                $data['lote_id'] = $newLote->id;
                $data['presentacion'] = $newLote->presentacion;
                $data['presentacion_otro'] = $newLote->presentacion_otro;

                $transacciones_minerale->update($data);
            } else {
                // Actualizar Registro de Compra (Lote)
                $data['presentacion'] = $request->presentacion;
                if ($request->presentacion === 'Otro') {
                    $data['presentacion_otro'] = $request->presentacion_otro;
                } else {
                    $data['presentacion_otro'] = null;
                }

                $pesoBruto = (float) $request->input('peso_bruto', 0);
                $humedadPct = (float) $request->input('humedad_porcentaje', 0);
                // Humidity is deducted directly from total cost (money), not from weight
                $pesoNetoSeco = $pesoBruto;

                $data['peso_bruto'] = $pesoBruto;
                $data['humedad_porcentaje'] = $humedadPct;
                $data['peso_neto_seco'] = $pesoNetoSeco;

                // Calcular el peso y cantidad ya vendidos previamente de este lote
                $totalSoldWeight = $transacciones_minerale->ventas()->sum('peso_neto_seco');
                $totalSoldQty = $transacciones_minerale->ventas()->sum('cantidad');

                if ($pesoNetoSeco < $totalSoldWeight) {
                    return back()->withErrors(['peso_neto_seco' => "El peso neto del lote no puede ser menor al peso ya vendido ({$totalSoldWeight} Kg)"])->withInput();
                }
                if ($request->cantidad < $totalSoldQty) {
                    return back()->withErrors(['cantidad' => "La cantidad del lote no puede ser menor a la cantidad ya vendida ({$totalSoldQty})"])->withInput();
                }

                // Recalcular el stock disponible en almacén
                $data['peso_disponible'] = $pesoNetoSeco - $totalSoldWeight;
                $data['cantidad_disponible'] = $request->cantidad - $totalSoldQty;

                $transacciones_minerale->update($data);

                // Actualizar leyes de laboratorio (sincronizar registros)
                $transacciones_minerale->analisis()->delete();
                if ($request->has('analisis')) {
                    foreach ($request->analisis as $an) {
                        if (!empty($an['mineral']) && isset($an['ley'])) {
                            $transacciones_minerale->analisis()->create([
                                'mineral' => $an['mineral'],
                                'ley' => $an['ley'],
                            ]);
                        }
                    }
                }
            }

            return redirect()->route('transacciones-minerales.index')->with('success', 'Transacción actualizada con éxito.');
        });
    }

    public function destroy(TransaccionMineral $transacciones_minerale)
    {
        return DB::transaction(function() use ($transacciones_minerale) {
            if ($transacciones_minerale->tipo === 'compra') {
                // Prevent deleting lot if it has associated sales
                if ($transacciones_minerale->ventas()->exists()) {
                    return back()->withErrors(['error' => 'No se puede eliminar este lote porque tiene ventas/salidas de stock registradas.']);
                }
                // Delete analysis before deleting lote (done automatically via cascade delete migration)
                $transacciones_minerale->delete();
            } else {
                // Reset stock on lote before deleting sale
                $lote = $transacciones_minerale->lote;
                if ($lote) {
                    $lote->increment('peso_disponible', $transacciones_minerale->peso_neto_seco);
                    $lote->increment('cantidad_disponible', $transacciones_minerale->cantidad);
                }
                $transacciones_minerale->delete();
            }

            return redirect()->route('transacciones-minerales.index')->with('success', 'Transacción eliminada con éxito.');
        });
    }
}
