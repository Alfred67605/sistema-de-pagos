@extends('layouts.app')

@section('title', 'Historial de Pagos')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100">Control de Caja y Pagos</h1>
            <p class="text-sm text-slate-400 mt-1">Monitorea los fondos retirados del banco, gestiona los pagos semanales y consulta el saldo disponible.</p>
        </div>
        <div class="flex flex-wrap gap-3 self-start">
            <a href="{{ route('fondos-caja.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-slate-800 border border-slate-700 hover:bg-slate-750 text-sm font-bold text-slate-200 transition duration-150">
                <i class="fa-solid fa-money-bill-trend-up mr-2 text-cyan-500"></i> Gestionar Fondos / Recargas
            </a>
            <a href="{{ route('pagos.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-sm font-bold text-slate-950 transition duration-150 shadow-lg shadow-teal-500/10">
                <i class="fa-solid fa-receipt mr-2"></i> Procesar Nuevo Pago
            </a>
        </div>
    </div>

    <!-- Summary Cards (3 Tarjetas Estilo Tablero Principal) -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <!-- Card 1: Gastado en Pagos Totales (Planillas) -->
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

        <!-- Card 2: Gastado en Anticipos -->
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

        <!-- Card 3: Saldo Sobrante en Caja -->
        @php
            $positivoIndex = $saldo_caja >= 0;
            $saldoGradientIndex = $positivoIndex ? 'from-emerald-500 to-teal-600' : 'from-amber-500 to-orange-650';
            $saldoTextIndex = $positivoIndex ? 'text-emerald-100/80' : 'text-amber-100/80';
        @endphp
        <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br {{ $saldoGradientIndex }} text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-white/90">Saldo Sobrante en Caja</span>
                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                    <i class="fa-solid fa-vault text-xs text-white"></i>
                </div>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight font-mono">Bs. {{ number_format(abs($saldo_caja), 2) }}</h2>
            <p class="text-[10px] {{ $saldoTextIndex }} font-medium mt-2">
                {{ $positivoIndex ? '● Efectivo físico disponible' : '▲ Caja en déficit' }}
            </p>
        </div>
    </div>

    <!-- Payouts List Table -->
    <div class="glass-card rounded-xl overflow-hidden">
        <div class="px-6 py-4 bg-slate-900/40 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-100 flex items-center">
                <i class="fa-solid fa-receipt mr-2 text-teal-500"></i> Historial de Liquidaciones de Pago
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800">
                <thead>
                    <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900/20">
                        <th class="px-6 py-4 font-semibold">ID</th>
                        <th class="px-6 py-4 font-semibold">Fecha</th>
                        <th class="px-6 py-4 font-semibold">Trabajador / Contratista</th>
                        <th class="px-6 py-4 font-semibold">Bocamina</th>
                        <th class="px-6 py-4 font-semibold">Trabajos (Subtotal)</th>
                        <th class="px-6 py-4 font-semibold">Bonos (+)</th>
                        <th class="px-6 py-4 font-semibold">Descuentos (-)</th>
                        <th class="px-6 py-4 font-semibold">Anticipos (-)</th>
                        <th class="px-6 py-4 font-semibold">Pago Neto</th>
                        <th class="px-6 py-4 font-semibold no-print">Comprobante</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                    @forelse($pagos as $pago)
                        <tr class="hover:bg-slate-900/10 transition duration-150">
                            <td class="px-6 py-4 font-mono text-xs">{{ $pago->id }}</td>
                            <td class="px-6 py-4 font-mono text-xs">{{ $pago->fecha->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 font-medium text-slate-100">{{ $pago->trabajador->nombre }}</td>
                            <td class="px-6 py-4 text-xs font-medium">{{ $pago->trabajador->bocamina ? $pago->trabajador->bocamina->nombre : 'Sin Bocamina' }}</td>
                            <td class="px-6 py-4 font-mono text-xs">Bs. {{ number_format($pago->subtotal, 2) }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-emerald-400">+Bs. {{ number_format($pago->bonos, 2) }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-red-400">-Bs. {{ number_format($pago->descuentos, 2) }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-red-400">-Bs. {{ number_format($pago->anticipos_descontados, 2) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-mono font-bold text-slate-100 text-sm">Bs. {{ number_format($pago->neto, 2) }}</span>
                                    @if($pago->saldo_pendiente > 0)
                                        <span class="text-[10px] text-amber-400 mt-0.5 font-mono">
                                            Efectivo Entregado: Bs. {{ number_format($pago->monto_pagado, 2) }}
                                        </span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold mt-1 self-start 
                                            @if($pago->saldo_liquidado) bg-slate-800 text-slate-400 border border-slate-700
                                            @else bg-amber-500/10 text-amber-400 border border-amber-500/20 @endif font-mono">
                                            @if($pago->saldo_liquidado)
                                                Adeudado: Bs. {{ number_format($pago->saldo_pendiente, 2) }} (Completado)
                                            @else
                                                Adeudado: Bs. {{ number_format($pago->saldo_pendiente, 2) }} (Pendiente)
                                            @endif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 mt-1 self-start font-mono">
                                            Completado
                                        </span>
                                        @if($pago->es_editado)
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[8.5px] font-extrabold bg-amber-500/10 text-amber-400 border border-amber-500/20 mt-1 self-start uppercase tracking-wider">
                                                <i class="fa-solid fa-pen-to-square mr-1 text-[8px]"></i> Editado
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 no-print">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('pagos.show', $pago->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-teal-400 text-xs font-medium transition duration-150" title="Ver / Imprimir Recibo">
                                        <i class="fa-solid fa-print mr-1.5"></i> Imprimir
                                    </a>
                                    <a href="{{ route('pagos.edit', $pago->id) }}" class="p-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-amber-400 transition duration-150" title="Editar Pago">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('pagos.destroy', $pago->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar este registro de pago? Al confirmar, el monto gastado se restaurará automáticamente en la Caja Personal.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-900/50 text-slate-400 hover:text-rose-400 transition duration-150" title="Eliminar Pago">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-receipt text-4xl mb-3 block text-slate-600"></i>
                                No se encontraron registros de pagos.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
