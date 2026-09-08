@extends('layouts.app')

@section('title', 'Reportes de Comercialización')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0 no-print">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100">Reportes de Comercialización</h1>
            <p class="text-sm text-slate-400 mt-1">Consulta, filtra e imprime reportes de compra y venta de mineral por bocamina y período.</p>
        </div>
        <div class="flex flex-wrap gap-2.5 no-print">
            <button onclick="doExportExcel()" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-md transition">
                <i class="fa-solid fa-file-excel mr-2"></i> Excel
            </button>
            <button onclick="doExportPDF()" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-bold bg-rose-600 hover:bg-rose-500 text-white shadow-md transition">
                <i class="fa-solid fa-file-pdf mr-2"></i> PDF
            </button>
            <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-bold bg-amber-600 hover:bg-amber-500 text-white shadow-md transition">
                <i class="fa-solid fa-print mr-2"></i> Imprimir
            </button>
        </div>
    </div>

    {{-- Print Header --}}
    <div class="hidden print-only mb-6 text-slate-900">
        <div class="text-center">
            <h1 class="text-2xl font-bold uppercase tracking-wider">Reporte de Comercialización de Mineral</h1>
            <p class="text-sm font-mono mt-1">Generado el {{ now()->format('d/m/Y H:i') }}</p>
        </div>
        <hr class="border-slate-300 my-4">
    </div>

    {{-- Filters --}}
    <div class="bg-slate-900/40 border border-slate-800/60 rounded-2xl p-5 no-print">
        <form method="GET" action="{{ route('reportes-comercializacion.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
            {{-- Quick date filter --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Período</label>
                <select name="filtro_fecha" onchange="this.form.submit()"
                    class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-orange-500/60">
                    <option value="personalizado" {{ $filtroFecha === 'personalizado' ? 'selected' : '' }}>Personalizado</option>
                    <option value="esta_semana"   {{ $filtroFecha === 'esta_semana'   ? 'selected' : '' }}>Esta Semana</option>
                    <option value="semana_pasada" {{ $filtroFecha === 'semana_pasada' ? 'selected' : '' }}>Semana Pasada</option>
                    <option value="este_mes"      {{ $filtroFecha === 'este_mes'      ? 'selected' : '' }}>Este Mes</option>
                    <option value="mes_pasado"    {{ $filtroFecha === 'mes_pasado'    ? 'selected' : '' }}>Mes Pasado</option>
                </select>
            </div>

            {{-- Desde --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Desde</label>
                <input type="date" name="fecha_desde" value="{{ $fechaDesde ?? '' }}"
                    class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-orange-500/60">
            </div>

            {{-- Hasta --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ $fechaHasta ?? '' }}"
                    class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-orange-500/60">
            </div>

            {{-- Tipo --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Tipo</label>
                <select name="tipo"
                    class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-orange-500/60">
                    <option value="todos"  {{ ($tipoFiltro ?? 'todos') === 'todos'  ? 'selected' : '' }}>Todos</option>
                    <option value="venta"  {{ ($tipoFiltro ?? '') === 'venta'  ? 'selected' : '' }}>Solo Ventas</option>
                    <option value="compra" {{ ($tipoFiltro ?? '') === 'compra' ? 'selected' : '' }}>Solo Compras</option>
                </select>
            </div>

            {{-- Bocamina --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5">Bocamina</label>
                <select name="bocamina_id"
                    class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-sm text-slate-200 focus:outline-none focus:border-orange-500/60">
                    <option value="">Todas</option>
                    @foreach($bocaminas as $b)
                        <option value="{{ $b->id }}" {{ ($bocaminaFiltro ?? '') == $b->id ? 'selected' : '' }}>{{ $b->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-5 flex gap-3">
                <button type="submit" class="flex-1 md:flex-none px-5 py-2 rounded-xl bg-orange-500 hover:bg-orange-400 text-white text-sm font-bold transition shadow-lg">
                    <i class="fa-solid fa-magnifying-glass mr-2"></i>Buscar
                </button>
                <a href="{{ route('reportes-comercializacion.index') }}" class="px-5 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 text-sm font-bold transition">
                    <i class="fa-solid fa-rotate-left mr-2"></i>Limpiar
                </a>
            </div>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Ventas --}}
        <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/20">
            <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-6 -left-4 w-20 h-20 rounded-full bg-white/5"></div>
            <div class="relative z-10">
                <p class="text-xs font-bold uppercase tracking-widest text-emerald-100/80 mb-1">Ingresos (Ventas)</p>
                <p class="text-2xl font-black text-white">Bs {{ number_format($totalVentas, 2) }}</p>
                <p class="text-xs text-emerald-100/70 mt-1">{{ $transacciones->where('tipo','venta')->count() }} transacciones</p>
            </div>
            <div class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                <i class="fa-solid fa-arrow-trend-up text-white text-base"></i>
            </div>
        </div>

        {{-- Total Compras --}}
        <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-rose-500 to-red-600 shadow-lg shadow-rose-500/20">
            <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-6 -left-4 w-20 h-20 rounded-full bg-white/5"></div>
            <div class="relative z-10">
                <p class="text-xs font-bold uppercase tracking-widest text-rose-100/80 mb-1">Egresos (Compras)</p>
                <p class="text-2xl font-black text-white">Bs {{ number_format($totalCompras, 2) }}</p>
                <p class="text-xs text-rose-100/70 mt-1">{{ $transacciones->where('tipo','compra')->count() }} transacciones</p>
            </div>
            <div class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                <i class="fa-solid fa-arrow-trend-down text-white text-base"></i>
            </div>
        </div>

        {{-- Balance --}}
        @php $balance = $totalVentas - $totalCompras; @endphp
        <div class="relative overflow-hidden rounded-2xl p-5 {{ $balance >= 0 ? 'bg-gradient-to-br from-sky-500 to-blue-600 shadow-sky-500/20' : 'bg-gradient-to-br from-amber-500 to-orange-600 shadow-amber-500/20' }} shadow-lg">
            <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="relative z-10">
                <p class="text-xs font-bold uppercase tracking-widest text-white/80 mb-1">Balance Neto</p>
                <p class="text-2xl font-black text-white">Bs {{ number_format(abs($balance), 2) }}</p>
                <p class="text-xs text-white/70 mt-1">{{ $balance >= 0 ? 'Superávit' : 'Déficit' }}</p>
            </div>
            <div class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                <i class="fa-solid fa-scale-balanced text-white text-base"></i>
            </div>
        </div>

        {{-- Total Peso --}}
        <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg shadow-violet-500/20">
            <div class="absolute -top-4 -right-4 w-24 h-24 rounded-full bg-white/10"></div>
            <div class="relative z-10">
                <p class="text-xs font-bold uppercase tracking-widest text-violet-100/80 mb-1">Peso Neto Vendido</p>
                <p class="text-2xl font-black text-white">{{ number_format($totalPesoNetoVentas, 2) }} TN</p>
                <p class="text-xs text-violet-100/70 mt-1">Total mineral comercializado</p>
            </div>
            <div class="absolute top-4 right-4 w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center">
                <i class="fa-solid fa-weight-scale text-white text-base"></i>
            </div>
        </div>
    </div>

    {{-- Charts + Per Bocamina Summary --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Bar Chart: Monthly Ventas vs Compras --}}
        <div class="lg:col-span-2 bg-slate-900/50 border border-slate-800/60 rounded-2xl p-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 mb-4">
                <i class="fa-solid fa-chart-bar mr-2 text-orange-500"></i>Tendencia Mensual — Últimos 6 Meses
            </h3>
            <div class="relative h-52">
                <canvas id="comercChart"></canvas>
            </div>
        </div>

        {{-- Per-bocamina summary --}}
        <div class="bg-slate-900/50 border border-slate-800/60 rounded-2xl p-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300 mb-4">
                <i class="fa-solid fa-mountain mr-2 text-emerald-500"></i>Resumen por Bocamina
            </h3>
            @if($resumenBocaminas->isEmpty())
                <p class="text-slate-500 text-sm text-center py-8">Sin datos para el período.</p>
            @else
                <div class="space-y-3">
                    @foreach($resumenBocaminas as $r)
                    <div class="bg-slate-800/50 rounded-xl p-3">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-slate-200">{{ $r['bocamina']->nombre }}</span>
                            <span class="text-[10px] text-slate-500">{{ $r['cantidad'] }} reg.</span>
                        </div>
                        <div class="flex justify-between text-xs mt-1">
                            <span class="text-emerald-400">↑ Bs {{ number_format($r['total_ventas'], 0) }}</span>
                            <span class="text-rose-400">↓ Bs {{ number_format($r['total_compras'], 0) }}</span>
                        </div>
                        <div class="mt-2 h-1 bg-slate-700 rounded-full overflow-hidden">
                            @php
                                $total = $r['total_ventas'] + $r['total_compras'];
                                $pct   = $total > 0 ? round(($r['total_ventas'] / $total) * 100) : 0;
                            @endphp
                            <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Transaction Log --}}
    <div class="bg-slate-900/50 border border-slate-800/60 rounded-2xl overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800/60">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-300">
                <i class="fa-solid fa-list-check mr-2 text-orange-500"></i>
                Registro de Transacciones
                <span class="ml-2 text-[10px] font-normal text-slate-500 normal-case">({{ $transacciones->count() }} registros)</span>
            </h3>
        </div>

        @if($transacciones->isEmpty())
            <div class="text-center py-16 text-slate-500">
                <i class="fa-solid fa-inbox text-4xl mb-3 block"></i>
                No hay transacciones para los filtros seleccionados.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-5 py-3">Fecha</th>
                            <th class="px-5 py-3">Tipo</th>
                            <th class="px-5 py-3">Bocamina</th>
                            <th class="px-5 py-3">Mineral / Presentación</th>
                            <th class="px-5 py-3 text-right">Peso Neto (TN)</th>
                            <th class="px-5 py-3 text-right">Monto Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @foreach($transacciones as $tx)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-5 py-3 text-slate-300 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($tx->fecha)->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3">
                                @if($tx->tipo === 'venta')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/25">
                                        <i class="fa-solid fa-arrow-up text-[8px]"></i> Venta
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-400 border border-rose-500/25">
                                        <i class="fa-solid fa-arrow-down text-[8px]"></i> Compra
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-slate-300">{{ $tx->bocamina->nombre ?? '—' }}</td>
                            <td class="px-5 py-3 text-slate-300">
                                {{ $tx->tipo_mineral ?? '—' }}
                                @if($tx->presentacion ?? null)
                                    <span class="text-slate-500 text-xs">/ {{ $tx->presentacion }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right text-slate-200 font-mono">
                                {{ $tx->peso_neto_seco ? number_format($tx->peso_neto_seco, 2) : '—' }}
                            </td>
                            <td class="px-5 py-3 text-right font-bold {{ $tx->tipo === 'venta' ? 'text-emerald-400' : 'text-rose-400' }} font-mono">
                                Bs {{ number_format($tx->monto_total, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-slate-700 bg-slate-900/60">
                        <tr>
                            <td colspan="4" class="px-5 py-3 text-xs font-bold uppercase tracking-wider text-slate-400">Totales del Período</td>
                            <td class="px-5 py-3 text-right text-sm font-bold text-slate-200 font-mono">
                                {{ number_format($totalPesoNetoVentas, 2) }} TN
                            </td>
                            <td class="px-5 py-3 text-right text-sm font-bold font-mono">
                                <span class="text-emerald-400">Bs {{ number_format($totalVentas, 2) }}</span>
                                <span class="text-slate-500 mx-1">/</span>
                                <span class="text-rose-400">Bs {{ number_format($totalCompras, 2) }}</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const meses   = @json($meses->pluck('label'));
    const ventas  = @json($meses->pluck('ventas'));
    const compras = @json($meses->pluck('compras'));

    function isLight() { return document.documentElement.classList.contains('light-theme'); }

    function getColors() {
        return {
            grid: isLight() ? 'rgba(15,23,42,0.07)' : 'rgba(255,255,255,0.05)',
            tick: isLight() ? '#475569' : '#94a3b8',
        };
    }

    const ctx = document.getElementById('comercChart');
    if (!ctx) return;

    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: meses,
            datasets: [
                {
                    label: 'Ventas (Bs)',
                    data: ventas,
                    backgroundColor: 'rgba(16,185,129,0.7)',
                    borderColor: 'rgba(16,185,129,1)',
                    borderWidth: 1.5,
                    borderRadius: 6,
                },
                {
                    label: 'Compras (Bs)',
                    data: compras,
                    backgroundColor: 'rgba(239,68,68,0.7)',
                    borderColor: 'rgba(239,68,68,1)',
                    borderWidth: 1.5,
                    borderRadius: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: { color: getColors().tick, font: { size: 11 }, boxWidth: 12, padding: 16 }
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ' Bs ' + Number(ctx.parsed.y).toLocaleString('es-BO', {minimumFractionDigits:2})
                    }
                }
            },
            scales: {
                x: { grid: { color: getColors().grid }, ticks: { color: getColors().tick, font: { size: 11 } } },
                y: { grid: { color: getColors().grid }, ticks: {
                    color: getColors().tick, font: { size: 11 },
                    callback: v => 'Bs ' + Number(v).toLocaleString('es-BO', {minimumFractionDigits:0})
                }},
            },
        },
    });

    window.addEventListener('theme-changed', function () {
        const c = getColors();
        chart.options.scales.x.grid.color = c.grid;
        chart.options.scales.x.ticks.color = c.tick;
        chart.options.scales.y.grid.color = c.grid;
        chart.options.scales.y.ticks.color = c.tick;
        chart.options.plugins.legend.labels.color = c.tick;
        chart.update();
    });
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
function doExportPDF() {
    window.print();
}

function doExportExcel() {
    const activeTable = document.querySelector('table:not(.no-print)');
    if (!activeTable) {
        alert('No hay datos en la tabla para exportar.');
        return;
    }

    let rowsHtml = '';
    const rows = activeTable.querySelectorAll('tr');
    rows.forEach((tr, index) => {
        const isHeader = tr.parentElement.tagName === 'THEAD';
        const cells = tr.querySelectorAll(isHeader ? 'th' : 'td');
        let rowStr = '<tr>';
        cells.forEach((td, cellIndex) => {
            if (cellIndex === cells.length - 1 && td.classList.contains('no-print')) return;
            
            const txt = td.textContent.replace(/\s+/g, ' ').trim();
            if (isHeader) {
                rowStr += `<th style="background-color:#d97706; color:#ffffff; font-weight:bold; padding:8px; border:1px solid #b45309; text-align:center;">${txt}</th>`;
            } else {
                const isNum = txt.startsWith('Bs.') || txt.includes('Kg') || !isNaN(parseFloat(txt));
                rowStr += `<td style="border:1px solid #cbd5e1; padding:7px 10px; ${isNum ? 'text-align:right; font-family:Consolas,monospace;' : ''}">${txt}</td>`;
            }
        });
        rowStr += '</tr>';
        rowsHtml += rowStr;
    });

    const htmlContent = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #1e293b; }
                .header-banner { background-color: #0f172a; color: #fbbf24; font-size: 16px; font-weight: bold; text-align: center; padding: 14px; }
                .info-sub { background-color: #1e293b; color: #ffffff; font-size: 11px; font-weight: bold; padding: 8px 12px; }
            </style>
        </head>
        <body>
            <table style="width:100%; border-collapse:collapse;">
                <tr><td colspan="8" class="header-banner">EMPRESA MINERA — REPORTE DE COMERCIALIZACIÓN DE MINERALES</td></tr>
                <tr><td colspan="8" class="info-sub">FECHA DE GENERACIÓN: ${new Date().toLocaleDateString('es-BO')} ${new Date().toLocaleTimeString('es-BO')}</td></tr>
                <tr><td colspan="8">&nbsp;</td></tr>
                ${rowsHtml}
            </table>
        </body>
        </html>
    `;

    const blob = new Blob(['\ufeff' + htmlContent], { type: 'application/vnd.ms-excel;charset=utf-8' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'Reporte_Comercializacion_' + new Date().toISOString().slice(0,10) + '.xls';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endpush
@endsection
