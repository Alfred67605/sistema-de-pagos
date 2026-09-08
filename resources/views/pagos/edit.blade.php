@extends('layouts.app')

@section('title', 'Editar Pago #' . str_pad($pago->id, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('pagos.index') }}" class="text-xs text-slate-400 hover:text-indigo-400 flex items-center font-medium transition">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Volver a Historial
            </a>
            <h1 class="text-2xl font-black text-slate-100 mt-1">Editar Registro de Pago #{{ str_pad($pago->id, 5, '0', STR_PAD_LEFT) }}</h1>
        </div>
    </div>

    <div class="glass-card rounded-2xl p-6 space-y-5 border border-slate-800">
        <form action="{{ route('pagos.update', $pago->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 grid grid-cols-2 gap-4 text-xs font-mono text-slate-300">
                <div>
                    <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Trabajador / Contratista:</span>
                    <span class="font-extrabold text-slate-100 text-sm block mt-0.5">{{ $pago->trabajador->nombre }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Bocamina:</span>
                    <span class="font-extrabold text-slate-100 text-sm block mt-0.5">{{ $pago->trabajador->bocamina->nombre ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Pago Neto Liquidado:</span>
                    <span class="font-extrabold text-emerald-400 text-base block mt-0.5">Bs. {{ number_format($pago->neto, 2) }}</span>
                </div>
                <div>
                    <span class="text-slate-500 block text-[9.5px] uppercase font-bold">Anticipos Descontados:</span>
                    <span class="font-extrabold text-rose-400 text-base block mt-0.5">Bs. {{ number_format($pago->anticipos_descontados, 2) }}</span>
                </div>
            </div>

            <div>
                <label for="fecha" class="block text-xs font-bold uppercase tracking-wider text-slate-400">Fecha del Pago</label>
                <input type="date" id="fecha" name="fecha" value="{{ old('fecha', $pago->fecha->format('Y-m-d')) }}" required
                       class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 text-sm font-mono focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <label for="monto_pagado" class="block text-xs font-bold uppercase tracking-wider text-slate-400">Monto Real Entregado en Efectivo (Bs.)</label>
                <input type="number" step="0.01" min="0" id="monto_pagado" name="monto_pagado" value="{{ old('monto_pagado', $pago->monto_pagado) }}" required
                       class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-emerald-400 text-base font-mono font-bold focus:outline-none focus:border-amber-500">
                <p class="text-[10px] text-slate-500 mt-1">Si el monto entregado coincide con el Pago Neto (Bs. {{ number_format($pago->neto, 2) }}), ingresa ese mismo valor.</p>
            </div>

            <div>
                <label for="tipo_cambio" class="block text-xs font-bold uppercase tracking-wider text-slate-400">Tipo de Cambio Dólar (Bs./USD)</label>
                <input type="number" step="0.01" min="0.01" id="tipo_cambio" name="tipo_cambio" value="{{ old('tipo_cambio', $pago->tipo_cambio ?? 6.96) }}" required
                       class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-emerald-400 text-sm font-mono font-bold focus:outline-none focus:border-amber-500">
                <p class="text-[10px] text-slate-500 mt-1">Valor para la conversión a $US en el recibo de pago.</p>
            </div>

            <div>
                <label for="observacion" class="block text-xs font-bold uppercase tracking-wider text-slate-400">Motivo / Observación de la Edición</label>
                <textarea id="observacion" name="observacion" rows="3" placeholder="Explica brevemente el motivo del ajuste..."
                          class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 text-xs focus:outline-none focus:border-amber-500">{{ old('observacion', $pago->observacion) }}</textarea>
            </div>

            <div class="pt-3 flex gap-3">
                <a href="{{ route('pagos.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs flex-1 text-center transition">
                    Cancelar
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white font-extrabold text-xs shadow-lg shadow-amber-500/20 flex-1 transition">
                    <i class="fa-solid fa-floppy-disk mr-1.5"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
