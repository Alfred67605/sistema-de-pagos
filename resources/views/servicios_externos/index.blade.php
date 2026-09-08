@extends('layouts.app')

@section('title', 'Pago de Servicios Externos')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100 flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl flex items-center justify-center text-white flex-shrink-0" style="background:linear-gradient(135deg,#0284c7,#0369a1);">
                    <i class="fa-solid fa-truck-front text-sm"></i>
                </span>
                Pago de Servicios Externos
            </h1>
            <p class="text-sm text-slate-400 mt-1">Registra los pagos por servicios externos (flete de volqueta para transporte de mineral, excavadora, gallinita, maquinaria, etc.). Se descuentan directamente de la Caja Chica.</p>
        </div>
        <div class="flex flex-wrap gap-3 self-start">
            <a href="{{ route('servicios-externos.create') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-sm font-bold text-white transition duration-150 shadow-lg shadow-sky-500/10 cursor-pointer">
                <i class="fa-solid fa-plus mr-2"></i> Registrar Nuevo Servicio
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="glass-card rounded-2xl p-5 border border-sky-500/20 bg-sky-500/5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-sky-400 uppercase tracking-wider">Total Gastado en Servicios</span>
                <i class="fa-solid fa-truck-ramp-box text-sky-400 text-lg"></i>
            </div>
            <div class="text-2xl font-black font-mono text-slate-100 mt-2">Bs. {{ number_format($total_gastado_servicios, 2) }}</div>
            <p class="text-[10px] text-slate-400 mt-1">Pagos a volquetas, choferes y maquinaria externa</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-emerald-500/20 bg-emerald-500/5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Saldo Sobrante en Caja Chica</span>
                <i class="fa-solid fa-vault text-emerald-400 text-lg"></i>
            </div>
            <div class="text-2xl font-black font-mono text-emerald-400 mt-2">Bs. {{ number_format($saldo_caja, 2) }}</div>
            <p class="text-[10px] text-slate-400 mt-1">Disponible tras descontar planilla, anticipos y servicios</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="glass-card rounded-xl p-6 no-print">
        <form action="{{ route('servicios-externos.index') }}" method="GET" onsubmit="event.preventDefault(); submitFilterRealTime(this);" class="grid grid-cols-1 gap-4 sm:grid-cols-4 items-end">
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

            <div>
                <label for="fecha_desde" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Fecha Desde</label>
                <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}"
                       onchange="submitFilterRealTime(this.form)"
                       class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-sky-500 text-sm">
            </div>

            <div class="flex space-x-2">
                <button type="button" onclick="document.getElementById('buscar').value = ''; document.getElementById('tipo_servicio_filter').value = ''; document.getElementById('fecha_desde').value = ''; submitFilterRealTime(this.form);" class="btn-vibrant-warm flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-bold rounded-lg shadow-lg cursor-pointer" title="Limpiar Filtros">
                    <i class="fa-solid fa-rotate-left mr-2"></i> Limpiar
                </button>
            </div>
        </form>
    </div>

    <!-- Table Section -->
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
                        <th class="px-6 py-4 font-semibold no-print text-center w-36">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                    @forelse($servicios as $item)
                        <tr class="hover:bg-slate-900/10 transition duration-150">
                            <td class="px-6 py-4 font-mono text-slate-400 font-bold text-xs">{{ str_pad($item->id, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4 font-mono text-xs">
                                <div>{{ $item->fecha->format('d/m/Y') }}</div>
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
                            <td class="px-6 py-4 font-bold text-slate-100 flex items-center gap-2">
                                <i class="fa-solid fa-user-gear text-slate-400 text-xs"></i>
                                {{ $item->chofer_operador }}
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
                                        <button type="submit" class="p-1.5 rounded-lg bg-slate-800 hover:bg-rose-900/50 text-slate-400 hover:text-rose-400 transition duration-150" title="Eliminar">
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
            </table>
        </div>
    </div>

</div>
@endsection
