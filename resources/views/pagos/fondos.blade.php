@extends('layouts.app')

@section('title', 'Caja Chica del Personal y Recargas')

@section('content')
@php $positivo = $saldo_caja >= 0; @endphp

@push('css')
<style>
.m-kpi {
    position: relative;
    overflow: hidden;
    padding: 20px;
    border-radius: 16px;
    color: #fff;
    transition: all 0.25s ease;
}
.m-kpi:hover { transform: translateY(-2px); }
.m-kpi::before {
    content: '';
    position: absolute;
    top: -30%;
    right: -10%;
    width: 120px;
    height: 120px;
    background: rgba(255,255,255,0.08);
    border-radius: 50%;
    pointer-events: none;
}
.m-kpi::after {
    content: '';
    position: absolute;
    bottom: -20%;
    right: 15%;
    width: 70px;
    height: 70px;
    background: rgba(255,255,255,0.05);
    border-radius: 50%;
    pointer-events: none;
}
</style>
@endpush

<div class="space-y-6" x-data="{ openResetModal: false, pdfExported: false, showRangeInput: '{{ request('rango') === 'personalizado' ? '1' : '0' }}' }">

    {{-- ══ HEADER ══ --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg shadow-cyan-500/30 flex-shrink-0">
                <i class="fa-solid fa-vault text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-100">
                    Caja Chica del Personal <span class="text-cyan-400 font-extrabold">/</span> Recargas
                </h1>
                <p class="text-xs text-slate-400 mt-0.5">Gestión de inyecciones del banco, saldos sobrantes acumulados y cierre de períodos</p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <button @click="openResetModal = true" type="button"
                    class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white font-bold text-xs shadow-lg shadow-amber-500/20 hover:scale-105 active:scale-95 transition-all flex items-center gap-2 cursor-pointer">
                <i class="fa-solid fa-rotate-left text-xs"></i> Reiniciar Caja (Empezar de cero)
            </button>
            <button onclick="window.doExportExcelCaja()" type="button"
                    class="px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-emerald-400 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-excel"></i> Excel
            </button>
            <button onclick="window.doExportPDFCaja()" type="button"
                    class="px-3.5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-rose-400 font-bold text-xs transition flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </button>
        </div>
    </div>

    {{-- ══ SUMMARY CARDS (ESTILO EXACTO TABLERO PRINCIPAL - 4 TARJETAS) ══ --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Card 1: Total Inyectado (Banco) --}}
        <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-cyan-500 to-blue-650 text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-100/90">Total Inyectado (Banco)</span>
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                    <i class="fa-solid fa-building-columns text-xs text-white"></i>
                </div>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight font-mono">Bs. {{ number_format($total_recargado, 2) }}</h2>
            <p class="text-[10px] text-cyan-100/80 font-medium mt-2">Sumatoria de fondos registrados</p>
        </div>

        {{-- Card 2: Gastado en Pagos Totales (Planillas) --}}
        <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-indigo-500 to-purple-650 text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-100/90">Gastado en Pagos Totales</span>
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                    <i class="fa-solid fa-receipt text-xs text-white"></i>
                </div>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight font-mono">Bs. {{ number_format($total_gastado_pagos, 2) }}</h2>
            <p class="text-[10px] text-indigo-100/80 font-medium mt-2">Liquidación final de planillas</p>
        </div>

        {{-- Card 3: Gastado en Anticipos --}}
        <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-rose-500 to-red-650 text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-100/90">Gastado en Anticipos</span>
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                    <i class="fa-solid fa-hand-holding-dollar text-xs text-white"></i>
                </div>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight font-mono">Bs. {{ number_format($total_gastado_anticipos, 2) }}</h2>
            <p class="text-[10px] text-rose-100/80 font-medium mt-2">Adelantos a trabajadores</p>
        </div>

        {{-- Card 4: Saldo Sobrante en Caja --}}
        @php
            $saldoGradient = $positivo ? 'from-emerald-500 to-teal-600' : 'from-amber-500 to-orange-650';
            $saldoText = $positivo ? 'text-emerald-100/80' : 'text-amber-100/80';
        @endphp
        <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br {{ $saldoGradient }} text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-white/90">Saldo Sobrante en Caja</span>
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                    <i class="fa-solid fa-vault text-xs text-white"></i>
                </div>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight font-mono">Bs. {{ number_format(abs($saldo_caja), 2) }}</h2>
            <p class="text-[10px] {{ $saldoText }} font-medium mt-2">
                {{ $positivo ? '● Efectivo disponible para planillas' : '▲ Caja en déficit' }}
            </p>
        </div>
    </div>

    {{-- ══ FILTRO DE PERÍODO / REPORTES ══ --}}
    <div class="glass-card rounded-2xl p-5">
        <form action="{{ route('fondos-caja.index') }}" method="GET" class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 flex-shrink-0">
                    <i class="fa-solid fa-filter text-xs"></i>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-slate-200 uppercase tracking-wider">Filtrar Historial y Reporte por Período</h3>
                    <p class="text-[10px] text-slate-500">Selecciona el rango de meses para analizar recargas y saldos sobrantes</p>
                </div>
            </div>

            <div class="flex items-center gap-3 flex-wrap">
                <select name="rango" onchange="if(this.value==='personalizado'){ this.form.submit(); } else { this.form.submit(); }"
                        class="px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 text-xs font-medium focus:outline-none focus:border-cyan-500">
                    <option value="">Todas las recargas</option>
                    <option value="1_mes" {{ request('rango') === '1_mes' ? 'selected' : '' }}>Último Mes (1 Mes)</option>
                    <option value="3_meses" {{ request('rango') === '3_meses' ? 'selected' : '' }}>Últimos 3 Meses</option>
                    <option value="este_mes" {{ request('rango') === 'este_mes' ? 'selected' : '' }}>Este Mes</option>
                    <option value="personalizado" {{ request('rango') === 'personalizado' ? 'selected' : '' }}>Personalizado...</option>
                </select>

                @if(request('rango') === 'personalizado' || request('fecha_desde'))
                    <div class="flex items-center gap-2">
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="px-2.5 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-xs text-slate-200">
                        <span class="text-slate-500 text-xs">a</span>
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="px-2.5 py-1.5 bg-slate-800 border border-slate-700 rounded-lg text-xs text-slate-200">
                        <button type="submit" class="px-3 py-1.5 bg-cyan-500 text-white rounded-lg text-xs font-bold hover:bg-cyan-400">Filtrar</button>
                    </div>
                @endif

                @if(request('rango') || request('fecha_desde'))
                    <a href="{{ route('fondos-caja.index') }}" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-400 rounded-xl text-xs font-semibold">
                        Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ══ FORM + TABLE ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- ── Formulario Nueva Recarga ── --}}
        <div class="glass-card rounded-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-cyan-500/10 to-blue-500/10 border-b border-slate-800/60 px-5 py-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-md shadow-cyan-500/25 flex-shrink-0">
                    <i class="fa-solid fa-money-bill-trend-up text-white text-xs"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-200">Registrar Recarga de Caja</h3>
                    <p class="text-[10px] text-slate-500">Nuevo ingreso retirado del banco</p>
                </div>
            </div>

            <form action="{{ route('fondos-pagos.store') }}" method="POST" class="p-5 space-y-4">
                @csrf

                <div>
                    <label for="fecha" class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        <i class="fa-regular fa-calendar mr-1"></i> Fecha de Inyección
                    </label>
                    <input id="fecha" name="fecha" type="date" required value="{{ now()->toDateString() }}"
                           class="block w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500 text-sm font-mono">
                </div>

                <div>
                    <label for="monto" class="block text-[10px] font-bold uppercase tracking-widest text-cyan-400 mb-1.5">
                        <i class="fa-solid fa-coins mr-1"></i> Monto a Recargar (Bs.)
                    </label>
                    <div class="relative">
                        <input id="monto" name="monto" type="number" step="0.01" required min="0.01"
                               placeholder="0.00"
                               class="block w-full pl-3 pr-12 py-3 bg-slate-800 border border-cyan-500/40 rounded-xl text-slate-100 focus:outline-none focus:border-cyan-500 text-sm font-mono font-bold">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-black text-cyan-400 pointer-events-none font-mono">Bs.</span>
                    </div>
                </div>

                <div>
                    <label for="observacion" class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        <i class="fa-solid fa-note-sticky mr-1"></i> Origen / Concepto
                    </label>
                    <input id="observacion" name="observacion" type="text"
                           placeholder="Ej. Retiro Banco - Fondeo quincenal"
                           class="block w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500 text-sm">
                </div>

                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 py-3 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white font-black text-sm shadow-lg shadow-cyan-500/25 transition-all hover:scale-[1.02] active:scale-95 cursor-pointer">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    Confirmar Recarga
                </button>

                <div class="flex gap-2 bg-cyan-500/5 border border-cyan-500/15 rounded-xl px-3 py-2.5 text-[10px] text-slate-500 leading-relaxed">
                    <i class="fa-solid fa-circle-info text-cyan-500 mt-0.5 flex-shrink-0"></i>
                    <span>El saldo sobrante de recargas anteriores se acumula automáticamente al nuevo disponible.</span>
                </div>
            </form>
        </div>

        {{-- ── Tabla Historial con Remanentes ── --}}
        <div id="caja-chica-report-output" class="glass-card rounded-2xl overflow-hidden lg:col-span-2">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800/60 bg-gradient-to-r from-slate-800/20 to-transparent">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-clock-rotate-left text-cyan-400 text-sm"></i>
                    <div>
                        <h3 class="text-sm font-bold text-slate-200">Historial Detallado de Recargas y Sobrantes</h3>
                        <p class="text-[10px] text-slate-500">Monto inyectado + Sobrante acumulado del período</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-slate-400 bg-slate-800/60 border border-slate-700/40 px-3 py-1 rounded-full">
                    {{ count($fondos) }} {{ count($fondos) === 1 ? 'recarga' : 'recargas' }}
                </span>
            </div>

            @if(count($fondos) === 0)
                <div class="flex flex-col items-center justify-center py-14 text-slate-500 gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-slate-800/50 flex items-center justify-center">
                        <i class="fa-solid fa-building-columns text-2xl text-slate-600"></i>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-semibold text-slate-400">Sin recargas en este período</p>
                        <p class="text-xs text-slate-600 mt-0.5">Usa el formulario para registrar un fondeo</p>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b border-slate-800/40 bg-slate-900/50 text-slate-400">
                                <th class="px-4 py-3 text-left font-bold uppercase tracking-wider text-[10px]">Fecha</th>
                                <th class="px-4 py-3 text-right font-bold uppercase tracking-wider text-[10px]">Inyección (Banco)</th>
                                <th class="px-4 py-3 text-right font-bold uppercase tracking-wider text-[10px]">Sobró Anterior</th>
                                <th class="px-4 py-3 text-right font-bold uppercase tracking-wider text-[10px]">Total Disponible</th>
                                <th class="px-4 py-3 text-right font-bold uppercase tracking-wider text-[10px]">Gastos Planilla</th>
                                <th class="px-4 py-3 text-right font-bold uppercase tracking-wider text-[10px]">Sobrante Final</th>
                                <th class="px-4 py-3 text-center font-bold uppercase tracking-wider text-[10px] no-print">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/20">
                            @foreach($fondos as $i => $fondo)
                            <tr class="hover:bg-slate-800/10 transition-colors duration-150">
                                <td class="px-4 py-3.5 font-mono text-slate-300 whitespace-nowrap">
                                    <div class="font-bold">{{ $fondo->fecha->format('d/m/Y') }}</div>
                                    <div class="text-[9px] text-slate-500 font-normal truncate max-w-[120px]">{{ $fondo->observacion ?: 'Fondeo de Caja' }}</div>
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono font-bold text-cyan-400 whitespace-nowrap">
                                    +Bs. {{ number_format($fondo->monto, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono text-slate-400 whitespace-nowrap">
                                    Bs. {{ number_format($fondo->remanente_anterior, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono font-black text-slate-100 whitespace-nowrap">
                                    Bs. {{ number_format($fondo->total_disponible, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono text-rose-400 whitespace-nowrap">
                                    -Bs. {{ number_format($fondo->gastado_periodo, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono font-black text-emerald-400 whitespace-nowrap">
                                    Bs. {{ number_format($fondo->sobrante_remanente, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-center no-print whitespace-nowrap">
                                    <form action="{{ route('fondos-pagos.destroy', $fondo->id) }}" method="POST"
                                          class="inline" onsubmit="return confirm('¿Eliminar esta recarga de caja?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" title="Eliminar recarga"
                                                class="w-7 h-7 rounded-lg flex items-center justify-center bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white transition-all cursor-pointer">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ══ MODAL DE REINICIO DE CAJA (EMPEAZAR DE CERO) ══ --}}
    <div x-show="openResetModal" x-cloak style="display:none" :style="{ display: openResetModal ? 'flex' : 'none' }"
         @click.self="openResetModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
        <div class="w-full max-w-md bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-2xl">
            <div class="p-5 bg-gradient-to-r from-amber-500/10 to-orange-500/10 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 flex items-center justify-center text-amber-400">
                        <i class="fa-solid fa-rotate-left text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-100">Reiniciar Caja Chica (Empezar de cero)</h3>
                        <p class="text-[10px] text-slate-500">Obligatorio descargar reporte PDF antes de reiniciar</p>
                    </div>
                </div>
                <button @click="openResetModal = false" class="text-slate-500 hover:text-slate-300">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form action="{{ route('fondos-caja.reiniciar') }}" method="POST" class="p-5 space-y-4">
                @csrf
                <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-3.5 text-xs text-amber-300 space-y-1.5">
                    <div class="font-bold flex items-center gap-1.5 text-amber-400">
                        <i class="fa-solid fa-shield-halved"></i> Requisito de Seguridad Obligatorio
                    </div>
                    <p class="text-[11px] text-amber-300/80 leading-relaxed">
                        Para empezar de cero y reiniciar la caja a <strong>Bs. 0.00</strong>, debes descargar primero el <strong>Reporte de Cierre en PDF</strong> para respaldar la información.
                    </p>
                </div>

                {{-- Paso 1: Descargar PDF --}}
                <div class="space-y-1.5">
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">
                        Paso 1: Respaldar Período Actual
                    </label>
                    <button type="button" @click="window.doExportPDFCaja(); pdfExported = true;"
                            :class="pdfExported ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border-rose-500/40 hover:bg-rose-500 hover:text-white'"
                            class="w-full py-3 px-4 rounded-xl font-bold text-xs border transition flex items-center justify-center gap-2 cursor-pointer shadow-md">
                        <i class="fa-solid" :class="pdfExported ? 'fa-circle-check text-emerald-400' : 'fa-file-pdf text-rose-400'"></i>
                        <span x-text="pdfExported ? '✓ Reporte PDF Exportado (Paso 1 Completado)' : '1. Descargar Reporte de Cierre (PDF)'"></span>
                    </button>
                </div>

                {{-- Opciones opcionales --}}
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        Monto de Apertura (Bs.) <span class="text-slate-500 font-normal">(Opcional, dejar en 0 para iniciar todo en cero)</span>
                    </label>
                    <input type="number" step="0.01" name="monto_inicial" placeholder="0.00"
                           class="block w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-100 text-sm font-mono">
                </div>

                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">
                        Observación / Motivo del Cierre
                    </label>
                    <input type="text" name="observacion" placeholder="Ej. Cierre de trimestre — Inicio nuevo período"
                           class="block w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 text-xs">
                </div>

                {{-- Paso 2: Botón de confirmación (Desbloqueado tras exportar PDF) --}}
                <div class="pt-2 space-y-2">
                    <button type="submit" :disabled="!pdfExported"
                            :class="pdfExported ? 'bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white cursor-pointer shadow-lg shadow-amber-500/25 hover:scale-[1.02] active:scale-95' : 'bg-slate-800 text-slate-500 border border-slate-700/50 cursor-not-allowed opacity-60'"
                            class="w-full py-3 rounded-xl font-black text-xs transition flex items-center justify-center gap-2">
                        <i class="fa-solid" :class="pdfExported ? 'fa-rotate-left' : 'fa-lock'"></i>
                        <span x-text="pdfExported ? 'Paso 2: Confirmar Reinicio y Empezar de Cero' : 'Paso 2: Bloqueado (Descarga el PDF primero)'"></span>
                    </button>
                    <button type="button" @click="openResetModal = false" class="w-full py-2 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-400 text-xs font-bold">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// ─── PDF Export Caja Chica ───────────────────────────────────────────────────
window.doExportPDFCaja = function() {
    const tableContainer = document.getElementById('caja-chica-report-output');
    if (!tableContainer) return;
    const table = tableContainer.querySelector('table');
    if (!table) return;

    const now = new Date();
    const dateStr = now.toLocaleDateString('es-BO', { day:'2-digit', month:'long', year:'numeric' });
    const timeStr = now.toLocaleTimeString('es-BO', { hour:'2-digit', minute:'2-digit' });

    const headers = Array.from(table.querySelectorAll('thead th'))
        .filter(th => !th.classList.contains('no-print'))
        .map(th => th.textContent.replace(/\s+/g, ' ').trim());

    const rows = Array.from(table.querySelectorAll('tbody tr')).map(tr => {
        return Array.from(tr.querySelectorAll('td'))
            .filter((td, idx) => !td.classList.contains('no-print'))
            .map(td => td.textContent.replace(/\s+/g, ' ').trim());
    });

    const pdfHtml = `<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte_Caja_Chica_${now.toISOString().slice(0,10)}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 24px; color: #0f172a; background: #ffffff; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #0f172a; padding-bottom: 12px; margin-bottom: 18px; }
        .title { font-size: 20px; font-weight: 900; color: #0f172a; margin: 0; text-transform: uppercase; }
        .subtitle { font-size: 10px; color: #64748b; margin: 4px 0 0 0; }
        .badge { font-size: 9px; text-transform: uppercase; color: #64748b; font-weight: 700; text-align: right; }
        .company { font-size: 11px; font-weight: 800; color: #0f172a; margin-top: 2px; text-align: right; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; margin-top: 10px; }
        th { background: #1e293b; color: #ffffff; padding: 8px 10px; text-align: left; font-weight: 700; text-transform: uppercase; border: 1px solid #1e293b; }
        td { padding: 8px 10px; border: 1px solid #cbd5e1; color: #334155; vertical-align: middle; }
        tr:nth-child(even) { background: #f8fafc; }
        .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #e2e8f0; text-align: right; font-size: 9px; color: #94a3b8; font-weight: 600; }
        @page { size: landscape; margin: 12mm; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1 class="title">Reporte de Caja Chica y Recargas</h1>
            <p class="subtitle">Generado el ${dateStr} a las ${timeStr} · Módulo 1 (Personal)</p>
        </div>
        <div>
            <div class="badge">Documento Oficial</div>
            <div class="company">SISTEMA DE PAGOS Y CONTROL MINERO</div>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                ${headers.map(h => `<th>${h}</th>`).join('')}
            </tr>
        </thead>
        <tbody>
            ${rows.map(r => `
                <tr>
                    ${r.map(cell => `<td>${cell}</td>`).join('')}
                </tr>
            `).join('')}
        </tbody>
    </table>
    <div class="footer">
        Página 1 / 1 · Reporte Oficial de Fondo de Caja del Personal
    </div>
</body>
</html>`;

    const oldIframe = document.getElementById('fondos-print-iframe');
    if (oldIframe) oldIframe.remove();

    const iframe = document.createElement('iframe');
    iframe.id = 'fondos-print-iframe';
    iframe.style.position = 'fixed';
    iframe.style.right = '0';
    iframe.style.bottom = '0';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = 'none';
    iframe.style.visibility = 'hidden';
    document.body.appendChild(iframe);

    iframe.contentDocument.open();
    iframe.contentDocument.write(pdfHtml);
    iframe.contentDocument.close();

    setTimeout(() => {
        try {
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        } catch (e) {
            window.print();
        }
        setTimeout(() => {
            if (iframe.parentNode) iframe.remove();
            if (typeof window.hideProcessingOverlay === 'function') window.hideProcessingOverlay();
        }, 2500);
    }, 350);
};

// ─── Excel Export Caja Chica ─────────────────────────────────────────────────
window.doExportExcelCaja = function() {
    const tableContainer = document.getElementById('caja-chica-report-output');
    if (!tableContainer) return;
    const table = tableContainer.querySelector('table');
    if (!table) return;

    const aoa = [];
    const headers = Array.from(table.querySelectorAll('thead th'))
        .filter(th => !th.classList.contains('no-print'))
        .map(th => th.textContent.replace(/\s+/g, ' ').trim());
    aoa.push(headers);

    Array.from(table.querySelectorAll('tbody tr')).forEach(tr => {
        const rowData = Array.from(tr.querySelectorAll('td'))
            .filter((td, idx) => !td.classList.contains('no-print'))
            .map(td => td.textContent.replace(/\s+/g, ' ').trim());
        if (rowData.length > 0) {
            aoa.push(rowData);
        }
    });

    const filename = 'Reporte_Caja_Chica_' + new Date().toLocaleDateString('es-BO').replace(/\//g,'-') + '.csv';
    const csvLines = aoa.map(row => row.map(cell => `"${cell.replace(/"/g, '""')}"`).join(","));
    const csvContent = "\uFEFF" + csvLines.join("\r\n");
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
    setTimeout(() => {
        if (link.parentNode) document.body.removeChild(link);
        if (typeof window.hideProcessingOverlay === 'function') window.hideProcessingOverlay();
    }, 200);
};
</script>
@endpush
