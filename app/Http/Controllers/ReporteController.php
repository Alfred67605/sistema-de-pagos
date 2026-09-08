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
            
        $recientesAnticipos = Anticipo::with('trabajador')->orderBy('fecha', 'desc')->take(5)->get();
        $recientesPagos = Pago::with('trabajador')->orderBy('fecha', 'desc')->take(5)->get();
        $recientesTransaccionesMineral = \App\Models\TransaccionMineral::with('bocamina')
            ->orderBy('fecha', 'desc')
            ->take(5)
            ->get();

        // Chart data: Production (subtotal of pagos) by Bocamina
        $produccionBocaminas = Bocamina::with(['trabajadores.pagos'])
            ->get()
            ->map(function($bocamina) {
                $total = 0;
                foreach ($bocamina->trabajadores as $trabajador) {
                    $total += $trabajador->pagos->sum('subtotal');
                }
                return [
                    'nombre' => $bocamina->nombre,
                    'total' => $total
                ];
            });

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

        $pagosTrabajadorQuery = Pago::with(['trabajador.bocamina', 'trabajador.tipoContrato'])->orderBy('fecha', 'desc');
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
                $wpQuery = Pago::where('trabajador_id', $w->id);
                $waQuery = Anticipo::where('trabajador_id', $w->id);
                if ($fechaDesde) {
                    $wpQuery->where('fecha', '>=', $fechaDesde);
                    $waQuery->where('fecha', '>=', $fechaDesde);
                }
                if ($fechaHasta) {
                    $wpQuery->where('fecha', '<=', $fechaHasta);
                    $waQuery->where('fecha', '<=', $fechaHasta);
                }
                return [
                    'trabajador' => $w,
                    'pagos' => $wpQuery->sum('monto_pagado'),
                    'anticipos' => $waQuery->sum('monto'),
                    'total' => $wpQuery->sum('monto_pagado') + $waQuery->sum('monto'),
                ];
            });

            return [
                'bocamina' => $b,
                'cant_trabajadores' => $workers->count(),
                'total_pagos' => $totPagos,
                'total_anticipos' => $totAnticipos,
                'total_gastado' => $totGastado,
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

        $query = \App\Models\TransaccionMineral::with('bocamina')->orderBy('fecha', 'desc');

        if ($fechaDesde) $query->where('fecha', '>=', $fechaDesde);
        if ($fechaHasta) $query->where('fecha', '<=', $fechaHasta);

        $tipoFiltro = $request->input('tipo', 'todos');
        if ($tipoFiltro === 'venta')  $query->where('tipo', 'venta');
        if ($tipoFiltro === 'compra') $query->where('tipo', 'compra');

        $bocaminaFiltro = $request->input('bocamina_id');
        if ($bocaminaFiltro) $query->where('bocamina_id', $bocaminaFiltro);

        $transacciones = $query->get();

        $totalVentas  = $transacciones->where('tipo', 'venta')->sum('monto_total');
        $totalCompras = $transacciones->where('tipo', 'compra')->sum('monto_total');
        $totalPesoNetoVentas  = $transacciones->where('tipo', 'venta')->sum('peso_neto_seco');
        $totalPesoNetoCompras = $transacciones->where('tipo', 'compra')->sum('peso_neto_seco');

        $resumenBocaminas = $bocaminas->map(function ($b) use ($transacciones) {
            $bTx = $transacciones->where('bocamina_id', $b->id);
            return [
                'bocamina'        => $b,
                'total_ventas'    => $bTx->where('tipo', 'venta')->sum('monto_total'),
                'total_compras'   => $bTx->where('tipo', 'compra')->sum('monto_total'),
                'peso_neto_ventas' => $bTx->where('tipo', 'venta')->sum('peso_neto_seco'),
                'cantidad'        => $bTx->count(),
            ];
        })->filter(fn($r) => $r['cantidad'] > 0);

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
            'resumenBocaminas',
            'filtroFecha',
            'fechaDesde',
            'fechaHasta',
            'tipoFiltro',
            'bocaminaFiltro',
            'meses'
        ));
    }
}
