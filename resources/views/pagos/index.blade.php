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
        <div class="flex flex-wrap gap-2.5 self-start">
            <a href="{{ route('fondos-caja.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-bold uppercase tracking-wider text-slate-200 transition-colors shadow-sm">
                <i class="fa-solid fa-money-bill-trend-up mr-2 text-amber-500"></i> Gestionar Fondos / Recargas
            </a>
            <a href="{{ route('pagos.create') }}" class="btn-gold px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider inline-flex items-center shadow-md">
                <i class="fa-solid fa-receipt mr-2 text-xs"></i> Procesar Nuevo Pago
            </a>
        </div>
    </div>

    <!-- Summary Cards (3 Tarjetas con Fondos Fotográficos Contextuales) -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <!-- Card 1: Gastado en Pagos Totales (Planillas) -->
        <div class="group relative overflow-hidden mining-card interactive shimmer-card accent-border-gold glow-gold p-5">
            <!-- Contextual photo -->
            <div class="absolute inset-0 bg-cover bg-center opacity-25 mix-blend-luminosity group-hover:scale-110 group-hover:opacity-35 transition-all duration-700 pointer-events-none" style="background-image: url('{{ asset('images/mining/mining_payroll.jpg') }}');"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950/85 via-amber-950/40 to-slate-950/90 pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-amber-200/90">Total Liquidaciones</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-300 shadow-sm backdrop-blur-sm">
                        <i class="fa-solid fa-receipt text-xs"></i>
                    </div>
                </div>
                <div class="text-2xl font-extrabold tracking-tight font-mono text-white tabular-nums drop-shadow">
                    Bs. {{ number_format($total_gastado_pagos, 2) }}
                </div>
                <p class="text-[11px] text-amber-200/80 mt-2 flex items-center">
                    <i class="fa-solid fa-check-double text-[9px] mr-1.5 text-emerald-400"></i> Liquidación final de planillas
                </p>
            </div>
        </div>

        <!-- Card 2: Gastado en Anticipos -->
        <div class="group relative overflow-hidden mining-card interactive shimmer-card accent-border-rose glow-rose p-5">
            <!-- Contextual photo -->
            <div class="absolute inset-0 bg-cover bg-center opacity-25 mix-blend-luminosity group-hover:scale-110 group-hover:opacity-35 transition-all duration-700 pointer-events-none" style="background-image: url('{{ asset('images/mining/cash_advance.jpg') }}');"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950/85 via-rose-950/40 to-slate-950/90 pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-200/90">Total Anticipos</span>
                    <div class="w-8 h-8 rounded-lg bg-rose-500/20 border border-rose-500/40 flex items-center justify-center text-rose-300 shadow-sm backdrop-blur-sm">
                        <i class="fa-solid fa-hand-holding-dollar text-xs"></i>
                    </div>
                </div>
                <div class="text-2xl font-extrabold tracking-tight font-mono text-rose-300 tabular-nums drop-shadow">
                    Bs. {{ number_format($total_gastado_anticipos, 2) }}
                </div>
                <p class="text-[11px] text-rose-200/80 mt-2 flex items-center">
                    <i class="fa-solid fa-arrow-down text-[9px] mr-1.5 text-rose-400"></i> Adelantos entregados a cuenta
                </p>
            </div>
        </div>

        <!-- Card 3: Saldo Sobrante en Caja -->
        @php
            $positivoIndex = $saldo_caja >= 0;
        @endphp
        <div class="group relative overflow-hidden mining-card interactive shimmer-card {{ $positivoIndex ? 'accent-border-emerald glow-emerald' : 'accent-border-rose glow-rose' }} p-5">
            <!-- Contextual photo -->
            <div class="absolute inset-0 bg-cover bg-center opacity-25 mix-blend-luminosity group-hover:scale-110 group-hover:opacity-35 transition-all duration-700 pointer-events-none" style="background-image: url('{{ asset('images/mining/vault_safe.jpg') }}');"></div>
            <div class="absolute inset-0 bg-gradient-to-br {{ $positivoIndex ? 'from-slate-950/85 via-emerald-950/40 to-slate-950/90' : 'from-slate-950/85 via-rose-950/40 to-slate-950/90' }} pointer-events-none"></div>

            <div class="relative z-10">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-[11px] font-bold uppercase tracking-wider {{ $positivoIndex ? 'text-emerald-200/90' : 'text-rose-200/90' }}">Saldo en Caja Chica</span>
                    <div class="w-8 h-8 rounded-lg {{ $positivoIndex ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40' }} flex items-center justify-center shadow-sm backdrop-blur-sm">
                        <i class="fa-solid fa-vault text-xs"></i>
                    </div>
                </div>
                <div class="text-2xl font-extrabold tracking-tight font-mono tabular-nums text-white drop-shadow">
                    Bs. {{ number_format(abs($saldo_caja), 2) }}
                </div>
                <p class="text-[11px] font-medium mt-2 flex items-center {{ $positivoIndex ? 'text-emerald-300' : 'text-rose-300' }}">
                    <i class="fa-solid {{ $positivoIndex ? 'fa-circle-check text-emerald-400' : 'fa-triangle-exclamation text-rose-400' }} text-[10px] mr-1.5"></i>
                    {{ $positivoIndex ? 'Efectivo disponible en caja' : 'Caja con saldo deudor' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Payouts List Table -->
    <div class="glass-card shimmer-card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 bg-slate-900/40 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-100 flex items-center">
                <i class="fa-solid fa-receipt mr-2 text-emerald-400"></i> Historial de Liquidaciones de Pago
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
                        <tr class="interactive-table-row">
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
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold mt-1 self-start 
                                            @if($pago->saldo_liquidado) bg-slate-800 text-slate-400 border border-slate-700
                                            @else bg-amber-500/15 text-amber-300 border border-amber-500/30 @endif font-mono">
                                            @if($pago->saldo_liquidado)
                                                Empresa Debe: Bs. {{ number_format($pago->saldo_pendiente, 2) }} (Saldado)
                                            @else
                                                ⚠️ Empresa Debe: Bs. {{ number_format($pago->saldo_pendiente, 2) }} (Pendiente)
                                            @endif
                                        </span>
                                    @elseif($pago->monto_pagado > $pago->neto)
                                        <span class="text-[10px] text-cyan-400 mt-0.5 font-mono">
                                            Efectivo Entregado: Bs. {{ number_format($pago->monto_pagado, 2) }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 mt-1 self-start font-mono">
                                            <i class="fa-solid fa-hand-holding-dollar mr-1"></i> Trabajador Debe: Bs. {{ number_format($pago->monto_pagado - $pago->neto, 2) }} (Adelanto)
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 mt-1 self-start font-mono">
                                            <i class="fa-solid fa-check mr-1"></i> Al día / Saldado
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
                                    <form action="{{ route('pagos.destroy', $pago->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar este registro de pago #{{ $pago->id }}? Al eliminarlo, los anticipos descontados se restaurarán automáticamente en la cuenta del trabajador y cualquier adelanto extra generado será revertido.')">
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
