@extends('layouts.app')

@section('title', 'Pago de Servicios Externos')

@section('content')
@php
    $totalFiltrado = $total_filtrado ?? $servicios->sum('monto_total');
    $countServicios = count($servicios);
    $usuarioActual = auth()->user()->name ?? 'Administración General';
@endphp

<!-- Custom Styles for Buttons & Executive Print Layout -->
<style>
    .btn-export-excel {
        background: linear-gradient(135deg, #059669, #047857);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(5, 150, 105, 0.25);
        transition: all 0.2s ease;
    }
    .btn-export-excel:hover {
        background: linear-gradient(135deg, #10b981, #059669);
        box-shadow: 0 6px 18px rgba(5, 150, 105, 0.4);
        transform: translateY(-1px);
    }
    .btn-export-pdf {
        background: linear-gradient(135deg, #e11d48, #be123c);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(225, 29, 72, 0.25);
        transition: all 0.2s ease;
    }
    .btn-export-pdf:hover {
        background: linear-gradient(135deg, #f43f5e, #e11d48);
        box-shadow: 0 6px 18px rgba(225, 29, 72, 0.4);
        transform: translateY(-1px);
    }
    .btn-export-print {
        background: linear-gradient(135deg, #d97706, #b45309);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(217, 119, 6, 0.25);
        transition: all 0.2s ease;
    }
    .btn-export-print:hover {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        box-shadow: 0 6px 18px rgba(217, 119, 6, 0.4);
        transform: translateY(-1px);
    }

    /* Print media overrides */
    @media print {
        .no-print, nav, header, aside, .sidebar, [data-sidebar], #sidebar, .filter-section {
            display: none !important;
        }
        body {
            background: #ffffff !important;
            color: #0f172a !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 10px !important;
        }
        #exec-report-servicios {
            display: block !important;
        }
        #main-interactive-view {
            display: none !important;
        }
        @page {
            size: letter landscape;
            margin: 7mm 8mm;
        }
    }
</style>

<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0 no-print">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100 flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl flex items-center justify-center text-white flex-shrink-0 shadow-lg shadow-sky-500/20" style="background:linear-gradient(135deg,#0284c7,#0369a1);">
                    <i class="fa-solid fa-truck-front text-sm"></i>
                </span>
                Pago de Servicios Externos
            </h1>
            <p class="text-sm text-slate-400 mt-1">Registra y controla los pagos por servicios externos (flete de volqueta, transporte de mineral, excavadora, maquinaria, etc.) descontados de Caja Chica.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5 self-start">
            {{-- Botón Excel --}}
            <button type="button" onclick="doExportExcel(this)" class="btn-export-excel inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer">
                <i class="fa-solid fa-file-excel mr-2 text-sm"></i> Excel
            </button>
            {{-- Botón PDF --}}
            <button type="button" onclick="doExportPDF(this)" class="btn-export-pdf inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer">
                <i class="fa-solid fa-file-pdf mr-2 text-sm"></i> PDF
            </button>
            {{-- Botón Imprimir --}}
            <button type="button" onclick="window.print()" class="btn-export-print inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-bold transition cursor-pointer">
                <i class="fa-solid fa-print mr-2 text-sm"></i> Imprimir
            </button>
            {{-- Botón Registrar Nuevo --}}
            <a href="{{ route('servicios-externos.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-xs font-bold text-white transition duration-150 shadow-lg shadow-sky-500/20 cursor-pointer">
                <i class="fa-solid fa-plus mr-2 text-sm"></i> Registrar Nuevo Servicio
            </a>
        </div>
    </div>

    <!-- Summary Cards (Interactive Screen) -->
    <div id="kpi-container" class="grid grid-cols-1 sm:grid-cols-3 gap-5 no-print">
        <div class="glass-card rounded-2xl p-5 border border-sky-500/20 bg-sky-500/5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-sky-400 uppercase tracking-wider">Total Gastado en Servicios</span>
                <i class="fa-solid fa-truck-ramp-box text-sky-400 text-lg"></i>
            </div>
            <div class="text-2xl font-black font-mono text-slate-100 mt-2">Bs. {{ number_format($total_gastado_servicios, 2) }}</div>
            <p class="text-[10px] text-slate-400 mt-1">Histórico total pagado a volquetas y maquinaria</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-emerald-500/20 bg-emerald-500/5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Saldo Sobrante en Caja Chica</span>
                <i class="fa-solid fa-vault text-emerald-400 text-lg"></i>
            </div>
            <div class="text-2xl font-black font-mono text-emerald-400 mt-2">Bs. {{ number_format($saldo_caja, 2) }}</div>
            <p class="text-[10px] text-slate-400 mt-1">Disponible tras descontar planilla, anticipos y servicios</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-amber-500/20 bg-amber-500/5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Monto Filtrado en Lista</span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-mono text-[10px] font-bold">
                    {{ $countServicios }} registros
                </span>
            </div>
            <div class="text-2xl font-black font-mono text-amber-400 mt-2">Bs. {{ number_format($totalFiltrado, 2) }}</div>
            <p class="text-[10px] text-slate-400 mt-1">Subtotal de los pagos filtrados en este reporte</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="glass-card rounded-xl p-5 no-print filter-section">
        <form id="filterFormServicios" action="{{ route('servicios-externos.index') }}" method="GET" onsubmit="event.preventDefault(); submitFilterRealTime(this);" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6 items-end">
            <div>
                <label for="buscar" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Chofer, Placa o Comprobante</label>
                <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}" 
                       oninput="clearTimeout(searchDebounceTimeout); searchDebounceTimeout = setTimeout(() => submitFilterRealTime(this.form), 250)"
                       class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-sm"
                       placeholder="Ej. Juan Pérez, 1234-ABC...">
            </div>

            <div>
                <label for="tipo_servicio_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Tipo de Servicio</label>
                <select name="tipo_servicio" id="tipo_servicio_filter" 
                        onchange="submitFilterRealTime(this.form)"
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-sm">
                    <option value="">Todos los Servicios</option>
                    <option value="Volqueta / Transporte de Mineral" {{ request('tipo_servicio') === 'Volqueta / Transporte de Mineral' ? 'selected' : '' }}>Volqueta / Transporte de Mineral</option>
                    <option value="Excavadora" {{ request('tipo_servicio') === 'Excavadora' ? 'selected' : '' }}>Excavadora</option>
                    <option value="Gallinita / Retroexcavadora" {{ request('tipo_servicio') === 'Gallinita / Retroexcavadora' ? 'selected' : '' }}>Gallinita / Retroexcavadora</option>
                    <option value="Tractor Oruga" {{ request('tipo_servicio') === 'Tractor Oruga' ? 'selected' : '' }}>Tractor Oruga</option>
                    <option value="Mantenimiento de Maquinaria" {{ request('tipo_servicio') === 'Mantenimiento de Maquinaria' ? 'selected' : '' }}>Mantenimiento de Maquinaria</option>
                    <option value="Otro Servicio" {{ request('tipo_servicio') === 'Otro Servicio' ? 'selected' : '' }}>Otro Servicio</option>
                </select>
            </div>

            @if(isset($bocaminas) && count($bocaminas) > 0)
            <div>
                <label for="bocamina_id_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Bocamina</label>
                <select name="bocamina_id" id="bocamina_id_filter" 
                        onchange="submitFilterRealTime(this.form)"
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-sm">
                    <option value="">Todas las Bocaminas</option>
                    @foreach($bocaminas as $boc)
                        <option value="{{ $boc->id }}" {{ request('bocamina_id') == $boc->id ? 'selected' : '' }}>{{ $boc->nombre }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label for="fecha_desde" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Fecha Desde</label>
                <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}"
                       onchange="submitFilterRealTime(this.form)"
                       class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-sky-500 text-sm">
            </div>

            <div>
                <label for="fecha_hasta" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Fecha Hasta</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}"
                       onchange="submitFilterRealTime(this.form)"
                       class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-sky-500 text-sm">
            </div>

            <div class="flex space-x-2">
                <button type="button" onclick="document.getElementById('buscar').value = ''; document.getElementById('tipo_servicio_filter').value = ''; if(document.getElementById('bocamina_id_filter')) document.getElementById('bocamina_id_filter').value = ''; document.getElementById('fecha_desde').value = ''; document.getElementById('fecha_hasta').value = ''; submitFilterRealTime(this.form);" class="btn-vibrant-warm flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-bold rounded-lg shadow-lg cursor-pointer" title="Limpiar Filtros">
                    <i class="fa-solid fa-rotate-left mr-2"></i> Limpiar
                </button>
            </div>
        </form>
    </div>

    <!-- Table Section (Interactive Screen) -->
    <div id="main-interactive-view">
        <div id="table-container" class="glass-card rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900/40">
                            <th class="px-6 py-4 font-semibold w-16">ID</th>
                            <th class="px-6 py-4 font-semibold">Fecha / Comprobante</th>
                            <th class="px-6 py-4 font-semibold">Tipo de Servicio</th>
                            <th class="px-6 py-4 font-semibold">Chofer / Operador</th>
                            <th class="px-6 py-4 font-semibold">Placa / Máquina</th>
                            <th class="px-6 py-4 font-semibold">Servicio (Detalle)</th>
                            <th class="px-6 py-4 font-semibold text-right">Monto Total</th>
                            <th class="px-6 py-4 no-print text-center w-36">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                        @forelse($servicios as $item)
                            <tr class="hover:bg-slate-900/20 transition duration-150">
                                <td class="px-6 py-4 font-mono text-slate-400 font-bold text-xs">{{ str_pad($item->id, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4 font-mono text-xs">
                                    <div class="font-bold text-slate-200">{{ $item->fecha->format('d/m/Y') }}</div>
                                    @if($item->numero_comprobante)
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-sky-500/10 text-sky-400 font-mono text-[9.5px] font-bold mt-0.5">
                                            Nº {{ $item->numero_comprobante }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sky-500/10 text-sky-300 border border-sky-500/20 font-bold text-xs">
                                        <i class="fa-solid {{ str_contains(strtolower($item->tipo_servicio), 'volqueta') ? 'fa-truck-front' : 'fa-tractor' }} text-sky-400 text-xs"></i>
                                        {{ $item->tipo_servicio }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-100">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-user-gear text-slate-400 text-xs"></i>
                                        {{ $item->chofer_operador }}
                                    </div>
                                    @if($item->metodo_pago)
                                        <div class="text-[10px] text-slate-400 font-mono capitalize mt-0.5">
                                            <i class="fa-solid fa-money-bill-wave mr-1 text-slate-500"></i>{{ $item->metodo_pago }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-amber-400 font-bold">
                                    <span class="px-2 py-0.5 rounded bg-slate-900 border border-amber-500/30">
                                        🚘 {{ $item->placa_maquinaria }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-xs font-mono">
                                    <div>
                                        <strong class="text-slate-200">{{ number_format($item->cantidad, 2) }}</strong> {{ $item->unidad_medida }}
                                        @if($item->precio_unitario > 0)
                                            <span class="text-slate-500">× Bs. {{ number_format($item->precio_unitario, 2) }}</span>
                                        @endif
                                    </div>
                                    @if($item->origen_destino)
                                        <div class="text-[10px] text-slate-400 mt-0.5">
                                            <i class="fa-solid fa-route mr-1 text-slate-500"></i>{{ $item->origen_destino }}
                                        </div>
                                    @endif
                                    @if($item->bocamina)
                                        <div class="text-[9.5px] text-emerald-400 font-sans mt-0.5">
                                            <i class="fa-solid fa-mountain mr-1"></i>{{ $item->bocamina->nombre }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-mono font-black text-sky-400 text-base">
                                    Bs. {{ number_format($item->monto_total, 2) }}
                                </td>
                                <td class="px-6 py-4 no-print text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('servicios-externos.show', $item->id) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-sky-400 transition duration-150" title="Ver / Imprimir Comprobante">
                                            <i class="fa-solid fa-print text-xs"></i>
                                        </a>
                                        <a href="{{ route('servicios-externos.edit', $item->id) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-amber-400 transition duration-150" title="Editar Servicio">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </a>
                                        <form action="{{ route('servicios-externos.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este registro de servicio?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-900/50 text-slate-400 hover:text-rose-400 transition duration-150 cursor-pointer" title="Eliminar">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                    <i class="fa-solid fa-truck-front text-4xl mb-3 block text-slate-600 opacity-40"></i>
                                    No se encontraron pagos de servicios externos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($servicios) > 0)
                    <tfoot>
                        <tr class="bg-slate-900/80 font-bold border-t-2 border-slate-700">
                            <td colspan="6" class="px-6 py-4 text-right uppercase tracking-wider text-xs text-slate-300">
                                Total Pagado en Servicios Filtrados ({{ $countServicios }} registros):
                            </td>
                            <td class="px-6 py-4 text-right font-mono font-black text-amber-400 text-lg">
                                Bs. {{ number_format($totalFiltrado, 2) }}
                            </td>
                            <td class="no-print"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- 🏛️ EXECUTIVE CORPORATE REPORT VIEW (PDF EXPORT & PRINT)                 -->
    <!-- ===================================================================== -->
    <div id="exec-report-servicios" class="hidden">
        <div class="exec-doc" style="font-family:'Outfit', Arial, sans-serif; background:#ffffff; color:#0f172a; padding:10px;">
            
            {{-- Encabezado Corporativo --}}
            <div style="border-bottom:3px solid #0f172a; padding-bottom:12px; margin-bottom:14px; display:flex; justify-content:space-between; align-items:flex-end;">
                <div style="display:flex; align-items:center; gap:14px;">
                    <div style="width:46px; height:46px; background:#0f172a; color:#f59e0b; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0;">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                    </div>
                    <div>
                        <h1 style="font-size:18px; font-weight:900; color:#0f172a; text-transform:uppercase; letter-spacing:0.04em; margin:0; line-height:1.1;">
                            EMPRESA MINERA — CONTROL OPERATIVO
                        </h1>
                        <p style="font-size:12.5px; font-weight:800; color:#0284c7; text-transform:uppercase; letter-spacing:0.03em; margin:3px 0 0 0;">
                            REPORTE EJECUTIVO DE PAGOS POR SERVICIOS EXTERNOS Y MAQUINARIA
                        </p>
                        <p style="font-size:9px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; margin:2px 0 0 0;">
                            RESPALDO DE EGRESOS DE CAJA CHICA · FLETES, VOLQUETAS Y EQUIPO PESADO
                        </p>
                    </div>
                </div>
                <div style="border:1.5px solid #cbd5e1; border-radius:8px; background:#f8fafc; padding:6px 12px; font-size:9.5px; min-width:260px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                        <span style="font-weight:800; color:#475569; text-transform:uppercase; font-size:9px;">ESTADO:</span>
                        <span style="background:#047857; color:#ffffff; font-size:8px; font-weight:800; padding:1px 6px; border-radius:4px; text-transform:uppercase;">DOCUMENTO OFICIAL AUDITADO</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                        <span style="font-weight:800; color:#475569; text-transform:uppercase; font-size:9px;">FECHA EMISIÓN:</span>
                        <span style="font-weight:700; color:#0f172a; font-family:monospace;">{{ now()->format('d/m/Y H:i') }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:2px;">
                        <span style="font-weight:800; color:#475569; text-transform:uppercase; font-size:9px;">PERÍODO / FILTRO:</span>
                        <span style="font-weight:700; color:#0f172a; font-family:monospace;">
                            @if(request('fecha_desde') && request('fecha_hasta'))
                                {{ \Carbon\Carbon::parse(request('fecha_desde'))->format('d/m/Y') }} al {{ \Carbon\Carbon::parse(request('fecha_hasta'))->format('d/m/Y') }}
                            @elseif(request('fecha_desde'))
                                Desde {{ \Carbon\Carbon::parse(request('fecha_desde'))->format('d/m/Y') }}
                            @else
                                Histórico Completo
                            @endif
                        </span>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="font-weight:800; color:#475569; text-transform:uppercase; font-size:9px;">GENERADO POR:</span>
                        <span style="font-weight:700; color:#0f172a;">{{ $usuarioActual }}</span>
                    </div>
                </div>
            </div>

            {{-- Barra de KPIs del Reporte --}}
            <div style="display:flex; gap:10px; margin-bottom:14px;">
                <div style="flex:1; background:#f8fafc; border:1.5px solid #cbd5e1; border-radius:8px; padding:8px 12px;">
                    <div style="font-size:8.5px; font-weight:800; text-transform:uppercase; color:#64748b; letter-spacing:0.05em;">TOTAL PAGADO (FILTRADO)</div>
                    <div style="font-size:16px; font-weight:900; color:#0284c7; font-family:Consolas, monospace; margin-top:2px;">
                        Bs. {{ number_format($totalFiltrado, 2) }}
                    </div>
                </div>
                <div style="flex:1; background:#f8fafc; border:1.5px solid #cbd5e1; border-radius:8px; padding:8px 12px;">
                    <div style="font-size:8.5px; font-weight:800; text-transform:uppercase; color:#64748b; letter-spacing:0.05em;">TOTAL REGISTROS EMITIDOS</div>
                    <div style="font-size:16px; font-weight:900; color:#0f172a; font-family:Consolas, monospace; margin-top:2px;">
                        {{ $countServicios }} SERVICIOS
                    </div>
                </div>
                <div style="flex:1; background:#f8fafc; border:1.5px solid #cbd5e1; border-radius:8px; padding:8px 12px;">
                    <div style="font-size:8.5px; font-weight:800; text-transform:uppercase; color:#64748b; letter-spacing:0.05em;">PROMEDIO POR SERVICIO</div>
                    <div style="font-size:16px; font-weight:900; color:#047857; font-family:Consolas, monospace; margin-top:2px;">
                        Bs. {{ number_format($countServicios > 0 ? $totalFiltrado / $countServicios : 0, 2) }}
                    </div>
                </div>
                <div style="flex:1; background:#f8fafc; border:1.5px solid #cbd5e1; border-radius:8px; padding:8px 12px;">
                    <div style="font-size:8.5px; font-weight:800; text-transform:uppercase; color:#64748b; letter-spacing:0.05em;">SALDO CAJA CHICA VIGENTE</div>
                    <div style="font-size:16px; font-weight:900; color:#059669; font-family:Consolas, monospace; margin-top:2px;">
                        Bs. {{ number_format($saldo_caja, 2) }}
                    </div>
                </div>
            </div>

            {{-- Tabla Ejecutiva Detallada --}}
            <table class="exec-table" style="width:100%; border-collapse:collapse; border:1.5px solid #cbd5e1; font-size:9px; margin-bottom:14px; background:#ffffff;">
                <thead>
                    <tr style="background:#0f172a; color:#ffffff;">
                        <th style="padding:6px 6px; text-align:center; font-weight:800; text-transform:uppercase; border:1px solid #334155; width:35px;">Nº</th>
                        <th style="padding:6px 6px; text-align:center; font-weight:800; text-transform:uppercase; border:1px solid #334155; width:65px;">FECHA</th>
                        <th style="padding:6px 6px; text-align:center; font-weight:800; text-transform:uppercase; border:1px solid #334155; width:75px;">COMPR. Nº</th>
                        <th style="padding:6px 8px; text-align:left; font-weight:800; text-transform:uppercase; border:1px solid #334155;">TIPO SERVICIO</th>
                        <th style="padding:6px 8px; text-align:left; font-weight:800; text-transform:uppercase; border:1px solid #334155;">CHOFER / OPERADOR</th>
                        <th style="padding:6px 6px; text-align:center; font-weight:800; text-transform:uppercase; border:1px solid #334155; width:75px;">PLACA / MAQ.</th>
                        <th style="padding:6px 8px; text-align:left; font-weight:800; text-transform:uppercase; border:1px solid #334155;">DESTINO / BOCAMINA</th>
                        <th style="padding:6px 6px; text-align:center; font-weight:800; text-transform:uppercase; border:1px solid #334155; width:70px;">CANTIDAD</th>
                        <th style="padding:6px 6px; text-align:right; font-weight:800; text-transform:uppercase; border:1px solid #334155; width:75px;">PRECIO U.</th>
                        <th style="padding:6px 8px; text-align:right; font-weight:800; text-transform:uppercase; border:1px solid #334155; width:90px;">TOTAL (Bs.)</th>
                        <th style="padding:6px 6px; text-align:center; font-weight:800; text-transform:uppercase; border:1px solid #334155; width:65px;">MÉTODO</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($servicios as $idx => $s)
                        <tr style="background: {{ $idx % 2 === 0 ? '#ffffff' : '#f8fafc' }}; border-bottom:1px solid #cbd5e1;">
                            <td style="padding:5px 6px; text-align:center; font-family:monospace; font-weight:bold; border:1px solid #cbd5e1;">{{ str_pad($s->id, 2, '0', STR_PAD_LEFT) }}</td>
                            <td style="padding:5px 6px; text-align:center; font-family:monospace; border:1px solid #cbd5e1;">{{ $s->fecha->format('d/m/Y') }}</td>
                            <td style="padding:5px 6px; text-align:center; font-family:monospace; font-weight:bold; color:#0284c7; border:1px solid #cbd5e1;">
                                {{ $s->numero_comprobante ? 'Nº ' . $s->numero_comprobante : 'S/N' }}
                            </td>
                            <td style="padding:5px 8px; font-weight:700; border:1px solid #cbd5e1;">{{ $s->tipo_servicio }}</td>
                            <td style="padding:5px 8px; font-weight:600; border:1px solid #cbd5e1;">{{ $s->chofer_operador }}</td>
                            <td style="padding:5px 6px; text-align:center; font-family:monospace; font-weight:bold; color:#b45309; border:1px solid #cbd5e1;">{{ $s->placa_maquinaria }}</td>
                            <td style="padding:5px 8px; border:1px solid #cbd5e1;">
                                <div>{{ $s->origen_destino ?: '-' }}</div>
                                @if($s->bocamina)
                                    <div style="font-size:8px; color:#047857; font-weight:bold;">📍 {{ $s->bocamina->nombre }}</div>
                                @endif
                            </td>
                            <td style="padding:5px 6px; text-align:center; font-family:monospace; border:1px solid #cbd5e1;">
                                {{ number_format($s->cantidad, 2) }} {{ $s->unidad_medida }}
                            </td>
                            <td style="padding:5px 6px; text-align:right; font-family:monospace; border:1px solid #cbd5e1;">
                                {{ $s->precio_unitario > 0 ? number_format($s->precio_unitario, 2) : '-' }}
                            </td>
                            <td style="padding:5px 8px; text-align:right; font-family:monospace; font-weight:900; color:#0f172a; border:1px solid #cbd5e1;">
                                {{ number_format($s->monto_total, 2) }}
                            </td>
                            <td style="padding:5px 6px; text-align:center; font-size:8px; text-transform:uppercase; font-weight:bold; border:1px solid #cbd5e1;">
                                {{ $s->metodo_pago ?: 'Efectivo' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" style="padding:16px; text-align:center; color:#64748b; font-style:italic; border:1px solid #cbd5e1;">
                                No se encontraron registros de servicios externos bajo los criterios seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr style="background:#0f172a; color:#ffffff; font-weight:900; font-size:10px;">
                        <td colspan="9" style="padding:7px 10px; text-align:right; text-transform:uppercase; letter-spacing:0.04em; border:1px solid #0f172a;">
                            TOTAL GENERAL PAGADO EN SERVICIOS EXTERNOS (Bs.):
                        </td>
                        <td style="padding:7px 8px; text-align:right; font-family:Consolas, monospace; font-size:11px; color:#38bdf8; border:1px solid #0f172a;">
                            {{ number_format($totalFiltrado, 2) }}
                        </td>
                        <td style="border:1px solid #0f172a;"></td>
                    </tr>
                </tfoot>
            </table>

            {{-- Firmas de Conformidad y Auditoría --}}
            <div style="margin-top:28px; display:flex; justify-content:space-between; gap:40px; page-break-inside:avoid;">
                <div style="flex:1; text-align:center; border-top:1.5px solid #0f172a; padding-top:6px;">
                    <div style="font-size:10.5px; font-weight:800; color:#0f172a; margin:0;">{{ $usuarioActual }}</div>
                    <div style="font-size:8.5px; font-weight:800; color:#0284c7; text-transform:uppercase; letter-spacing:0.05em; margin:2px 0 0 0;">ADMINISTRACIÓN Y CAJA CHICA</div>
                    <div style="font-size:8px; color:#64748b; margin:2px 0 0 0;">Control y Emisión de Egresos</div>
                </div>

                <div style="flex:1; text-align:center; border-top:1.5px solid #0f172a; padding-top:6px;">
                    <div style="font-size:10.5px; font-weight:800; color:#0f172a; margin:0;">SUPERVISIÓN DE MINA</div>
                    <div style="font-size:8.5px; font-weight:800; color:#d97706; text-transform:uppercase; letter-spacing:0.05em; margin:2px 0 0 0;">CONTROL OPERATIVO Y MAQUINARIA</div>
                    <div style="font-size:8px; color:#64748b; margin:2px 0 0 0;">Visto Bueno de Horas / Viajes</div>
                </div>
            </div>

            {{-- Pie de Página Institucional --}}
            <div style="margin-top:18px; padding-top:6px; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; font-size:8px; color:#94a3b8; text-transform:uppercase; letter-spacing:0.04em;">
                <span>Sistema de Pagos y Control Minero SCPM · Documento Oficial de Respaldo Contable</span>
                <span>Generado el {{ now()->format('d/m/Y H:i:s') }} · Página 1 / 1</span>
            </div>

        </div>
    </div>

</div>

@push('scripts')
<script>
let searchDebounceTimeout = null;

// ─── Realtime Filter Fetch ───────────────────────────────────────────────────
function submitFilterRealTime(form) {
    const url = new URL(form.action);
    const formData = new FormData(form);
    const params = new URLSearchParams();

    for (const [key, value] of formData.entries()) {
        if (value.trim() !== '') {
            params.append(key, value);
        }
    }

    url.search = params.toString();
    window.history.replaceState({}, '', url.toString());

    fetch(url.toString(), {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');

        const newTable = doc.getElementById('table-container');
        const currentTable = document.getElementById('table-container');
        if (newTable && currentTable) {
            currentTable.innerHTML = newTable.innerHTML;
        }

        const newKpi = doc.getElementById('kpi-container');
        const currentKpi = document.getElementById('kpi-container');
        if (newKpi && currentKpi) {
            currentKpi.innerHTML = newKpi.innerHTML;
        }

        const newExec = doc.getElementById('exec-report-servicios');
        const currentExec = document.getElementById('exec-report-servicios');
        if (newExec && currentExec) {
            currentExec.innerHTML = newExec.innerHTML;
        }
    })
    .catch(err => {
        console.error('Error al filtrar:', err);
        form.submit();
    });
}

// ─── PDF Export (Corporate Letter Landscape Iframe) ──────────────────────────
function doExportPDF(btnEl) {
    const sourceEl = document.getElementById('exec-report-servicios');
    if (!sourceEl) {
        window.print();
        return;
    }

    const reportTitle = 'Reporte_Servicios_Externos_' + new Date().toISOString().slice(0, 10);

    const docHtml = `<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>${reportTitle}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page {
            size: letter landscape;
            margin: 6mm 8mm;
        }
        *, *::before, *::after {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        body {
            margin: 0;
            padding: 10px 14px;
            background: #ffffff !important;
            color: #0f172a !important;
            font-family: 'Outfit', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            font-size: 10px;
            line-height: 1.3;
        }
        table {
            page-break-inside: auto;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        thead {
            display: table-header-group;
        }
        tfoot {
            display: table-footer-group;
        }
    </style>
</head>
<body>
    ${sourceEl.innerHTML}
</body>
</html>`;

    // Remove any previous print iframe
    const oldIframe = document.getElementById('scpm-servicios-iframe');
    if (oldIframe) oldIframe.remove();

    const iframe = document.createElement('iframe');
    iframe.id = 'scpm-servicios-iframe';
    iframe.style.position = 'fixed';
    iframe.style.right = '0';
    iframe.style.bottom = '0';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = 'none';
    iframe.style.visibility = 'hidden';
    document.body.appendChild(iframe);

    iframe.contentDocument.open();
    iframe.contentDocument.write(docHtml);
    iframe.contentDocument.close();

    setTimeout(() => {
        try {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        } catch (err) {
            console.warn('Iframe print error, fallback to window.print', err);
            window.print();
        }
        setTimeout(() => {
            if (iframe.parentNode) iframe.remove();
        }, 2500);
    }, 350);
}

// ─── Excel Export (Structured Infallible XML/HTML Spreadsheet) ────────────────
function doExportExcel(btnEl) {
    const execContainer = document.getElementById('exec-report-servicios');
    const table = execContainer ? execContainer.querySelector('table.exec-table') : null;
    
    if (!table) {
        alert('No hay datos en la tabla para exportar.');
        return;
    }

    let rowsHtml = '';
    const rows = table.querySelectorAll('tr');
    rows.forEach(tr => {
        const isHeader = tr.parentElement && tr.parentElement.tagName === 'THEAD';
        const isFooter = tr.parentElement && tr.parentElement.tagName === 'TFOOT';
        const cells = tr.querySelectorAll('th, td');
        
        let rowStr = '<tr>';
        cells.forEach(cell => {
            const txt = cell.textContent.replace(/\s+/g, ' ').trim();
            const colspan = cell.getAttribute('colspan') ? ` colspan="${cell.getAttribute('colspan')}"` : '';
            
            if (isHeader) {
                rowStr += `<th${colspan} style="background-color:#0f172a; color:#38bdf8; font-weight:bold; padding:8px; border:1px solid #334155; text-align:center;">${txt}</th>`;
            } else if (isFooter) {
                rowStr += `<td${colspan} style="background-color:#0f172a; color:#fbbf24; font-weight:bold; padding:8px; border:1px solid #0f172a; font-family:Consolas,monospace; font-size:12px;">${txt}</td>`;
            } else {
                const isNum = !isNaN(parseFloat(txt.replace(/,/g, ''))) && (txt.includes('.') || !txt.includes(' '));
                rowStr += `<td${colspan} style="border:1px solid #cbd5e1; padding:6px 8px; ${isNum ? 'text-align:right; font-family:Consolas,monospace;' : ''}">${txt}</td>`;
            }
        });
        rowStr += '</tr>';
        rowsHtml += rowStr;
    });

    const filename = 'Reporte_Servicios_Externos_' + new Date().toISOString().slice(0, 10) + '.xls';

    const htmlContent = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #1e293b; }
                .header-banner { background-color: #0f172a; color: #38bdf8; font-size: 16px; font-weight: bold; text-align: center; padding: 14px; }
                .info-sub { background-color: #1e293b; color: #ffffff; font-size: 11px; font-weight: bold; padding: 8px 12px; }
            </style>
        </head>
        <body>
            <table style="width:100%; border-collapse:collapse;">
                <tr><td colspan="11" class="header-banner">EMPRESA MINERA — REPORTE DE PAGOS POR SERVICIOS EXTERNOS</td></tr>
                <tr><td colspan="11" class="info-sub">FECHA DE GENERACIÓN: ${new Date().toLocaleDateString('es-BO')} ${new Date().toLocaleTimeString('es-BO')} · USUARIO: {{ $usuarioActual }}</td></tr>
                <tr><td colspan="11">&nbsp;</td></tr>
                ${rowsHtml}
            </table>
        </body>
        </html>
    `;

    const blob = new Blob(['\ufeff' + htmlContent], { type: 'application/vnd.ms-excel;charset=utf-8' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    setTimeout(() => {
        if (link.parentNode) document.body.removeChild(link);
    }, 200);
}
</script>
@endpush
@endsection
