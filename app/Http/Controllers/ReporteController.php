<?php

namespace App\Http\Controllers;

use App\Models\Bocamina;
use App\Models\Trabajador;
use App\Models\TipoContrato;
use App\Models\Anticipo;
use App\Models\Pago;
use App\Models\FondoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function dashboard()
    {
        $totalTrabajadores = Trabajador::count();
        $totalBocaminas = Bocamina::count();
        $totalContratosActivos = Trabajador::where('estado', 'activo')->whereNotNull('tipo_contrato_id')->count();
        $totalAnticiposPendientes = Anticipo::where('saldo', '>', 0)->sum('saldo');
        
        // Calculate available cash balance
        $total_recargado = FondoPago::sum('monto');
        $total_gastado_pagos = Pago::sum('monto_pagado');
        $total_gastado_anticipos = Anticipo::sum('monto');
        $saldo_caja = $total_recargado - ($total_gastado_pagos + $total_gastado_anticipos);
        
        // Mineral transactions stats (for Module 2)
        $totalVentasMineral = \App\Models\TransaccionMineral::where('tipo', 'venta')->sum('monto_total');
        $totalComprasMineral = \App\Models\TransaccionMineral::where('tipo', 'compra')->sum('monto_total');
        
        // Top 5 workers by production (subtotal of pagos)
        $topTrabajadores = Trabajador::withSum('pagos as trabajos_sum_subtotal', 'subtotal')
            ->withCount('pagos as trabajos_count')
            ->orderBy('trabajos_sum_subtotal', 'desc')
            ->take(5)
            ->get();
            
        // Optimized eager loading with specific columns
        $recientesAnticipos = Anticipo::with('trabajador:id,nombre')->orderBy('fecha', 'desc')->take(5)->get();
        $recientesPagos = Pago::with('trabajador:id,nombre')->orderBy('fecha', 'desc')->take(5)->get();
        $recientesTransaccionesMineral = \App\Models\TransaccionMineral::with('bocamina:id,nombre')
            ->orderBy('fecha', 'desc')
            ->take(5)
            ->get();

        // Chart data: Production (subtotal of pagos) by Bocamina - Optimized single indexed query
        $produccionBocaminas = Bocamina::leftJoin('trabajadores', 'trabajadores.bocamina_id', '=', 'bocaminas.id')
            ->leftJoin('pagos', 'pagos.trabajador_id', '=', 'trabajadores.id')
            ->select('bocaminas.nombre', DB::raw('COALESCE(SUM(pagos.subtotal), 0) as total'))
            ->groupBy('bocaminas.id', 'bocaminas.nombre')
            ->get();

        // Chart data: Payments by month (last 6 months)
        $driverName = DB::connection()->getDriverName();
        if ($driverName === 'pgsql') {
            $mesExp = DB::raw("EXTRACT(MONTH FROM fecha) as mes");
            $anioExp = DB::raw("EXTRACT(YEAR FROM fecha) as anio");
        } else {
            $mesExp = DB::raw("strftime('%m', fecha) as mes");
            $anioExp = DB::raw("strftime('%Y', fecha) as anio");
        }

        $pagosMensuales = Pago::select(
            $mesExp,
            $anioExp,
            DB::raw("SUM(neto) as total")
        )
        ->groupBy('anio', 'mes')
        ->orderBy('anio', 'desc')
        ->orderBy('mes', 'desc')
        ->take(6)
        ->get()
        ->reverse()
        ->map(function($item) {
            $date = Carbon::createFromDate($item->anio, $item->mes, 1);
            return [
                'etiqueta' => $date->translatedFormat('F Y'),
                'total' => $item->total
            ];
        });

        return view('dashboard', compact(
            'totalTrabajadores',
            'totalBocaminas',
            'totalContratosActivos',
            'totalAnticiposPendientes',
            'saldo_caja',
            'totalVentasMineral',
            'totalComprasMineral',
            'topTrabajadores',
            'recientesAnticipos',
            'recientesPagos',
            'recientesTransaccionesMineral',
            'produccionBocaminas',
            'pagosMensuales'
        ));
    }

    public function index(Request $request)
    {
        $tab = $request->input('tab', 'general');

        $trabajadores = Trabajador::where('estado', 'activo')->orderBy('nombre', 'asc')->get();
        $allTrabajadores = Trabajador::orderBy('nombre', 'asc')->get();
        $bocaminas = Bocamina::orderBy('nombre', 'asc')->get();
        $tiposContrato = TipoContrato::orderBy('nombre', 'asc')->get();
        $roles = Trabajador::whereNotNull('rol')->where('rol', '!=', '')->distinct()->pluck('rol');

        // Common Date Filters
        $filtroFecha = $request->input('filtro_fecha', 'personalizado');
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        if ($request->filled('filtro_fecha') && $filtroFecha !== 'personalizado') {
            $hoy = Carbon::today();
            if ($filtroFecha === 'esta_semana') {
                $fechaDesde = $hoy->copy()->startOfWeek()->toDateString();
                $fechaHasta = $hoy->copy()->endOfWeek()->toDateString();
            } elseif ($filtroFecha === 'semana_pasada') {
                $fechaDesde = $hoy->copy()->subWeek()->startOfWeek()->toDateString();
                $fechaHasta = $hoy->copy()->subWeek()->endOfWeek()->toDateString();
            } elseif ($filtroFecha === 'este_mes') {
                $fechaDesde = $hoy->copy()->startOfMonth()->toDateString();
                $fechaHasta = $hoy->copy()->endOfMonth()->toDateString();
            } elseif ($filtroFecha === 'mes_pasado') {
                $fechaDesde = $hoy->copy()->subMonth()->startOfMonth()->toDateString();
                $fechaHasta = $hoy->copy()->subMonth()->endOfMonth()->toDateString();
            }
        }

        // ==========================================
        // TAB 1: RESUMEN GENERAL
        // ==========================================
        $genTotalRecargado = FondoPago::sum('monto');
        $genTotalPagado = Pago::sum('monto_pagado');
        $genTotalAnticipos = Anticipo::sum('monto');
        $genSaldoCaja = $genTotalRecargado - ($genTotalPagado + $genTotalAnticipos);
        $genTrabajadoresActivos = Trabajador::where('estado', 'activo')->count();

        // Chart Data 1: Gastos por Semana (Últimas 8 semanas)
        $semanasChart = collect();
        for ($i = 7; $i >= 0; $i--) {
            $wStart = Carbon::today()->subWeeks($i)->startOfWeek()->toDateString();
            $wEnd = Carbon::today()->subWeeks($i)->endOfWeek()->toDateString();
            $label = 'Sem ' . Carbon::today()->subWeeks($i)->weekOfYear;
            
            $pSem = Pago::whereBetween('fecha', [$wStart, $wEnd])->sum('monto_pagado');
            $aSem = Anticipo::whereBetween('fecha', [$wStart, $wEnd])->sum('monto');

            $semanasChart->push([
                'label' => $label,
                'pagos' => (float)$pSem,
                'anticipos' => (float)$aSem,
                'total' => (float)($pSem + $aSem),
            ]);
        }

        // Chart Data 2: Gastos por Bocamina
        $bocaminasChart = $bocaminas->map(function($b) {
            $workersIds = Trabajador::where('bocamina_id', $b->id)->pluck('id');
            $pagosSum = Pago::whereIn('trabajador_id', $workersIds)->sum('monto_pagado');
            $anticiposSum = Anticipo::whereIn('trabajador_id', $workersIds)->sum('monto');
            return [
                'nombre' => $b->nombre,
                'total' => (float)($pagosSum + $anticiposSum),
            ];
        });

        // ==========================================
        // TAB 2: REPORTES POR TRABAJADOR
        // ==========================================
        $trabId = $request->input('trabajador_id');
        $trabRol = $request->input('rol');
        $trabContratoId = $request->input('tipo_contrato_id');
        $trabBocaminaId = $request->input('bocamina_id');

        $pagosTrabajadorQuery = Pago::with(['trabajador.bocamina', 'trabajador.tipoContrato', 'items', 'anticipos'])->orderBy('fecha', 'desc');
        $anticiposTrabajadorQuery = Anticipo::with(['trabajador.bocamina'])->orderBy('fecha', 'desc');

        if ($trabId) {
            $pagosTrabajadorQuery->where('trabajador_id', $trabId);
            $anticiposTrabajadorQuery->where('trabajador_id', $trabId);
        }
        if ($trabBocaminaId) {
            $pagosTrabajadorQuery->whereHas('trabajador', fn($q) => $q->where('bocamina_id', $trabBocaminaId));
            $anticiposTrabajadorQuery->whereHas('trabajador', fn($q) => $q->where('bocamina_id', $trabBocaminaId));
        }
        if ($trabRol) {
            $pagosTrabajadorQuery->whereHas('trabajador', fn($q) => $q->where('rol', $trabRol));
            $anticiposTrabajadorQuery->whereHas('trabajador', fn($q) => $q->where('rol', $trabRol));
        }
        if ($trabContratoId) {
            $pagosTrabajadorQuery->whereHas('trabajador', fn($q) => $q->where('tipo_contrato_id', $trabContratoId));
            $anticiposTrabajadorQuery->whereHas('trabajador', fn($q) => $q->where('tipo_contrato_id', $trabContratoId));
        }
        if ($fechaDesde) {
            $pagosTrabajadorQuery->where('fecha', '>=', $fechaDesde);
            $anticiposTrabajadorQuery->where('fecha', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $pagosTrabajadorQuery->where('fecha', '<=', $fechaHasta);
            $anticiposTrabajadorQuery->where('fecha', '<=', $fechaHasta);
        }

        $listPagosTrabajador = $pagosTrabajadorQuery->get();
        $listAnticiposTrabajador = $anticiposTrabajadorQuery->get();

        $totPagadoTrabajador = $listPagosTrabajador->sum('monto_pagado');
        $totAnticiposTrabajador = $listAnticiposTrabajador->sum('monto');
        $netoRecibidoTrabajador = $listPagosTrabajador->sum('neto');

        // ==========================================
        // TAB 3: REPORTES POR BOCAMINA
        // ==========================================
        $bocFiltroId = $request->input('boc_bocamina_id');
        $bocRol = $request->input('boc_rol');
        $bocContratoId = $request->input('boc_tipo_contrato_id');

        $bocaminasResumen = $bocaminas->map(function($b) use ($bocFiltroId, $bocRol, $bocContratoId, $fechaDesde, $fechaHasta) {
            if ($bocFiltroId && $b->id != $bocFiltroId) {
                return null;
            }

            $workersQuery = Trabajador::where('bocamina_id', $b->id)->with('tipoContrato');
            if ($bocRol) $workersQuery->where('rol', $bocRol);
            if ($bocContratoId) $workersQuery->where('tipo_contrato_id', $bocContratoId);

            $workers = $workersQuery->get();
            $wIds = $workers->pluck('id');

            $pQuery = Pago::whereIn('trabajador_id', $wIds);
            $aQuery = Anticipo::whereIn('trabajador_id', $wIds);

            if ($fechaDesde) {
                $pQuery->where('fecha', '>=', $fechaDesde);
                $aQuery->where('fecha', '>=', $fechaDesde);
            }
            if ($fechaHasta) {
                $pQuery->where('fecha', '<=', $fechaHasta);
                $aQuery->where('fecha', '<=', $fechaHasta);
            }

            $totPagos = $pQuery->sum('monto_pagado');
            $totAnticipos = $aQuery->sum('monto');
            $totGastado = $totPagos + $totAnticipos;

            $workersDetalle = $workers->map(function($w) use ($fechaDesde, $fechaHasta) {
                $wpQuery = Pago::with(['items', 'anticipos'])->where('trabajador_id', $w->id);
                $waQuery = Anticipo::where('trabajador_id', $w->id);
                if ($fechaDesde) {
                    $wpQuery->where('fecha', '>=', $fechaDesde);
                    $waQuery->where('fecha', '>=', $fechaDesde);
                }
                if ($fechaHasta) {
                    $wpQuery->where('fecha', '<=', $fechaHasta);
                    $waQuery->where('fecha', '<=', $fechaHasta);
                }

                $listaPagos = $wpQuery->orderBy('fecha', 'desc')->get();
                $listaAnticipos = $waQuery->orderBy('fecha', 'desc')->get();

                $totSubtotal = $listaPagos->sum('subtotal');
                $totBonos = $listaPagos->sum('bonos');
                $totDescuentos = $listaPagos->sum('descuentos');
                $totAnticiposDesc = $listaPagos->sum('anticipos_descontados');
                $totNeto = $listaPagos->sum('neto');
                $totPagosMonto = $listaPagos->sum('monto_pagado');
                $totSaldoPendiente = $listaPagos->sum('saldo_pendiente');
                $totAnticiposMonto = $listaAnticipos->sum('monto');
                $totAnticiposSaldo = $listaAnticipos->sum('saldo');

                // Resumen agrupado por labor/concepto de este trabajador
                $conceptosTrabajador = [];
                foreach ($listaPagos as $p) {
                    if ($p->items && $p->items->count() > 0) {
                        foreach ($p->items as $it) {
                            $tipo = $it->tipo_trabajo ?: ($p->tipo_contrato_nombre ?: 'Labor minera');
                            if (!isset($conceptosTrabajador[$tipo])) {
                                $conceptosTrabajador[$tipo] = ['cantidad' => 0, 'subtotal' => 0, 'tarifa' => (float)$it->precio_unitario];
                            }
                            $conceptosTrabajador[$tipo]['cantidad'] += (float)$it->cantidad;
                            $conceptosTrabajador[$tipo]['subtotal'] += (float)$it->subtotal;
                        }
                    } else {
                        $tipo = $p->tipo_contrato_nombre ?: ($w->tipoContrato->nombre ?? 'Labor minera');
                        if (!isset($conceptosTrabajador[$tipo])) {
                            $conceptosTrabajador[$tipo] = ['cantidad' => 0, 'subtotal' => 0, 'tarifa' => (float)$p->tarifa_pago];
                        }
                        $conceptosTrabajador[$tipo]['cantidad'] += (float)$p->cantidad_trabajada;
                        $conceptosTrabajador[$tipo]['subtotal'] += (float)$p->subtotal;
                    }
                }

                return [
                    'trabajador' => $w,
                    'pagos_lista' => $listaPagos,
                    'anticipos_lista' => $listaAnticipos,
                    'conceptos_resumen' => $conceptosTrabajador,
                    'total_subtotal' => $totSubtotal,
                    'total_bonos' => $totBonos,
                    'total_descuentos' => $totDescuentos,
                    'total_anticipos_desc' => $totAnticiposDesc,
                    'total_neto' => $totNeto,
                    'total_saldo_pendiente' => $totSaldoPendiente,
                    'pagos' => $totPagosMonto,
                    'anticipos' => $totAnticiposMonto,
                    'anticipos_saldo' => $totAnticiposSaldo,
                    'total' => $totPagosMonto + $totAnticiposMonto,
                ];
            });

            // Resumen de conceptos agrupados a nivel de toda la bocamina
            $conceptosBocamina = [];
            foreach ($workersDetalle as $wd) {
                foreach ($wd['conceptos_resumen'] as $tipo => $cData) {
                    if (!isset($conceptosBocamina[$tipo])) {
                        $conceptosBocamina[$tipo] = ['cantidad' => 0, 'subtotal' => 0, 'tarifa' => $cData['tarifa']];
                    }
                    $conceptosBocamina[$tipo]['cantidad'] += $cData['cantidad'];
                    $conceptosBocamina[$tipo]['subtotal'] += $cData['subtotal'];
                }
            }

            $totSubtotalBoc = $workersDetalle->sum('total_subtotal');
            $totBonosBoc = $workersDetalle->sum('total_bonos');
            $totDescuentosBoc = $workersDetalle->sum('total_descuentos');
            $totAnticiposDescBoc = $workersDetalle->sum('total_anticipos_desc');
            $totNetoBoc = $workersDetalle->sum('total_neto');

            return [
                'bocamina' => $b,
                'cant_trabajadores' => $workers->count(),
                'total_pagos' => $totPagos,
                'total_anticipos' => $totAnticipos,
                'total_gastado' => $totGastado,
                'total_subtotal' => $totSubtotalBoc,
                'total_bonos' => $totBonosBoc,
                'total_descuentos' => $totDescuentosBoc,
                'total_anticipos_desc' => $totAnticiposDescBoc,
                'total_neto' => $totNetoBoc,
                'conceptos_resumen' => $conceptosBocamina,
                'trabajadores_detalle' => $workersDetalle,
            ];
        })->filter()->values();

        // ==========================================
        // TAB 4: REPORTES DE ANTICIPOS
        // ==========================================
        $antTrabId = $request->input('ant_trabajador_id');
        $antRol = $request->input('ant_rol');
        $antContratoId = $request->input('ant_tipo_contrato_id');
        $antBocaminaId = $request->input('ant_bocamina_id');
        $antEstado = $request->input('ant_estado', 'todos');

        $anticiposTabQuery = Anticipo::with(['trabajador.bocamina', 'trabajador.tipoContrato'])->orderBy('fecha', 'desc');

        if ($antTrabId) $anticiposTabQuery->where('trabajador_id', $antTrabId);
        if ($antBocaminaId) $anticiposTabQuery->whereHas('trabajador', fn($q) => $q->where('bocamina_id', $antBocaminaId));
        if ($antRol) $anticiposTabQuery->whereHas('trabajador', fn($q) => $q->where('rol', $antRol));
        if ($antContratoId) $anticiposTabQuery->whereHas('trabajador', fn($q) => $q->where('tipo_contrato_id', $antContratoId));
        if ($fechaDesde) $anticiposTabQuery->where('fecha', '>=', $fechaDesde);
        if ($fechaHasta) $anticiposTabQuery->where('fecha', '<=', $fechaHasta);

        if ($antEstado === 'pendiente') {
            $anticiposTabQuery->where('saldo', '>', 0);
        } elseif ($antEstado === 'descontado') {
            $anticiposTabQuery->where('saldo', '<=', 0);
        }

        $listAnticiposTab = $anticiposTabQuery->get();

        $antConteo = $listAnticiposTab->count();
        $antMontoTotal = $listAnticiposTab->sum('monto');
        $antMontoPendiente = $listAnticiposTab->sum('saldo');
        $antMontoDescontado = $listAnticiposTab->sum(fn($a) => $a->monto - $a->saldo);

        return view('reportes.index', compact(
            'tab',
            'trabajadores',
            'allTrabajadores',
            'bocaminas',
            'tiposContrato',
            'roles',
            'filtroFecha',
            'fechaDesde',
            'fechaHasta',

            // Tab 1 Data
            'genTotalRecargado',
            'genTotalPagado',
            'genTotalAnticipos',
            'genSaldoCaja',
            'genTrabajadoresActivos',
            'semanasChart',
            'bocaminasChart',

            // Tab 2 Data
            'trabId',
            'trabRol',
            'trabContratoId',
            'trabBocaminaId',
            'listPagosTrabajador',
            'listAnticiposTrabajador',
            'totPagadoTrabajador',
            'totAnticiposTrabajador',
            'netoRecibidoTrabajador',

            // Tab 3 Data
            'bocFiltroId',
            'bocRol',
            'bocContratoId',
            'bocaminasResumen',

            // Tab 4 Data
            'antTrabId',
            'antRol',
            'antContratoId',
            'antBocaminaId',
            'antEstado',
            'listAnticiposTab',
            'antConteo',
            'antMontoTotal',
            'antMontoPendiente',
            'antMontoDescontado'
        ));
    }

    public function comercializacion(Request $request)
    {
        $bocaminas = Bocamina::orderBy('nombre', 'asc')->get();

        $filtroFecha = $request->input('filtro_fecha', 'personalizado');
        $fechaDesde  = $request->input('fecha_desde');
        $fechaHasta  = $request->input('fecha_hasta');

        if ($request->filled('filtro_fecha') && $filtroFecha !== 'personalizado') {
            $hoy = Carbon::today();
            if ($filtroFecha === 'esta_semana') {
                $fechaDesde = $hoy->copy()->startOfWeek()->toDateString();
                $fechaHasta = $hoy->copy()->endOfWeek()->toDateString();
            } elseif ($filtroFecha === 'semana_pasada') {
                $fechaDesde = $hoy->copy()->subWeek()->startOfWeek()->toDateString();
                $fechaHasta = $hoy->copy()->endOfWeek()->toDateString();
            } elseif ($filtroFecha === 'este_mes') {
                $fechaDesde = $hoy->copy()->startOfMonth()->toDateString();
                $fechaHasta = $hoy->copy()->endOfMonth()->toDateString();
            } elseif ($filtroFecha === 'mes_pasado') {
                $fechaDesde = $hoy->copy()->subMonth()->startOfMonth()->toDateString();
                $fechaHasta = $hoy->copy()->subMonth()->endOfMonth()->toDateString();
            }
        }

        $query = \App\Models\TransaccionMineral::with(['bocamina', 'analisis', 'lote.analisis'])->orderBy('fecha', 'desc')->orderBy('id', 'desc');

        if ($fechaDesde) $query->where('fecha', '>=', $fechaDesde);
        if ($fechaHasta) $query->where('fecha', '<=', $fechaHasta);

        $tipoFiltro = $request->input('tipo', 'todos');
        if ($tipoFiltro === 'venta')  $query->where('tipo', 'venta');
        if ($tipoFiltro === 'compra') $query->where('tipo', 'compra');

        $bocaminaFiltro = $request->input('bocamina_id');
        if ($bocaminaFiltro) $query->where('bocamina_id', $bocaminaFiltro);

        $totalsQuery = clone $query;
        $totalVentas  = (float)((clone $totalsQuery)->where('tipo', 'venta')->sum('monto_total') ?: 0);
        $totalCompras = (float)((clone $totalsQuery)->where('tipo', 'compra')->sum('monto_total') ?: 0);
        $totalPesoNetoVentas  = (float)((clone $totalsQuery)->where('tipo', 'venta')->sum('peso_neto_seco') ?: 0);
        $totalPesoNetoCompras = (float)((clone $totalsQuery)->where('tipo', 'compra')->sum('peso_neto_seco') ?: 0);
        $totalCountVentas = (int)((clone $totalsQuery)->where('tipo', 'venta')->count());
        $totalCountCompras = (int)((clone $totalsQuery)->where('tipo', 'compra')->count());

        $transacciones = $query->paginate(25)->withQueryString();

        $resumenBocaminas = $bocaminas->map(function ($b) use ($fechaDesde, $fechaHasta, $tipoFiltro) {
            $bQuery = \App\Models\TransaccionMineral::where('bocamina_id', $b->id);
            if ($fechaDesde) $bQuery->where('fecha', '>=', $fechaDesde);
            if ($fechaHasta) $bQuery->where('fecha', '<=', $fechaHasta);
            if ($tipoFiltro === 'venta')  $bQuery->where('tipo', 'venta');
            if ($tipoFiltro === 'compra') $bQuery->where('tipo', 'compra');

            $cnt = (clone $bQuery)->count();
            if ($cnt === 0) return null;

            return [
                'bocamina'        => $b,
                'total_ventas'    => (float)((clone $bQuery)->where('tipo', 'venta')->sum('monto_total') ?: 0),
                'total_compras'   => (float)((clone $bQuery)->where('tipo', 'compra')->sum('monto_total') ?: 0),
                'peso_neto_ventas' => (float)((clone $bQuery)->where('tipo', 'venta')->sum('peso_neto_seco') ?: 0),
                'cantidad'        => $cnt,
            ];
        })->filter()->values();

        $meses = collect();
        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::today()->subMonths($i);
            $mesLabel = $mes->locale('es')->isoFormat('MMM Y');
            $mesInicio = $mes->copy()->startOfMonth()->toDateString();
            $mesFin    = $mes->copy()->endOfMonth()->toDateString();

            $ventasMes  = \App\Models\TransaccionMineral::where('tipo', 'venta')
                ->whereBetween('fecha', [$mesInicio, $mesFin])->sum('monto_total');
            $comprasMes = \App\Models\TransaccionMineral::where('tipo', 'compra')
                ->whereBetween('fecha', [$mesInicio, $mesFin])->sum('monto_total');

            $meses->push(['label' => $mesLabel, 'ventas' => $ventasMes, 'compras' => $comprasMes]);
        }

        return view('reportes.comercializacion', compact(
            'bocaminas',
            'transacciones',
            'totalVentas',
            'totalCompras',
            'totalPesoNetoVentas',
            'totalPesoNetoCompras',
            'totalCountVentas',
            'totalCountCompras',
            'resumenBocaminas',
            'filtroFecha',
            'fechaDesde',
            'fechaHasta',
            'tipoFiltro',
            'bocaminaFiltro',
            'meses'
        ));
    }

    public function exportarExcel(Request $request)
    {
        $filtroFecha = $request->input('filtro_fecha', 'personalizado');
        $fechaDesde  = $request->input('fecha_desde');
        $fechaHasta  = $request->input('fecha_hasta');

        if ($request->filled('filtro_fecha') && $filtroFecha !== 'personalizado') {
            $hoy = Carbon::today();
            if ($filtroFecha === 'esta_semana') {
                $fechaDesde = $hoy->copy()->startOfWeek()->toDateString();
                $fechaHasta = $hoy->copy()->endOfWeek()->toDateString();
            } elseif ($filtroFecha === 'semana_pasada') {
                $fechaDesde = $hoy->copy()->subWeek()->startOfWeek()->toDateString();
                $fechaHasta = $hoy->copy()->endOfWeek()->toDateString();
            } elseif ($filtroFecha === 'este_mes') {
                $fechaDesde = $hoy->copy()->startOfMonth()->toDateString();
                $fechaHasta = $hoy->copy()->endOfMonth()->toDateString();
            } elseif ($filtroFecha === 'mes_pasado') {
                $fechaDesde = $hoy->copy()->subMonth()->startOfMonth()->toDateString();
                $fechaHasta = $hoy->copy()->subMonth()->endOfMonth()->toDateString();
            }
        }

        $query = \App\Models\TransaccionMineral::with(['bocamina', 'analisis', 'lote.analisis'])->orderBy('fecha', 'desc')->orderBy('id', 'desc');

        if ($fechaDesde) $query->where('fecha', '>=', $fechaDesde);
        if ($fechaHasta) $query->where('fecha', '<=', $fechaHasta);

        $tipoFiltro = $request->input('tipo', 'todos');
        if ($tipoFiltro === 'venta')  $query->where('tipo', 'venta');
        if ($tipoFiltro === 'compra') $query->where('tipo', 'compra');

        $bocaminaFiltro = $request->input('bocamina_id');
        if ($bocaminaFiltro) $query->where('bocamina_id', $bocaminaFiltro);

        $rows = $query->get();

        $totalCompras = (float) $rows->where('tipo', 'compra')->sum('monto_total');
        $totalVentas  = (float) $rows->where('tipo', 'venta')->sum('monto_total');
        $balanceNeto  = $totalVentas - $totalCompras;
        $pesoCompras  = (float) $rows->where('tipo', 'compra')->sum('peso_neto_seco');
        $pesoVentas   = (float) $rows->where('tipo', 'venta')->sum('peso_neto_seco');
        $totalCant    = (float) $rows->sum('cantidad');
        $totalPeso    = (float) $rows->sum('peso_neto_seco');
        $totalMonto   = (float) $rows->sum('monto_total');

        $filename = 'Reporte_Comercializacion_KOYOSITA_' . date('Y-m-d_H-i') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        return response()->stream(function() use ($rows, $totalCompras, $totalVentas, $balanceNeto, $pesoCompras, $pesoVentas, $totalCant, $totalPeso, $totalMonto) {
            echo "\xEF\xBB\xBF";
            $fechaGen = date('d/m/Y H:i:s');
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<style>
                body, table { font-family: Calibri, Arial, sans-serif; font-size: 10pt; color: #0f172a; }
                table { border-collapse: collapse; width: 100%; }
                .title { background-color: #0f172a; color: #ffffff; font-size: 14pt; font-weight: bold; padding: 10px 12px; text-align: left; }
                .subtitle { background-color: #1e293b; color: #f59e0b; font-size: 10pt; font-weight: bold; padding: 6px 12px; text-align: left; }
                .kpi-label { background-color: #f1f5f9; color: #475569; font-weight: bold; font-size: 9pt; border: 1px solid #cbd5e1; padding: 6px 8px; text-transform: uppercase; }
                .kpi-val { background-color: #ffffff; color: #0f172a; font-weight: bold; font-size: 11pt; border: 1px solid #cbd5e1; padding: 6px 8px; text-align: right; }
                th.col-hdr { background-color: #0f172a; color: #ffffff; font-weight: bold; font-size: 9.5pt; text-transform: uppercase; border: 1px solid #334155; padding: 8px 10px; text-align: center; }
                td.cell { border: 1px solid #cbd5e1; padding: 6px 8px; vertical-align: middle; }
                td.cell-even { background-color: #f8fafc; border: 1px solid #cbd5e1; padding: 6px 8px; vertical-align: middle; }
                .num { mso-number-format:"\#\,\#\#0\.00"; text-align: right; font-family: Consolas, monospace; }
                .center { text-align: center; }
                .compra { color: #b45309; font-weight: bold; }
                .venta { color: #047857; font-weight: bold; }
                .tfoot { background-color: #0f172a; color: #ffffff; font-weight: bold; font-size: 10pt; border: 1px solid #0f172a; padding: 8px 10px; }
                .tfoot-num { background-color: #0f172a; color: #fbbf24; font-weight: bold; font-size: 10pt; border: 1px solid #0f172a; padding: 8px 10px; mso-number-format:"\#\,\#\#0\.00"; text-align: right; }
            </style></head><body>';

            echo '<table>';
            echo '<tr><td colspan="14" class="title">EMPRESA MINERA KOYOSITA S.R.L. — REPORTE DE COMERCIALIZACIÓN DE MINERALES</td></tr>';
            echo '<tr><td colspan="14" class="subtitle">Generado el ' . $fechaGen . ' · Total de Registros: ' . count($rows) . '</td></tr>';
            echo '<tr><td colspan="14"></td></tr>';

            echo '<tr>';
            echo '<td colspan="2" class="kpi-label">Total Compras (Bs.)</td><td colspan="2" class="kpi-val num">' . number_format($totalCompras, 2, '.', '') . '</td>';
            echo '<td colspan="2" class="kpi-label">Total Ventas (Bs.)</td><td colspan="2" class="kpi-val num">' . number_format($totalVentas, 2, '.', '') . '</td>';
            echo '<td colspan="2" class="kpi-label">Balance Neto (Bs.)</td><td colspan="2" class="kpi-val num">' . number_format($balanceNeto, 2, '.', '') . '</td>';
            echo '<td class="kpi-label">Peso Compras / Ventas (Kg)</td><td class="kpi-val">' . number_format($pesoCompras, 2) . ' / ' . number_format($pesoVentas, 2) . '</td>';
            echo '</tr>';
            echo '<tr><td colspan="14"></td></tr>';

            echo '<thead><tr>';
            echo '<th class="col-hdr">Código / Lote</th>';
            echo '<th class="col-hdr">Fecha</th>';
            echo '<th class="col-hdr">Tipo</th>';
            echo '<th class="col-hdr">Cliente / Proveedor</th>';
            echo '<th class="col-hdr">Bocamina</th>';
            echo '<th class="col-hdr">Presentación</th>';
            echo '<th class="col-hdr">Leyes de Laboratorio (%)</th>';
            echo '<th class="col-hdr">Cantidad</th>';
            echo '<th class="col-hdr">Peso Bruto (Kg)</th>';
            echo '<th class="col-hdr">Humedad (%)</th>';
            echo '<th class="col-hdr">Peso Neto Seco (Kg)</th>';
            echo '<th class="col-hdr">Peso Neto (TN)</th>';
            echo '<th class="col-hdr">Precio Unit. (Bs/Kg)</th>';
            echo '<th class="col-hdr">Monto Total (Bs.)</th>';
            echo '</tr></thead><tbody>';

            foreach ($rows as $idx => $row) {
                $cls = ($idx % 2 === 1) ? 'cell-even' : 'cell';
                $codigo = $row->tipo === 'compra' ? 'LOT-' . str_pad($row->id, 5, '0', STR_PAD_LEFT) : 'SLD-' . str_pad($row->id, 5, '0', STR_PAD_LEFT);
                $fecha = $row->fecha ? $row->fecha->format('d/m/Y') : '';
                $tipoTxt = strtoupper($row->tipo);
                $tipoCls = $row->tipo === 'compra' ? 'compra' : 'venta';
                $prov = e($row->cliente_proveedor ?? '—');
                $boc = e($row->bocamina->nombre ?? 'Sin asignar');
                $pres = e($row->presentacion === 'Otro' ? ($row->presentacion_otro ?: 'Otro') : ($row->presentacion ?? '—'));

                $analisisList = ($row->tipo === 'compra' || ($row->analisis && $row->analisis->count() > 0))
                    ? $row->analisis
                    : ($row->lote ? $row->lote->analisis : collect());
                $leyesArr = [];
                foreach ($analisisList as $an) {
                    $leyesArr[] = e($an->mineral) . ': ' . number_format((float)$an->ley, 2) . '%';
                }
                $leyesStr = count($leyesArr) > 0 ? implode(' | ', $leyesArr) : '—';

                $pesoBruto = (float)($row->peso_bruto ?: $row->peso_neto_seco);
                $humedad = (float)($row->humedad_porcentaje ?? 0);
                $pesoNetoKg = (float)$row->peso_neto_seco;
                $pesoNetoTn = $pesoNetoKg / 1000.0;

                echo '<tr>';
                echo '<td class="' . $cls . ' center" style="font-weight:bold;">' . $codigo . '</td>';
                echo '<td class="' . $cls . ' center">' . $fecha . '</td>';
                echo '<td class="' . $cls . ' center ' . $tipoCls . '">' . $tipoTxt . '</td>';
                echo '<td class="' . $cls . '">' . $prov . '</td>';
                echo '<td class="' . $cls . '">' . $boc . '</td>';
                echo '<td class="' . $cls . ' center">' . $pres . '</td>';
                echo '<td class="' . $cls . '">' . $leyesStr . '</td>';
                echo '<td class="' . $cls . ' num">' . number_format((float)$row->cantidad, 2, '.', '') . '</td>';
                echo '<td class="' . $cls . ' num">' . number_format($pesoBruto, 2, '.', '') . '</td>';
                echo '<td class="' . $cls . ' num">' . number_format($humedad, 2, '.', '') . '</td>';
                echo '<td class="' . $cls . ' num">' . number_format($pesoNetoKg, 2, '.', '') . '</td>';
                echo '<td class="' . $cls . ' num">' . number_format($pesoNetoTn, 3, '.', '') . '</td>';
                echo '<td class="' . $cls . ' num">' . number_format((float)$row->precio_unidad, 2, '.', '') . '</td>';
                echo '<td class="' . $cls . ' num" style="font-weight:bold;">' . number_format((float)$row->monto_total, 2, '.', '') . '</td>';
                echo '</tr>';
            }

            echo '</tbody><tfoot><tr>';
            echo '<td colspan="7" class="tfoot" style="text-align:right;">TOTALES DEL PERÍODO FILTRADO:</td>';
            echo '<td class="tfoot-num">' . number_format($totalCant, 2, '.', '') . '</td>';
            echo '<td class="tfoot"></td>';
            echo '<td class="tfoot"></td>';
            echo '<td class="tfoot-num">' . number_format($totalPeso, 2, '.', '') . '</td>';
            echo '<td class="tfoot-num">' . number_format($totalPeso / 1000.0, 3, '.', '') . '</td>';
            echo '<td class="tfoot"></td>';
            echo '<td class="tfoot-num">' . number_format($totalMonto, 2, '.', '') . '</td>';
            echo '</tr></tfoot></table></body></html>';
        }, 200, $headers);
    }
}

