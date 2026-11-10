@extends('layouts.app')

@section('title', 'Bocaminas')

@section('content')
<div x-data="{ 
    openModal: false, 
    editMode: false, 
    bocaminaId: null,
    nombre: '', 
    descripcion: '', 
    editActionUrl: '',
    openCreate() {
        this.editMode = false;
        this.bocaminaId = null;
        this.nombre = '';
        this.descripcion = '';
        this.openModal = true;
    },
    openEdit(bocamina) {
        this.editMode = true;
        this.bocaminaId = bocamina.id;
        this.nombre = bocamina.nombre;
        this.descripcion = bocamina.descripcion || '';
        this.editActionUrl = '/bocaminas/' + bocamina.id;
        this.openModal = true;
    }
}" class="space-y-6">

    <!-- Header with Contextual Background -->
    <div class="relative overflow-hidden rounded-2xl glass-card border border-orange-500/30 p-6 shadow-2xl">
        <div class="absolute inset-0 bg-cover bg-center opacity-25 mix-blend-luminosity pointer-events-none" style="background-image: url('{{ asset('images/mining/bocamina_tunnel.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900/90 to-slate-950/80 pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-orange-500/15 text-orange-400 border border-orange-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-400 pulse-dot mr-1.5"></span>
                        EXTRACCIÓN SUBTERRÁNEA
                    </span>
                    <span class="text-xs text-slate-500">•</span>
                    <span class="text-[11px] text-slate-400 font-mono">Frentes de Explotación</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-slate-100 flex items-center gap-3">
                    <span class="w-11 h-11 rounded-xl flex items-center justify-center text-orange-400 bg-orange-500/20 border border-orange-500/30 flex-shrink-0 shadow-md">
                        <i class="fa-solid fa-mountain text-lg"></i>
                    </span>
                    Administración de Bocaminas
                </h1>
                <p class="text-xs md:text-sm text-slate-400 mt-1">Registra y administra las diferentes bocaminas, socavones o frentes de extracción minera.</p>
            </div>
            <button @click="openCreate()" class="btn-gold px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider inline-flex items-center shadow-lg shadow-amber-500/20 self-start md:self-center hover:scale-105 active:scale-95 transition-all">
                <i class="fa-solid fa-plus mr-2 text-xs"></i> Nueva Bocamina
            </button>
        </div>
    </div>

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($bocaminas as $bocamina)
            <div class="group relative overflow-hidden mining-card interactive shimmer-card accent-border-copper glow-copper p-5 flex flex-col justify-between h-52">
                <!-- Contextual background photo -->
                <div class="absolute inset-0 bg-cover bg-center opacity-15 mix-blend-luminosity group-hover:scale-110 group-hover:opacity-25 transition-all duration-700 pointer-events-none" style="background-image: url('{{ asset('images/mining/bocamina_tunnel.jpg') }}');"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/85 to-slate-900/60 pointer-events-none"></div>

                <div class="relative z-10">
                    <div class="flex justify-between items-start">
                        <h3 class="text-base font-bold text-slate-100 group-hover:text-amber-400 transition-colors drop-shadow-sm">{{ $bocamina->nombre }}</h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30 backdrop-blur-sm shadow-sm">
                            Frente #{{ $bocamina->id }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-300 mt-2 line-clamp-2 leading-relaxed drop-shadow-sm">{{ $bocamina->descripcion ?: 'Frente de extracción y explotación mineral en socavón' }}</p>
                </div>
                
                <div class="relative z-10 flex items-center justify-between border-t border-white/10 pt-3 mt-4 w-full">
                    <a href="{{ route('trabajadores.index', ['bocamina_id' => $bocamina->id]) }}" class="text-xs text-slate-300 hover:text-amber-400 font-medium flex items-center transition-colors" title="Ver trabajadores de esta bocamina">
                        <i class="fa-solid fa-user-group mr-1.5 text-amber-400 text-[11px]"></i> {{ $bocamina->trabajadores_count }} en frente
                    </a>
                    <div class="flex items-center gap-1.5">
                        <button @click="openEdit({{ $bocamina }})" 
                            class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-500/15 hover:bg-amber-500/30 text-amber-400 border border-amber-500/30 transition-all hover:scale-105 active:scale-95 shadow-sm backdrop-blur-sm cursor-pointer"
                            title="Editar Bocamina">
                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                        </button>
                        <form action="{{ route('bocaminas.destroy', $bocamina->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta bocamina?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 transition-all hover:scale-105 active:scale-95 shadow-sm"
                                title="Eliminar Bocamina">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 flex flex-col items-center justify-center glass-card rounded-xl">
                <i class="fa-solid fa-mountain text-slate-600 text-5xl mb-4"></i>
                <p class="text-slate-400">No hay bocaminas registradas.</p>
                <button @click="openCreate()" class="text-xs text-emerald-500 underline font-medium mt-2 hover:text-amber-400">Registrar la primera bocamina</button>
            </div>
        @endforelse
    </div>

    <!-- AlpineJS Modal (Create/Edit) -->
    <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm" x-cloak>
        <div @click.away="openModal = false" class="glass-card w-full max-w-md rounded-2xl overflow-hidden shadow-2xl border border-slate-800/80 relative">
            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between bg-slate-900/60">
                <h3 class="text-lg font-bold text-slate-100" x-text="editMode ? 'Editar Bocamina' : 'Nueva Bocamina'"></h3>
                <button @click="openModal = false" class="text-slate-400 hover:text-slate-200">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form :action="editMode ? editActionUrl : '{{ route('bocaminas.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-6 space-y-4">
                    <div>
                        <label for="modal_nombre" class="block text-sm font-medium text-slate-300">Nombre de la Bocamina</label>
                        <input id="modal_nombre" name="nombre" type="text" required x-model="nombre"
                               class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-sm"
                               placeholder="Ej. Bocamina Rosario">
                    </div>
                    <div>
                        <label for="modal_descripcion" class="block text-sm font-medium text-slate-300">Descripción / Ubicación</label>
                        <textarea id="modal_descripcion" name="descripcion" rows="3" x-model="descripcion"
                                  class="mt-1 block w-full px-3 py-2 bg-slate-900 border border-slate-700 rounded-lg text-slate-100 focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-sm"
                                  placeholder="Detalles sobre la bocamina..."></textarea>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-slate-800/80 bg-slate-900/40 flex justify-end space-x-3">
                    <button type="button" @click="openModal = false" class="px-4 py-2 text-sm font-medium rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-bold rounded-lg bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-slate-950">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
