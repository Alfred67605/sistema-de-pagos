@extends('layouts.app')

@section('title', 'Detalle de Contrato - ' . $contrato->codigo)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between no-print">
        <div>
            <a href="{{ route('contratos.index') }}" class="text-xs text-slate-400 hover:text-amber-500 flex items-center font-medium transition duration-150">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Volver a Contratos
            </a>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100 mt-1">Detalle de Contrato: {{ $contrato->codigo }}</h1>
        </div>
        <button type="button" onclick="window.print()" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg bg-slate-800 border border-slate-700/80 hover:bg-slate-700 text-sm font-medium text-slate-200 transition duration-150 cursor-pointer">
            <i class="fa-solid fa-print mr-2 text-amber-500"></i> Imprimir Reporte de Contrato
        </button>
    </div>

    <!-- Print Only Header -->
    <div class="hidden print-only mb-6 text-slate-900">
        <div class="text-center">
            <h1 class="text-2xl font-bold uppercase tracking-wider">Reporte Detallado de Contrato</h1>
            <p class="text-sm font-mono mt-1">Código: {{ $contrato->codigo }} | Generado el {{ now()->format('d/m/Y H:i') }}</p>
        </div>
        <hr class="border-slate-300 my-4">
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Card 1: Contrato Info -->
        <div class="glass-card rounded-xl p-6 relative overflow-hidden print:border print:border-slate-300 print:text-slate-900">
            <h3 class="text-md font-bold text-slate-200 border-b border-slate-800 pb-3 mb-4 flex items-center print:text-slate-900 print:border-slate-200">
                <i class="fa-solid fa-file-invoice mr-2 text-amber-500 no-print"></i> Datos del Contrato
            </h3>
            <div class="space-y-3 font-mono text-sm">
                <div>
                    <span class="text-xs text-slate-500 uppercase block print:text-slate-500">Descripción</span>
                    <span class="text-slate-200 font-sans print:text-slate-900">{{ $contrato->descripcion }}</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-xs text-slate-500 uppercase block">Fecha Inicio</span>
                        <span class="text-slate-200 font-sans print:text-slate-900">{{ Carbon\Carbon::parse($contrato->fecha_inicio)->format('d/m/Y') }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 uppercase block">Fecha Fin</span>
                        <span class="text-slate-200 font-sans print:text-slate-900">{{ $contrato->fecha_fin ? Carbon\Carbon::parse($contrato->fecha_fin)->format('d/m/Y') : 'Abierto' }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-xs text-slate-500 uppercase block">Tipo Pago</span>
                        <span class="text-slate-200 capitalize font-sans print:text-slate-900">
                            @if($contrato->tipo_pago === 'monto_fijo')
                                Fijo / Destajo
                            @elseif(in_array($contrato->tipo_pago, ['metro', 'volqueta', 'tonelada', 'saco']))
                                Por {{ ucfirst($contrato->tipo_pago) }}
                            @else
                                {{ ucfirst($contrato->tipo_pago) }}
                            @endif
                        </span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 uppercase block">Precio Unitario</span>
                        <span class="text-slate-200 print:text-slate-900">{{ $contrato->precio_unitario ? 'Bs. ' . number_format($contrato->precio_unitario, 2) : '-' }}</span>
                    </div>
                </div>
                @if($contrato->avance_estimado_semanal)
                <div>
                    <span class="text-xs text-slate-500 uppercase block">Avance Semanal Estimado</span>
                    <span class="text-slate-200 print:text-slate-900 font-sans">
                        {{ number_format($contrato->avance_estimado_semanal, 1) }} 
                        {{ $contrato->tipo_pago === 'metro' ? 'Metros' : ($contrato->tipo_pago === 'volqueta' ? 'Volquetas' : ($contrato->tipo_pago === 'tonelada' ? 'Toneladas' : ($contrato->tipo_pago === 'saco' ? 'Sacos' : $contrato->tipo_pago))) }} por semana
                    </span>
                </div>
                @endif
                <div>
                    <span class="text-xs text-slate-500 uppercase block">Estado</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold mt-1
                        @if($contrato->estado === 'activo') bg-emerald-500/10 text-emerald-400 border border-emerald-500/25
                        @elseif($contrato->estado === 'finalizado') bg-blue-500/10 text-blue-400 border border-blue-500/25
                        @else bg-slate-800 text-slate-400 border border-slate-700 @endif">
                        {{ ucfirst($contrato->estado) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 2: Trabajador & Bocamina Info -->
        <div class="glass-card rounded-xl p-6 relative overflow-hidden print:border print:border-slate-300 print:text-slate-900">
            <h3 class="text-md font-bold text-slate-200 border-b border-slate-800 pb-3 mb-4 flex items-center print:text-slate-900 print:border-slate-200">
                <i class="fa-solid fa-people-carry-box mr-2 text-amber-500 no-print"></i> Asignación
            </h3>
            <div class="space-y-4 text-sm">
                <div>
                    <span class="text-xs text-slate-500 uppercase font-mono block">Trabajador Responsable</span>
                    <span class="text-slate-200 font-bold block print:text-slate-900">{{ $contrato->trabajador->nombre }}</span>
                    <span class="text-xs text-slate-400 font-mono block mt-0.5">CI: {{ $contrato->trabajador->ci }} | Tel: {{ $contrato->trabajador->telefono ?: '-' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 uppercase font-mono block">Bocamina</span>
                    <span class="text-slate-200 font-bold block flex items-center print:text-slate-900">
                        <i class="fa-solid fa-mountain mr-2 text-amber-500 no-print"></i> {{ $contrato->bocamina->nombre }}
                    </span>
                    <span class="text-xs text-slate-400 block mt-0.5 font-mono">Bocamina ID: {{ $contrato->bocamina->id }}</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Avance Financiero -->
        <div class="glass-card rounded-xl p-6 relative overflow-hidden print:border print:border-slate-300 print:text-slate-900 flex flex-col justify-between">
            <div>
                <h3 class="text-md font-bold text-slate-200 border-b border-slate-800 pb-3 mb-4 flex items-center print:text-slate-900 print:border-slate-200">
                    <i class="fa-solid fa-chart-line mr-2 text-amber-500 no-print"></i> Avance Financiero
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-end">
                        <div class="font-mono">
                            <span class="text-xs text-slate-500 uppercase block">Presupuestado</span>
                            <span class="text-xl font-bold text-slate-200 print:text-slate-900">Bs. {{ number_format($contrato->monto_total, 2) }}</span>
                        </div>
                        <div class="font-mono text-right">
                            <span class="text-xs text-slate-500 uppercase block">Ejecutado</span>
                            <span class="text-xl font-bold text-amber-500">Bs. {{ number_format($contrato->avance_monto, 2) }}</span>
                        </div>
                    </div>
                    
                    <div>
                        <div class="w-full bg-slate-800 rounded-full h-3 relative print:bg-slate-200">
                            <div class="bg-gradient-to-r from-amber-500 to-orange-500 h-3 rounded-full" style="width: {{ $contrato->avance_porcentaje }}%"></div>
                        </div>
                        <div class="flex justify-between items-center text-xs text-slate-400 font-mono mt-1.5">
                            <span>Monto Restante: Bs. {{ number_format(max($contrato->monto_total - $contrato->avance_monto, 0), 2) }}</span>
                            <span class="font-bold text-amber-500">{{ $contrato->avance_porcentaje }}% completado</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Works logged under this contract -->
    <div class="glass-card rounded-xl p-6 print:border print:border-slate-300 print:text-slate-900">
        <h3 class="text-lg font-semibold text-slate-100 mb-4 flex items-center print:text-slate-900 border-b border-slate-850 pb-3">
            <i class="fa-solid fa-list-check mr-2 text-amber-500 no-print"></i> Trabajos Ejecutados bajo este Contrato
        </h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800 print:divide-slate-300">
                <thead>
                    <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900/20 print:bg-slate-100 print:text-slate-600">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Tipo / Unidad</th>
                        <th class="px-4 py-3">Cantidad</th>
                        <th class="px-4 py-3">Precio Unitario</th>
                        <th class="px-4 py-3">Subtotal</th>
                        <th class="px-4 py-3">Observación</th>
                        <th class="px-4 py-3">Estado de Pago</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/30 print:divide-slate-200 text-sm text-slate-300 print:text-slate-800">
                    @forelse($contrato->trabajos as $trabajo)
                        <tr>
                            <td class="px-4 py-3.5 font-mono text-xs">{{ $trabajo->id }}</td>
                            <td class="px-4 py-3.5 font-mono">{{ $trabajo->fecha->format('d/m/Y') }}</td>
                            <td class="px-4 py-3.5 capitalize">{{ $trabajo->tipo }}</td>
                            <td class="px-4 py-3.5 font-mono">{{ $trabajo->cantidad }}</td>
                            <td class="px-4 py-3.5 font-mono">Bs. {{ number_format($trabajo->precio_unitario, 2) }}</td>
                            <td class="px-4 py-3.5 font-mono font-medium text-amber-500">Bs. {{ number_format($trabajo->subtotal, 2) }}</td>
                            <td class="px-4 py-3.5 font-sans">{{ $trabajo->observacion ?: '-' }}</td>
                            <td class="px-4 py-3.5">
                                @if($trabajo->pagado)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/25 print:bg-emerald-50 print:text-emerald-700">
                                        Pagado (ID Pago: {{ $trabajo->pago_id }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/25 print:bg-amber-50 print:text-amber-700">
                                        Pendiente
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500">
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
