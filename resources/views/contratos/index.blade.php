@extends('layouts.app')

@section('title', 'Asignación de Contratos')

@section('content')
<div x-data="{ 
    openModal: false, 
    editMode: false, 
    contratoId: null,
    trabajador_id: '', 
    bocamina_id: '', 
    tipo_contrato_id: '',
    tarifa_acordada: '',
    estado: 'activo', 
    observaciones: '',
    editActionUrl: '',
    
    openCreate() {
        this.editMode = false;
        this.contratoId = null;
        this.trabajador_id = '';
        this.bocamina_id = '';
        this.tipo_contrato_id = '';
        this.tarifa_acordada = '';
        this.estado = 'activo';
        this.observaciones = '';
        this.openModal = true;
    },
    openEdit(contrato) {
        this.editMode = true;
        this.contratoId = contrato.id;
        this.trabajador_id = contrato.trabajador_id;
        this.bocamina_id = contrato.bocamina_id;
        this.tipo_contrato_id = contrato.tipo_contrato_id;
        this.tarifa_acordada = contrato.tarifa_acordada || '';
        this.estado = contrato.estado;
        this.observaciones = contrato.observaciones || '';
        this.editActionUrl = '/contratos/' + contrato.id;
        this.openModal = true;
    }
}" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-100 flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl flex items-center justify-center text-amber-400 bg-amber-500/10 border border-amber-500/20 flex-shrink-0">
                    <i class="fa-solid fa-file-contract text-base"></i>
                </span>
                Contratos de Personal
            </h1>
            <p class="text-xs md:text-sm text-slate-400 mt-1">Asigna trabajadores a frentes de trabajo (Bocaminas), define su tipo de contrato y acuerda su tarifa habitual.</p>
        </div>
        <button @click="openCreate()" class="btn-gold inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold text-slate-950 self-start cursor-pointer">
            <i class="fa-solid fa-plus mr-2"></i> Nueva Asignación
        </button>
    </div>

    <!-- Filters Section -->
    <div class="glass-card rounded-xl p-6 no-print">
        <form action="{{ route('contratos.index') }}" method="GET" onsubmit="event.preventDefault(); submitFilterRealTime(this);" class="grid grid-cols-1 gap-4 sm:grid-cols-4 items-end">
            <div>
                <label for="buscar" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Buscar por Trabajador</label>
                <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}" 
                       oninput="clearTimeout(searchDebounceTimeout); searchDebounceTimeout = setTimeout(() => submitFilterRealTime(this.form), 250)"
                       class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm"
                       placeholder="Ej. Juan Pérez...">
            </div>

            <div>
                <label for="bocamina_id_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Filtrar por Bocamina</label>
                <select name="bocamina_id" id="bocamina_id_filter" 
                        onchange="submitFilterRealTime(this.form)"
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="">Todas las Bocaminas</option>
                    @foreach($bocaminas as $bocamina)
                        <option value="{{ $bocamina->id }}" {{ request('bocamina_id') == $bocamina->id ? 'selected' : '' }}>{{ $bocamina->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="estado_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Estado</label>
                <select name="estado" id="estado_filter" 
                        onchange="submitFilterRealTime(this.form)"
                        class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="">Todos los Estados</option>
                    <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activos</option>
                    <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="button" onclick="document.getElementById('buscar').value = ''; document.getElementById('bocamina_id_filter').value = ''; document.getElementById('estado_filter').value = ''; submitFilterRealTime(this.form);" class="btn-secondary flex-1 inline-flex items-center justify-center px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl cursor-pointer" title="Limpiar Filtros">
                    <i class="fa-solid fa-rotate-left mr-2"></i> Limpiar
                </button>
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div id="table-container" class="glass-card shimmer-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800">
                <thead>
                    <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900/40">
                        <th class="px-6 py-4 font-semibold">Trabajador</th>
                        <th class="px-6 py-4 font-semibold">Bocamina</th>
                        <th class="px-6 py-4 font-semibold w-40">Cargo / Función</th>
                        <th class="px-6 py-4 font-semibold">Tipo Contrato</th>
                        <th class="px-6 py-4 font-semibold text-right">Tarifa Acordada</th>
                        <th class="px-6 py-4 font-semibold text-center w-36">Estado</th>
                        <th class="px-6 py-4 font-semibold no-print text-center w-36">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                    @forelse($contratos as $contrato)
                        <tr class="interactive-table-row">
                            <td class="px-6 py-4">
                                <div class="font-medium text-slate-100">{{ $contrato->trabajador->nombre }}</div>
                                <div class="text-xs text-slate-450 font-mono mt-0.5">CI: {{ $contrato->trabajador->ci ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-800/60 text-slate-300 border border-slate-700/50">
                                    <i class="fa-solid fa-mountain mr-1 text-sky-500 text-[10px]"></i> {{ $contrato->bocamina->nombre }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold capitalize bg-sky-500/10 text-sky-400 border border-sky-500/20">
                                    {{ $contrato->trabajador->rol ?: 'ayudante' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-200">
                                {{ $contrato->tipoContrato->nombre }}
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-xs font-bold text-slate-250">
                                {{ $contrato->tarifa_acordada ? 'Bs. ' . number_format($contrato->tarifa_acordada, 2) : '-' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $contrato->estado === 'activo' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/25' : 'bg-slate-800 text-slate-450 border border-slate-700' }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $contrato->estado === 'activo' ? 'bg-emerald-400 pulse-dot' : 'bg-slate-500' }}"></span>
                                    {{ $contrato->estado === 'activo' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 no-print text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="relative group/btn">
                                        <button @click="openEdit({{ $contrato }})" 
                                            class="w-8 h-8 rounded-xl flex items-center justify-center bg-gradient-to-br from-indigo-500 to-violet-600 hover:from-indigo-400 hover:to-violet-500 text-white shadow-md shadow-indigo-500/25 hover:shadow-indigo-500/50 hover:scale-110 active:scale-95 transition-all duration-200">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>
                                        <span class="absolute -bottom-7 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-lg bg-slate-900 text-[10px] font-bold text-slate-200 whitespace-nowrap opacity-0 group-hover/btn:opacity-100 transition-all duration-150 pointer-events-none border border-slate-700/60 shadow-xl z-50">Editar</span>
                                    </div>
                                    <div class="relative group/del">
                                        <form action="{{ route('contratos.destroy', $contrato->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="w-8 h-8 rounded-xl flex items-center justify-center bg-gradient-to-br from-rose-500 to-red-600 hover:from-rose-400 hover:to-red-500 text-white shadow-md shadow-rose-500/25 hover:shadow-rose-500/50 hover:scale-110 active:scale-95 transition-all duration-200">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                        <span class="absolute -bottom-7 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-lg bg-slate-900 text-[10px] font-bold text-rose-400 whitespace-nowrap opacity-0 group-hover/del:opacity-100 transition-all duration-150 pointer-events-none border border-rose-500/30 shadow-xl z-50">Eliminar</span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-file-invoice-dollar text-4xl mb-3 block text-slate-600"></i>
                                No se encontraron asignaciones de contratos registradas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- AlpineJS Modal (Create/Edit - ERP Assignment Form) -->
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div @click.away="openModal = false" class="glass-card w-full max-w-xl rounded-2xl overflow-hidden shadow-2xl border border-slate-800/80 relative">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-800/60 flex items-center justify-between bg-slate-900/60">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center">
                        <i class="fa-solid fa-file-invoice text-sm"></i>
                    </div>
                    <h3 class="text-md font-bold text-slate-100" x-text="editMode ? 'Ficha de Contrato: Editar' : 'Ficha de Contrato: Nueva Asignación'"></h3>
                </div>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form :action="editMode ? editActionUrl : '{{ route('contratos.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-6 space-y-4">
                    <!-- Trabajador -->
                    <div>
                        <label for="modal_trabajador" class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Trabajador <span class="text-red-500">*</span></label>
                        <select id="modal_trabajador" name="trabajador_id" required x-model="trabajador_id"
                                class="block w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-sm">
                            <option value="">Seleccione un trabajador...</option>
                            @foreach($trabajadores as $trabajador)
                                <option value="{{ $trabajador->id }}">{{ $trabajador->nombre }} ({{ $trabajador->rol ?: 'ayudante' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Bocamina -->
                        <div>
                            <label for="modal_bocamina" class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Bocamina Asignada <span class="text-red-500">*</span></label>
                            <select id="modal_bocamina" name="bocamina_id" required x-model="bocamina_id"
                                    class="block w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-sm">
                                <option value="">Seleccione una bocamina...</option>
                                @foreach($bocaminas as $bocamina)
                                    <option value="{{ $bocamina->id }}">{{ $bocamina->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tipo de Contrato -->
                        <div>
                            <label for="modal_tipo_contrato" class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Tipo de Contrato <span class="text-red-500">*</span></label>
                            <select id="modal_tipo_contrato" name="tipo_contrato_id" required x-model="tipo_contrato_id"
                                    class="block w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-sm">
                                <option value="">Seleccione un tipo...</option>
                                @foreach($tiposContrato as $tipo)
                                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tarifa Acordada -->
                        <div>
                            <label for="modal_tarifa" class="block text-[10px] font-bold uppercase tracking-widest text-slate-450 mb-1.5">Tarifa Acordada (Bs.)</label>
                            <div class="relative">
                                <input id="modal_tarifa" name="tarifa_acordada" type="number" step="0.01" min="0" x-model="tarifa_acordada"
                                       class="block w-full pl-3 pr-10 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-sm font-mono"
                                       placeholder="0.00">
                                <span class="absolute right-3 top-3 text-xs text-slate-500 font-mono">Bs.</span>
                            </div>
                            <span class="text-[9px] text-slate-500 mt-1 block">Tarifa habitual del trabajador. Sugerida en pagos.</span>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label for="modal_estado" class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Estado <span class="text-red-500">*</span></label>
                            <select id="modal_estado" name="estado" required x-model="estado"
                                    class="block w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-sm">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                            <span class="text-[9px] text-slate-500 mt-1 block">Si se activa, desactivará contratos previos del trabajador.</span>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label for="modal_observaciones" class="block text-[10px] font-bold uppercase tracking-widest text-slate-450 mb-1.5">Observaciones</label>
                        <textarea id="modal_observaciones" name="observaciones" rows="2" x-model="observaciones"
                                  class="block w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500 text-sm"
                                  placeholder="Detalles particulares del contrato..."></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-slate-800/60 bg-slate-900/40 flex justify-end space-x-3">
                    <button type="button" @click="openModal = false" class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-350 border border-slate-700/60 transition-all duration-150">
                        Cancelar
                    </button>
                    <button type="submit" 
                            :disabled="!trabajador_id || !bocamina_id || !tipo_contrato_id"
                            :class="(!trabajador_id || !bocamina_id || !tipo_contrato_id) ? 'opacity-50 cursor-not-allowed' : ''"
                            class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-xs font-bold uppercase tracking-wider text-white transition duration-150 shadow-lg shadow-sky-500/10">
                        Guardar Asignación
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
