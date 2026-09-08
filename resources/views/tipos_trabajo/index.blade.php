@extends('layouts.app')

@section('title', 'Tipos de Trabajo')

@section('content')
<div x-data="{ 
    openModal: false, 
    editMode: false, 
    tipoId: null,
    nombre: '', 
    descripcion: '',
    estado: 'activo', 
    editActionUrl: '',
    
    openCreate() {
        this.editMode = false;
        this.tipoId = null;
        this.nombre = '';
        this.descripcion = '';
        this.estado = 'activo';
        this.openModal = true;
    },
    openEdit(item) {
        this.editMode = true;
        this.tipoId = item.id;
        this.nombre = item.nombre;
        this.descripcion = item.descripcion || '';
        this.estado = item.estado;
        this.editActionUrl = '/tipos-trabajo/' + item.id;
        this.openModal = true;
    }
}" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100 flex items-center gap-3">
                <span class="w-10 h-10 rounded-2xl flex items-center justify-center text-white flex-shrink-0" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                    <i class="fa-solid fa-hammer text-sm"></i>
                </span>
                Catálogo de Tipos de Trabajo
            </h1>
            <p class="text-sm text-slate-400 mt-1">Configura las actividades laborales (Jornal, Carguío, Descarguío, Transporte, Mantenimiento, etc.) para los pagos.</p>
        </div>
        <button @click="openCreate()" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-sm font-bold text-slate-950 transition duration-150 shadow-lg shadow-amber-500/10 self-start cursor-pointer">
            <i class="fa-solid fa-plus mr-2"></i> Nuevo Tipo de Trabajo
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="glass-card rounded-2xl p-5 border border-amber-500/20 bg-amber-500/5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider">Total Registrados</span>
                <i class="fa-solid fa-layer-group text-amber-400 text-lg"></i>
            </div>
            <div class="text-2xl font-black font-mono text-slate-100 mt-2">{{ count($tiposTrabajo) }}</div>
            <p class="text-[10px] text-slate-400 mt-1">Tipos de trabajo en catálogo</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-emerald-500/20 bg-emerald-500/5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Activos</span>
                <i class="fa-solid fa-circle-check text-emerald-400 text-lg"></i>
            </div>
            <div class="text-2xl font-black font-mono text-emerald-400 mt-2">{{ $totalActivos }}</div>
            <p class="text-[10px] text-slate-400 mt-1">Disponibles para selección en pagos</p>
        </div>

        <div class="glass-card rounded-2xl p-5 border border-slate-700/50 bg-slate-900/30">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Inactivos / Desactivados</span>
                <i class="fa-solid fa-circle-minus text-slate-500 text-lg"></i>
            </div>
            <div class="text-2xl font-black font-mono text-slate-400 mt-2">{{ $totalInactivos }}</div>
            <p class="text-[10px] text-slate-500 mt-1">Deshabilitados temporalmente</p>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="glass-card rounded-xl p-6 no-print">
        <form action="{{ route('tipos-trabajo.index') }}" method="GET" onsubmit="event.preventDefault(); submitFilterRealTime(this);" class="grid grid-cols-1 gap-4 sm:grid-cols-3 items-end">
            <div>
                <label for="buscar" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Buscar por Nombre o Descripción</label>
                <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}" 
                       oninput="clearTimeout(searchDebounceTimeout); searchDebounceTimeout = setTimeout(() => submitFilterRealTime(this.form), 250)"
                       class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700/80 rounded-lg text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm"
                       placeholder="Ej. Jornal, Carguío, Transporte...">
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
                <button type="button" onclick="document.getElementById('buscar').value = ''; document.getElementById('estado_filter').value = ''; submitFilterRealTime(this.form);" class="btn-vibrant-warm flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-bold rounded-lg shadow-lg cursor-pointer" title="Limpiar Filtros">
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
                        <th class="px-6 py-4 font-semibold w-24">ID</th>
                        <th class="px-6 py-4 font-semibold">Tipo de Trabajo</th>
                        <th class="px-6 py-4 font-semibold">Descripción</th>
                        <th class="px-6 py-4 font-semibold text-center w-40">Estado</th>
                        <th class="px-6 py-4 font-semibold no-print text-center w-40">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                    @forelse($tiposTrabajo as $item)
                        <tr class="hover:bg-slate-900/10 transition duration-150">
                            <td class="px-6 py-4 font-mono text-slate-400 font-bold text-xs">{{ str_pad($item->id, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4 font-bold text-slate-100 flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-xs">
                                    <i class="fa-solid fa-wrench"></i>
                                </div>
                                {{ $item->nombre }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-400">
                                {{ $item->descripcion ?: 'Sin descripción adicional' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $item->estado === 'activo' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/25' : 'bg-slate-800 text-slate-450 border border-slate-700' }}">
                                    {{ $item->estado === 'activo' ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 no-print text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="relative group/btn">
                                        <button @click="openEdit({{ $item }})" 
                                            class="w-8 h-8 rounded-xl flex items-center justify-center bg-gradient-to-br from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-slate-950 font-bold shadow-md shadow-amber-500/25 hover:scale-110 active:scale-95 transition-all duration-200 cursor-pointer">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>
                                        <span class="absolute -bottom-7 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-lg bg-slate-900 text-[10px] font-bold text-slate-200 whitespace-nowrap opacity-0 group-hover/btn:opacity-100 transition-all duration-150 pointer-events-none border border-slate-700/60 shadow-xl z-50">Editar</span>
                                    </div>
                                    <div class="relative group/del">
                                        <form action="{{ route('tipos-trabajo.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Cambiar estado o eliminar este tipo de trabajo?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="w-8 h-8 rounded-xl flex items-center justify-center {{ $item->estado === 'activo' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30 hover:bg-rose-500 hover:text-white' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500 hover:text-white' }} shadow-md hover:scale-110 active:scale-95 transition-all duration-200 cursor-pointer"
                                                title="{{ $item->estado === 'activo' ? 'Desactivar' : 'Eliminar / Activar' }}">
                                                <i class="fa-solid {{ $item->estado === 'activo' ? 'fa-power-off' : 'fa-trash' }} text-xs"></i>
                                            </button>
                                        </form>
                                        <span class="absolute -bottom-7 left-1/2 -translate-x-1/2 px-2 py-0.5 rounded-lg bg-slate-900 text-[10px] font-bold text-slate-200 whitespace-nowrap opacity-0 group-hover/del:opacity-100 transition-all duration-150 pointer-events-none border border-slate-700/60 shadow-xl z-50">
                                            {{ $item->estado === 'activo' ? 'Desactivar' : 'Eliminar' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-hammer text-4xl mb-3 block text-slate-600 opacity-40"></i>
                                No se encontraron tipos de trabajo registrados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- AlpineJS Modal (Create/Edit) -->
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div @click.away="openModal = false" class="glass-card w-full max-w-md rounded-2xl overflow-hidden shadow-2xl border border-slate-800/80 relative">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-800/60 flex items-center justify-between bg-slate-900/60">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                        <i class="fa-solid fa-hammer text-sm"></i>
                    </div>
                    <h3 class="text-md font-bold text-slate-100" x-text="editMode ? 'Editar Tipo de Trabajo' : 'Nuevo Tipo de Trabajo'"></h3>
                </div>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-200 cursor-pointer">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form :action="editMode ? editActionUrl : '{{ route('tipos-trabajo.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-6 space-y-4">
                    <div>
                        <label for="modal_nombre" class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Nombre del Tipo de Trabajo <span class="text-rose-400">*</span></label>
                        <input id="modal_nombre" name="nombre" type="text" required x-model="nombre"
                               class="block w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm"
                               placeholder="Ej. Jornal, Carguío, Transporte, Mantenimiento...">
                    </div>

                    <div>
                        <label for="modal_descripcion" class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Descripción (Opcional)</label>
                        <textarea id="modal_descripcion" name="descripcion" x-model="descripcion" rows="2"
                                  class="block w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-xs resize-none"
                                  placeholder="Ej. Pago por jornal realizado en bocamina..."></textarea>
                    </div>

                    <div>
                        <label for="modal_estado" class="block text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">Estado <span class="text-rose-400">*</span></label>
                        <select id="modal_estado" name="estado" required x-model="estado"
                                class="block w-full px-3 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-200 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                            <option value="activo">Activo (Disponible en Pagos)</option>
                            <option value="inactivo">Desactivado (Inactivo)</option>
                        </select>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-slate-800/60 bg-slate-900/40 flex justify-end space-x-3">
                    <button type="button" @click="openModal = false" class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-350 border border-slate-700/60 transition-all duration-150 cursor-pointer">
                        Cancelar
                    </button>
                    <button type="submit" 
                            :disabled="!nombre"
                            :class="(!nombre) ? 'opacity-50 cursor-not-allowed' : ''"
                            class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-xs font-bold uppercase tracking-wider text-slate-950 transition duration-150 shadow-lg shadow-amber-500/10 cursor-pointer">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
