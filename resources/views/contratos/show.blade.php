@extends('layouts.app')

@section('title', 'Detalle de Contrato - ' . $contrato->codigo)

@section('content')
<div class="space-y-6 page-fade-in">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-print pb-2 border-b border-white/5">
        <div>
            <a href="{{ route('contratos.index') }}" class="group text-xs text-slate-400 hover:text-amber-400 inline-flex items-center font-semibold transition-colors duration-200">
                <i class="fa-solid fa-arrow-left mr-2 transition-transform duration-200 group-hover:-translate-x-1 text-amber-500"></i> Volver a Contratos
            </a>
            <div class="flex items-center space-x-3 mt-1.5 flex-wrap gap-y-2">
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-100 flex items-center gap-2.5">
                    <span class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i class="fa-solid fa-file-contract text-base"></i>
                    </span>
                    <span>Contrato:</span>
                    <span class="font-mono text-amber-400">{{ $contrato->codigo }}</span>
                </h1>
                
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-mono font-bold
                    @if($contrato->estado === 'activo') bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 shadow-[0_0_10px_rgba(16,185,129,0.15)]
                    @elseif($contrato->estado === 'finalizado') bg-blue-500/15 text-blue-400 border border-blue-500/30
                    @else bg-slate-800 text-slate-400 border border-slate-700 @endif">
                    <span class="w-2 h-2 rounded-full mr-2 {{ $contrato->estado === 'activo' ? 'bg-emerald-400 pulse-dot' : 'bg-slate-500' }}"></span>
                    {{ strtoupper($contrato->estado) }}
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2.5 self-start sm:self-center">
            <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-slate-900/80 hover:bg-slate-800 border border-slate-700/80 text-xs font-bold text-slate-200 transition-all shadow-md inline-flex items-center hover:border-amber-500/40 cursor-pointer active:scale-95">
                <i class="fa-solid fa-print mr-2 text-amber-400"></i> Imprimir Reporte
            </button>
        </div>
    </div>

    <!-- Print Only Header -->
    <div class="hidden print-only mb-6 text-slate-900">
        <div class="text-center">
            <h1 class="text-2xl font-bold uppercase tracking-wider">Reporte Detallado de Contrato Minero</h1>
            <p class="text-sm font-mono mt-1">Código: {{ $contrato->codigo }} | Generado el {{ now()->format('d/m/Y H:i') }}</p>
        </div>
        <hr class="border-slate-300 my-4">
    </div>

    <!-- 3 Jewel-Toned Cards Grid -->
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        
        <!-- Card 1: Contrato Info (Sapphire Cyan Accent) -->
        <div class="mining-card interactive shimmer-card accent-border-cyan glow-cyan p-6 relative overflow-hidden print:border print:border-slate-300 print:text-slate-900 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-white/5 pb-3 mb-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200 flex items-center print:text-slate-900">
                        <i class="fa-solid fa-file-invoice mr-2 text-cyan-400 no-print"></i> Datos del Contrato
                    </h3>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                        Especificación
                    </span>
                </div>

                <div class="space-y-3.5 text-xs font-mono">
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-sans font-semibold tracking-wider block">Descripción del Trabajo</span>
                        <span class="text-slate-100 font-sans text-sm font-medium mt-0.5 block print:text-slate-900">{{ $contrato->descripcion }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-1 border-t border-white/5">
                        <div class="p-2.5 rounded-lg bg-white/[0.02] border border-white/5">
                            <span class="text-[9px] text-slate-400 uppercase font-sans block">Fecha Inicio</span>
                            <span class="text-slate-200 font-bold text-xs mt-0.5 block print:text-slate-900">
                                <i class="fa-regular fa-calendar text-[10px] text-cyan-400 mr-1"></i>
                                {{ Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }}
                            </span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-white/[0.02] border border-white/5">
                            <span class="text-[9px] text-slate-400 uppercase font-sans block">Fecha Fin</span>
                            <span class="text-slate-200 font-bold text-xs mt-0.5 block print:text-slate-900">
                                <i class="fa-regular fa-calendar-check text-[10px] text-cyan-400 mr-1"></i>
                                {{ $contrato->fecha_fin ? Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') : 'Abierto' }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-2.5 rounded-lg bg-white/[0.02] border border-white/5">
                            <span class="text-[9px] text-slate-400 uppercase font-sans block">Modalidad de Pago</span>
                            <span class="text-cyan-300 capitalize font-bold text-xs mt-0.5 block font-sans print:text-slate-900">
                                @if($contrato->tipo_pago === 'monto_fijo')
                                    Fijo / Destajo
                                @elseif(in_array($contrato->tipo_pago, ['metro', 'volqueta', 'tonelada', 'saco']))
                                    Por {{ ucfirst($contrato->tipo_pago) }}
                                @else
                                    {{ ucfirst($contrato->tipo_pago) }}
                                @endif
                            </span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-white/[0.02] border border-white/5">
                            <span class="text-[9px] text-slate-400 uppercase font-sans block">Precio Unitario</span>
                            <span class="text-amber-400 font-bold text-xs mt-0.5 block print:text-slate-900">
                                {{ $contrato->precio_unitario ? 'Bs. ' . number_format($contrato->precio_unitario, 2) : '-' }}
                            </span>
                        </div>
                    </div>

                    @if($contrato->avance_estimado_semanal)
                    <div class="p-2.5 rounded-lg bg-cyan-500/5 border border-cyan-500/15">
                        <span class="text-[9px] text-cyan-400 uppercase font-sans font-bold block">Meta Semanal Estimada</span>
                        <span class="text-slate-200 font-semibold text-xs mt-0.5 block font-sans print:text-slate-900">
                            {{ number_format($contrato->avance_estimado_semanal, 1) }} 
                            {{ $contrato->tipo_pago === 'metro' ? 'Metros' : ($contrato->tipo_pago === 'volqueta' ? 'Volquetas' : ($contrato->tipo_pago === 'tonelada' ? 'Toneladas' : ($contrato->tipo_pago === 'saco' ? 'Sacos' : $contrato->tipo_pago))) }} / semana
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 2: Trabajador & Bocamina (Amber Gold Accent) -->
        <div class="mining-card interactive shimmer-card accent-border-gold glow-gold p-6 relative overflow-hidden print:border print:border-slate-300 print:text-slate-900 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-white/5 pb-3 mb-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200 flex items-center print:text-slate-900">
                        <i class="fa-solid fa-people-carry-box mr-2 text-amber-400 no-print"></i> Asignación en Mina
                    </h3>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                        Frente Activo
                    </span>
                </div>

                <div class="space-y-4 text-sm">
                    <!-- Trabajador Profile Pill -->
                    <div class="p-3.5 rounded-xl bg-white/[0.02] border border-white/5 hover:border-amber-500/25 transition-colors">
                        <span class="text-[10px] text-slate-400 uppercase font-mono tracking-wider block">Trabajador Responsable</span>
                        <div class="flex items-center space-x-3 mt-1.5">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center font-bold text-sm shadow-sm flex-shrink-0">
                                {{ substr($contrato->trabajador->nombre, 0, 1) }}
                            </div>
                            <div class="min-w-0">
                                <span class="text-slate-100 font-extrabold text-sm block truncate print:text-slate-900">{{ $contrato->trabajador->nombre }}</span>
                                <span class="text-[11px] text-slate-400 font-mono block mt-0.5">
                                    CI: {{ $contrato->trabajador->ci ?: 'S/N' }} • Tel: {{ $contrato->trabajador->telefono ?: 'S/N' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Bocamina Card -->
                    <div class="p-3.5 rounded-xl bg-white/[0.02] border border-white/5 hover:border-orange-500/25 transition-colors">
                        <span class="text-[10px] text-slate-400 uppercase font-mono tracking-wider block">Frente de Extracción (Bocamina)</span>
                        <div class="flex items-center space-x-3 mt-1.5">
                            <div class="w-10 h-10 rounded-xl bg-orange-500/15 border border-orange-500/30 text-orange-400 flex items-center justify-center text-sm shadow-sm flex-shrink-0">
                                <i class="fa-solid fa-mountain text-base"></i>
                            </div>
                            <div class="min-w-0">
                                <span class="text-slate-100 font-extrabold text-sm block truncate print:text-slate-900">{{ $contrato->bocamina->nombre }}</span>
                                <span class="text-[11px] text-orange-400/90 font-mono block mt-0.5">
                                    Frente ID #{{ $contrato->bocamina->id }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Avance Financiero (Emerald Vault Accent) -->
        <div class="mining-card interactive shimmer-card accent-border-emerald glow-emerald p-6 relative overflow-hidden print:border print:border-slate-300 print:text-slate-900 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-white/5 pb-3 mb-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-200 flex items-center print:text-slate-900">
                        <i class="fa-solid fa-chart-line mr-2 text-emerald-400 no-print"></i> Avance Financiero
                    </h3>
                    <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        Liquidación
                    </span>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-end p-3 rounded-xl bg-white/[0.02] border border-white/5">
                        <div class="font-mono">
                            <span class="text-[10px] text-slate-400 uppercase font-sans block">Presupuestado</span>
                            <span class="text-lg font-black text-slate-100 print:text-slate-900">Bs. {{ number_format($contrato->monto_total, 2) }}</span>
                        </div>
                        <div class="font-mono text-right">
                            <span class="text-[10px] text-emerald-400 uppercase font-sans font-bold block">Ejecutado</span>
                            <span class="text-lg font-black text-emerald-400">Bs. {{ number_format($contrato->avance_monto, 2) }}</span>
                        </div>
                    </div>
                    
                    <div>
                        <div class="w-full bg-slate-900 rounded-full h-3.5 relative overflow-hidden p-0.5 border border-white/5 print:bg-slate-200">
                            <div class="bg-gradient-to-r from-emerald-500 via-teal-400 to-amber-500 animated-progress-bar h-full rounded-full transition-all duration-700 ease-out shadow-sm" style="width: {{ min($contrato->avance_porcentaje, 100) }}%"></div>
                        </div>
                        <div class="flex justify-between items-center text-xs text-slate-400 font-mono mt-2">
                            <span>Restante: <strong class="text-slate-200">Bs. {{ number_format(max($contrato->monto_total - $contrato->avance_monto, 0), 2) }}</strong></span>
                            <span class="font-black text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">{{ $contrato->avance_porcentaje }}% completado</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Works logged under this contract -->
    <div class="glass-card shimmer-card rounded-2xl overflow-hidden print:border print:border-slate-300 print:text-slate-900">
        <div class="px-6 py-4 bg-slate-900/40 border-b border-white/5 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-100 flex items-center print:text-slate-900">
                <i class="fa-solid fa-list-check mr-2 text-amber-400 no-print"></i> Trabajos Ejecutados bajo este Contrato
            </h3>
            <span class="text-xs font-mono text-slate-400">
                Total Registros: <strong class="text-slate-200">{{ $contrato->trabajos->count() }}</strong>
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800 print:divide-slate-300">
                <thead>
                    <tr class="text-left text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-900/30 print:bg-slate-100 print:text-slate-600">
                        <th class="px-5 py-3.5">ID</th>
                        <th class="px-5 py-3.5">Fecha</th>
                        <th class="px-5 py-3.5">Tipo / Unidad</th>
                        <th class="px-5 py-3.5 text-right">Cantidad</th>
                        <th class="px-5 py-3.5 text-right">Precio Unitario</th>
                        <th class="px-5 py-3.5 text-right">Subtotal</th>
                        <th class="px-5 py-3.5">Observación</th>
                        <th class="px-5 py-3.5 text-center">Estado de Pago</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 print:divide-slate-200 text-sm text-slate-300 print:text-slate-800">
                    @forelse($contrato->trabajos as $trabajo)
                        <tr class="interactive-table-row">
                            <td class="px-5 py-4 font-mono text-xs font-bold text-sky-400">#{{ $trabajo->id }}</td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-300">{{ $trabajo->fecha->format('d/m/Y') }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold capitalize bg-white/5 border border-white/10 text-slate-200">
                                    {{ $trabajo->tipo }}
                                </span>
                            </td>
                            <td class="px-5 py-4 font-mono text-right text-slate-200 font-semibold">{{ $trabajo->cantidad }}</td>
                            <td class="px-5 py-4 font-mono text-right text-slate-300">Bs. {{ number_format($trabajo->precio_unitario, 2) }}</td>
                            <td class="px-5 py-4 font-mono font-black text-right text-emerald-400 tabular-nums">Bs. {{ number_format($trabajo->subtotal, 2) }}</td>
                            <td class="px-5 py-4 font-sans text-xs text-slate-400">{{ $trabajo->observacion ?: '-' }}</td>
                            <td class="px-5 py-4 text-center">
                                @if($trabajo->pagado)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/25 print:bg-emerald-50 print:text-emerald-700">
                                        <i class="fa-solid fa-circle-check text-[10px] mr-1.5"></i> Pagado (ID #{{ $trabajo->pago_id }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/25 print:bg-amber-50 print:text-amber-700">
                                        <i class="fa-regular fa-clock text-[10px] mr-1.5"></i> Pendiente
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-file-invoice text-3xl mb-2 text-slate-600 block"></i>
                                No se han registrado trabajos ejecutados para este contrato.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
