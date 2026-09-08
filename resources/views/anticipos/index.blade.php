@extends('layouts.app')

@section('title', 'Anticipos')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100">Historial de Anticipos (Adelantos)</h1>
            <p class="text-sm text-slate-400 mt-1">Historial de adelantos de dinero registrados. Se descuentan automáticamente en las liquidaciones semanales.</p>
        </div>
        <div class="no-print">
            <button onclick="document.getElementById('modalAnticipo').classList.remove('hidden'); setTimeout(() => { document.getElementById('modalAnticipo').classList.remove('modal-hide'); }, 10);" class="btn-vibrant-success px-5 py-2.5 rounded-xl font-bold text-sm flex items-center shadow-[0_0_15px_rgba(16,185,129,0.3)] transition-all">
                <i class="fa-solid fa-hand-holding-dollar mr-2"></i> Registrar Anticipo
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="glass-card rounded-xl p-6 no-print">
        <form id="filterFormAnticipos" action="{{ route('anticipos.index') }}" method="GET" onsubmit="event.preventDefault(); submitFilterRealTime(this);" class="grid grid-cols-1 gap-4 sm:grid-cols-4 items-end">
            <div>
                <label for="bocamina_id_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Bocamina</label>
                <select name="bocamina_id" id="bocamina_id_filter" 
                        onchange="submitFilterRealTime(this.form)"
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-sm">
                    <option value="">Todas las Bocaminas</option>
                    @foreach($bocaminas as $bocamina)
                        <option value="{{ $bocamina->id }}" {{ request('bocamina_id') == $bocamina->id ? 'selected' : '' }}>{{ $bocamina->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="trabajador_id_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Trabajador / Contratista</label>
                <select name="trabajador_id" id="trabajador_id_filter" 
                        onchange="submitFilterRealTime(this.form)"
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-sm">
                    <option value="">Todos los Trabajadores / Contratistas</option>
                    @foreach($trabajadores as $trabajador)
                        <option value="{{ $trabajador->id }}" {{ request('trabajador_id') == $trabajador->id ? 'selected' : '' }}>{{ $trabajador->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="estado_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Estado de Saldo</label>
                <select name="estado" id="estado_filter" 
                        onchange="submitFilterRealTime(this.form)"
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-rose-500 focus:border-rose-500 text-sm">
                    <option value="">Todos los Anticipos</option>
                    <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Con Saldo Pendiente</option>
                    <option value="pagado" {{ request('estado') === 'pagado' ? 'selected' : '' }}>Totalmente Descontados</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="button" onclick="limpiarFiltrosAnticipos()" class="btn-vibrant-warm flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-bold rounded-lg shadow-lg" title="Limpiar Filtros">
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
                        <th class="px-6 py-4 font-semibold">ID</th>
                        <th class="px-6 py-4 font-semibold">Fecha y Hora</th>
                        <th class="px-6 py-4 font-semibold">Trabajador / Contratista</th>
                        <th class="px-6 py-4 font-semibold">Bocamina</th>
                        <th class="px-6 py-4 font-semibold">Monto Original</th>
                        <th class="px-6 py-4 font-semibold">Saldo Restante</th>
                        <th class="px-6 py-4 font-semibold">Estado</th>
                        <th class="px-6 py-4 font-semibold no-print">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                    @forelse($anticipos as $anticipo)
                        <tr class="hover:bg-slate-900/10 transition duration-150">
                            <td class="px-6 py-4 font-mono text-xs">{{ $anticipo->id }}</td>
                            <td class="px-6 py-4 font-mono text-xs">
                                {{ $anticipo->fecha->format('d/m/Y') }}
                                <span class="text-[10px] text-slate-500 block">{{ $anticipo->created_at->format('H:i:s') }}</span>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-100">
                                <div>{{ $anticipo->trabajador->nombre }}</div>
                                @if($anticipo->dias_debe > 0)
                                    <span class="text-[10px] text-cyan-400 font-semibold inline-flex items-center gap-1 mt-0.5">
                                        <i class="fa-solid fa-calendar-day"></i> Debe {{ $anticipo->dias_debe }} día(s)
                                    </span>
                                @endif
                                @if($anticipo->observacion)
                                    <span class="text-[10px] text-slate-400 block italic truncate max-w-[200px]" title="{{ $anticipo->observacion }}">
                                        {{ $anticipo->observacion }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs">{{ $anticipo->trabajador->bocamina ? $anticipo->trabajador->bocamina->nombre : 'Sin Bocamina' }}</td>
                            <td class="px-6 py-4 font-mono font-medium text-slate-200">Bs. {{ number_format($anticipo->monto, 2) }}</td>
                            <td class="px-6 py-4 font-mono font-bold {{ $anticipo->saldo > 0 ? 'text-rose-450' : 'text-slate-400' }}">Bs. {{ number_format($anticipo->saldo, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $anticipo->saldo == 0 ? 'bg-slate-800 text-slate-400 border border-slate-700' : 'bg-red-500/10 text-red-400 border border-red-500/25' }}">
                                    {{ $anticipo->saldo == 0 ? 'Descontado' : 'Pendiente' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 no-print">
                                <div class="flex space-x-2">
                                    <a href="{{ route('anticipos.recibo', $anticipo->id) }}" target="_blank"
                                       class="p-1.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-emerald-500 transition duration-150" title="Imprimir Recibo de Anticipo">
                                        <i class="fa-solid fa-print text-xs"></i>
                                    </a>
                                    <form action="{{ route('anticipos.destroy', $anticipo->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar este anticipo? Al confirmar, el monto prestado se restaurará automáticamente en la Caja Personal.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded bg-slate-800 hover:bg-rose-900/50 text-slate-400 hover:text-rose-400 transition duration-150" title="Eliminar Anticipo">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-user-slash text-4xl mb-3 block text-slate-600"></i>
                                No se encontraron anticipos registrados con los filtros seleccionados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Nuevo Anticipo -->
    <div id="modalAnticipo" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/80 backdrop-blur-sm modal-hide transition-opacity duration-300">
        <div class="glass-card w-full max-w-lg rounded-2xl border border-slate-700/60 shadow-2xl overflow-hidden transform transition-transform duration-300 m-4">
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-bold text-white flex items-center">
                    <i class="fa-solid fa-hand-holding-dollar mr-2"></i> Registrar Nuevo Anticipo
                </h3>
                <button type="button" onclick="closeModalAnticipo()" class="text-white/70 hover:text-white transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <form action="{{ route('anticipos.store') }}" method="POST" class="p-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Trabajador <span class="text-rose-500">*</span></label>
                        <select name="trabajador_id" required class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                            <option value="">Seleccione un trabajador...</option>
                            @foreach($trabajadores as $t)
                                <option value="{{ $t->id }}">{{ $t->nombre }} {{ $t->bocamina ? '('.$t->bocamina->nombre.')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Fecha <span class="text-rose-500">*</span></label>
                            <input type="date" name="fecha" required value="{{ date('Y-m-d') }}" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Monto (Bs.) <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500 font-bold">Bs.</span>
                                <input type="number" step="0.01" min="1" name="monto" required placeholder="0.00" class="w-full pl-10 bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors font-mono">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Días de Trabajo que debe (Opcional)</label>
                        <div class="relative">
                            <input type="number" step="0.5" min="0" name="dias_debe" placeholder="Ej. 2 o 3 días" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors font-mono">
                            <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 text-xs font-semibold">días</span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-1.5">Observación (Opcional)</label>
                        <textarea name="observacion" rows="2" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-slate-200 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors" placeholder="Motivo del adelanto..."></textarea>
                    </div>
                </div>
                
                <div class="mt-6 pt-5 border-t border-slate-800 flex justify-end space-x-3">
                    <button type="button" onclick="closeModalAnticipo()" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-400 hover:text-slate-200 hover:bg-slate-800 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" class="btn-vibrant-success px-6 py-2.5 rounded-xl font-bold text-sm flex items-center shadow-lg">
                        <i class="fa-solid fa-save mr-2"></i> Guardar Anticipo
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function submitFilterRealTime(form) {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        const url = form.action + '?' + params;

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTable = doc.getElementById('table-container');
            if (newTable) {
                document.getElementById('table-container').innerHTML = newTable.innerHTML;
            }
            window.history.replaceState({}, '', url);
        })
        .catch(err => console.error('Error al filtrar en tiempo real:', err));
    }

    function limpiarFiltrosAnticipos() {
        document.getElementById('bocamina_id_filter').value = '';
        document.getElementById('trabajador_id_filter').value = '';
        document.getElementById('estado_filter').value = '';
        const form = document.getElementById('filterFormAnticipos');
        submitFilterRealTime(form);
    }

    function closeModalAnticipo() {
        const modal = document.getElementById('modalAnticipo');
        modal.classList.add('modal-hide');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 250);
    }
</script>
@endpush
