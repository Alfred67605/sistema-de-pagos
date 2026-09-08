@extends('layouts.app')

@section('title', 'Editar Pago de Servicio Externo')

@section('content')
<style>
    .se-card {
        background: rgba(15, 23, 42, 0.85);
        border: 1px solid rgba(51, 65, 85, 0.5);
        border-radius: 1.25rem;
        backdrop-filter: blur(16px);
    }
    .se-input {
        width: 100%;
        min-height: 44px;
        padding: 0.625rem 0.875rem;
        background-color: #0b1329;
        border: 1px solid #1e293b;
        border-radius: 0.75rem;
        color: #f8fafc;
        font-size: 0.875rem;
        font-weight: 500;
        box-sizing: border-box;
        transition: all 0.15s ease;
    }
    .se-input:focus {
        outline: none;
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.2);
    }
    .se-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.725rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #94a3b8;
        margin-bottom: 0.4rem;
    }
</style>

<div class="max-w-6xl mx-auto space-y-6">

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('servicios-externos.index') }}" class="text-xs text-slate-400 hover:text-sky-400 inline-flex items-center font-bold transition mb-1">
                <i class="fa-solid fa-arrow-left mr-2"></i> Volver a Historial
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-100 flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white flex-shrink-0 shadow-lg shadow-amber-500/20">
                    <i class="fa-solid fa-pen-to-square text-base"></i>
                </span>
                Editar Pago de Servicio Externo #{{ str_pad($servicio->id, 5, '0', STR_PAD_LEFT) }}
            </h1>
        </div>
    </div>

    <!-- Main Form Card -->
    <form action="{{ route('servicios-externos.update', $servicio->id) }}" method="POST" class="se-card p-6 sm:p-8 space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Sección Izquierda: Información de Maquinaria y Chofer -->
            <div class="space-y-6">
                <div class="border-b border-slate-800 pb-3 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-truck-monster"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-100 uppercase tracking-wider">1. Maquinaria y Chofer / Operador</h3>
                        <p class="text-[11px] text-slate-400">Datos fundamentales del servicio y proveedor</p>
                    </div>
                </div>

                {{-- Tipo de Servicio --}}
                <div>
                    <label class="se-label">
                        <span><i class="fa-solid fa-layer-group text-sky-400 mr-1.5"></i> Tipo de Servicio <span class="text-rose-400">*</span></span>
                    </label>
                    <select name="tipo_servicio" class="se-input font-bold text-sky-400" required>
                        <option value="Volqueta / Transporte de Mineral" {{ $servicio->tipo_servicio === 'Volqueta / Transporte de Mineral' ? 'selected' : '' }}>🚚 Volqueta / Transporte de Mineral (Flete)</option>
                        <option value="Excavadora" {{ $servicio->tipo_servicio === 'Excavadora' ? 'selected' : '' }}>🚜 Excavadora (Por hora/periodo)</option>
                        <option value="Gallinita / Retroexcavadora" {{ $servicio->tipo_servicio === 'Gallinita / Retroexcavadora' ? 'selected' : '' }}>🚜 Gallinita / Retroexcavadora</option>
                        <option value="Tractor Oruga" {{ $servicio->tipo_servicio === 'Tractor Oruga' ? 'selected' : '' }}>🏗️ Tractor Oruga</option>
                        <option value="Mantenimiento de Maquinaria" {{ $servicio->tipo_servicio === 'Mantenimiento de Maquinaria' ? 'selected' : '' }}>⚙️ Mantenimiento de Maquinaria</option>
                        <option value="Otro Servicio" {{ $servicio->tipo_servicio === 'Otro Servicio' ? 'selected' : '' }}>📑 Otro Servicio Externo</option>
                    </select>
                </div>

                {{-- Chofer / Operador / Proveedor --}}
                <div>
                    <label class="se-label">
                        <span><i class="fa-solid fa-user-gear text-sky-400 mr-1.5"></i> Chofer / Operador / Proveedor <span class="text-rose-400">*</span></span>
                        <span class="text-[10px] text-slate-500 font-mono">Fundamental</span>
                    </label>
                    <input type="text" name="chofer_operador" value="{{ old('chofer_operador', $servicio->chofer_operador) }}" required
                           class="se-input font-bold text-slate-100">
                </div>

                {{-- Placa / Código de Maquinaria --}}
                <div>
                    <label class="se-label">
                        <span><i class="fa-solid fa-id-card text-amber-400 mr-1.5"></i> Placa / N.º de Maquinaria <span class="text-rose-400">*</span></span>
                        <span class="text-[10px] text-slate-500 font-mono">Fundamental</span>
                    </label>
                    <input type="text" name="placa_maquinaria" value="{{ old('placa_maquinaria', $servicio->placa_maquinaria) }}" required
                           class="se-input font-mono text-amber-400 font-black uppercase text-base tracking-wider">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Bocamina asociada --}}
                    <div>
                        <label class="se-label">
                            <span><i class="fa-solid fa-mountain text-emerald-400 mr-1.5"></i> Bocamina (Opcional)</span>
                        </label>
                        <select name="bocamina_id" class="se-input">
                            <option value="">— Ninguna / General —</option>
                            @foreach($bocaminas as $b)
                                <option value="{{ $b->id }}" {{ $servicio->bocamina_id == $b->id ? 'selected' : '' }}>{{ $b->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Origen - Destino --}}
                    <div>
                        <label class="se-label">
                            <span><i class="fa-solid fa-route text-cyan-400 mr-1.5"></i> Tramo Origen / Destino</span>
                        </label>
                        <input type="text" name="origen_destino" value="{{ old('origen_destino', $servicio->origen_destino) }}"
                               class="se-input text-xs">
                    </div>
                </div>
            </div>

            <!-- Sección Derecha: Liquidación, Cantidad y Montos -->
            <div class="space-y-6">
                <div class="border-b border-slate-800 pb-3 flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm font-bold">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-100 uppercase tracking-wider">2. Liquidación y Monto Pagado</h3>
                        <p class="text-[11px] text-slate-400">Detalle de pago y deducción de caja chica</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Fecha --}}
                    <div>
                        <label class="se-label">
                            <span><i class="fa-solid fa-calendar-day text-slate-400 mr-1.5"></i> Fecha del Pago <span class="text-rose-400">*</span></span>
                        </label>
                        <input type="date" name="fecha" value="{{ old('fecha', $servicio->fecha->format('Y-m-d')) }}" required class="se-input font-mono">
                    </div>

                    {{-- N.º de Comprobante --}}
                    <div>
                        <label class="se-label">
                            <span><i class="fa-solid fa-receipt text-slate-400 mr-1.5"></i> N.º Comprobante / Recibo</span>
                        </label>
                        <input type="text" name="numero_comprobante" value="{{ old('numero_comprobante', $servicio->numero_comprobante) }}" class="se-input font-mono uppercase">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Cantidad --}}
                    <div>
                        <label class="se-label">
                            <span><i class="fa-solid fa-hashtag text-sky-400 mr-1.5"></i> Cantidad <span class="text-rose-400">*</span></span>
                        </label>
                        <input type="number" step="0.01" min="0.01" name="cantidad" value="{{ old('cantidad', $servicio->cantidad) }}" required class="se-input font-mono font-bold text-sky-400">
                    </div>

                    {{-- Unidad de Medida --}}
                    <div>
                        <label class="se-label">
                            <span><i class="fa-solid fa-ruler-horizontal text-sky-400 mr-1.5"></i> Unidad de Medida <span class="text-rose-400">*</span></span>
                        </label>
                        <select name="unidad_medida" class="se-input font-semibold">
                            <option value="viajes" {{ $servicio->unidad_medida === 'viajes' ? 'selected' : '' }}>Viajes</option>
                            <option value="horas" {{ $servicio->unidad_medida === 'horas' ? 'selected' : '' }}>Horas</option>
                            <option value="días" {{ $servicio->unidad_medida === 'días' ? 'selected' : '' }}>Días</option>
                            <option value="servicio" {{ $servicio->unidad_medida === 'servicio' ? 'selected' : '' }}>Servicio Global</option>
                            <option value="volquetadas" {{ $servicio->unidad_medida === 'volquetadas' ? 'selected' : '' }}>Volquetadas</option>
                        </select>
                    </div>
                </div>

                {{-- Precio Unitario --}}
                <div>
                    <label class="se-label">
                        <span><i class="fa-solid fa-tag text-slate-400 mr-1.5"></i> Tarifa / Precio Unitario (Bs.)</span>
                    </label>
                    <input type="number" step="0.01" min="0" name="precio_unitario" value="{{ old('precio_unitario', $servicio->precio_unitario) }}" class="se-input font-mono">
                </div>

                {{-- Monto Total Pagado (FUNDAMENTAL) --}}
                <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-inner">
                    <label class="se-label mb-2">
                        <span class="text-emerald-400 font-extrabold text-xs"><i class="fa-solid fa-money-bill-wave mr-1.5"></i> Monto Total a Pagar (Bs.) <span class="text-rose-400">*</span></span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-base text-emerald-400 font-bold font-mono">Bs.</span>
                        <input type="number" step="0.01" min="0.01" name="monto_total" value="{{ old('monto_total', $servicio->monto_total) }}" required
                               class="se-input !pl-12 text-xl font-mono font-black text-emerald-400 bg-slate-950/80 border-emerald-500/40">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Forma de Pago --}}
                    <div>
                        <label class="se-label">
                            <span><i class="fa-solid fa-wallet text-sky-400 mr-1.5"></i> Forma de Pago</span>
                        </label>
                        <select name="metodo_pago" class="se-input font-bold text-sky-400">
                            <option value="efectivo" {{ $servicio->metodo_pago === 'efectivo' ? 'selected' : '' }}>💵 Efectivo</option>
                            <option value="cheque" {{ $servicio->metodo_pago === 'cheque' ? 'selected' : '' }}>📋 Cheque</option>
                            <option value="transferencia" {{ $servicio->metodo_pago === 'transferencia' ? 'selected' : '' }}>🏦 Transferencia</option>
                        </select>
                    </div>

                    {{-- Entregado Por --}}
                    <div>
                        <label class="se-label">
                            <span><i class="fa-solid fa-user-check text-slate-400 mr-1.5"></i> Entregado Por</span>
                        </label>
                        <input type="text" name="entregado_por" value="{{ old('entregado_por', $servicio->entregado_por) }}" class="se-input text-xs">
                    </div>
                </div>

                {{-- Observación / Detalle --}}
                <div>
                    <label class="se-label">
                        <span><i class="fa-solid fa-comment-dots text-slate-400 mr-1.5"></i> Observaciones / Detalle Adicional</span>
                    </label>
                    <textarea name="observacion" rows="2" class="se-input text-xs resize-none">{{ old('observacion', $servicio->observacion) }}</textarea>
                </div>

            </div>

        </div>

        <!-- Submit Action Bar -->
        <div class="pt-6 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-end gap-3">
            <a href="{{ route('servicios-externos.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-extrabold text-xs uppercase tracking-wider transition text-center">
                Cancelar
            </a>
            <button type="submit" class="w-full sm:w-auto px-8 py-3 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-extrabold text-xs uppercase tracking-wider transition shadow-lg shadow-sky-500/20 cursor-pointer">
                <i class="fa-solid fa-save mr-2"></i> Actualizar Servicio
            </button>
        </div>

    </form>

</div>
@endsection
