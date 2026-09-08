@extends('layouts.app')

@section('title', 'Procesar Pago')

@section('content')
<style>
/* ══════════════════════════════════════════════════════════════
   SISTEMA PAGOS PERSONAL — FORMULARIO MULTI-ÍTEM
   ══════════════════════════════════════════════════════════════ */
:root {
    --p-bg: #070c19;
    --p-card: rgba(12,18,38,0.8);
    --p-border: rgba(148,163,184,0.08);
    --p-text: #f1f5f9;
    --p-muted: #64748b;
    --p-teal: #14b8a6;
    --p-indigo: #6366f1;
    --p-emerald: #10b981;
    --p-amber: #f59e0b;
    --p-rose: #f43f5e;
}
.p-card {
    background: var(--p-card);
    border: 1px solid var(--p-border);
    border-radius: 16px;
    backdrop-filter: blur(12px);
}
.p-label {
    display: block;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--p-muted);
    margin-bottom: 5px;
}
.p-input {
    width: 100%;
    padding: 9px 13px;
    background: rgba(7,12,25,0.7);
    border: 1px solid var(--p-border);
    border-radius: 10px;
    color: var(--p-text);
    font-size: 13px;
    font-weight: 500;
    outline: none;
    transition: all 0.15s ease;
    appearance: none;
    box-sizing: border-box;
}
.p-input:focus { border-color: #14b8a6; box-shadow: 0 0 0 3px rgba(20,184,166,0.15); }
.p-input::placeholder { color: #334155; }
.p-input option { background: #0f172a; color: #f1f5f9; }

.p-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 18px; border-radius: 10px;
    font-size: 11px; font-weight: 800; text-transform: uppercase;
    letter-spacing: 0.06em; border: none; cursor: pointer;
    transition: all 0.15s ease;
}
.p-btn:active { transform: scale(0.97); }
.p-btn-teal { background: linear-gradient(135deg,#14b8a6,#0d9488); color:#fff; box-shadow: 0 4px 12px rgba(20,184,166,0.3); }
.p-btn-teal:hover { background: linear-gradient(135deg,#0d9488,#0f766e); }
.p-btn-ghost { background: rgba(255,255,255,0.05); color:#94a3b8; border:1px solid var(--p-border); }
.p-btn-ghost:hover { background: rgba(255,255,255,0.09); color:#f1f5f9; }
.p-btn-rose { background: rgba(244,63,94,0.1); color:#f43f5e; border:1px solid rgba(244,63,94,0.2); }
.p-btn-rose:hover { background: rgba(244,63,94,0.2); }
.p-btn-icon { padding: 7px; border-radius: 8px; }

/* Item de pago / línea */
.item-row {
    background: rgba(7,12,25,0.5);
    border: 1px solid rgba(148,163,184,0.1);
    border-radius: 14px;
    padding: 16px;
    position: relative;
    transition: border-color 0.2s;
}
.item-row:hover { border-color: rgba(20,184,166,0.25); }
.item-row-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 12px; padding-bottom: 10px;
    border-bottom: 1px dashed rgba(148,163,184,0.08);
}

/* Badge */
.p-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 99px;
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.05em; border: 1px solid transparent;
}
.p-badge-teal    { background: rgba(20,184,166,0.12); color:#14b8a6; border-color: rgba(20,184,166,0.25); }
.p-badge-amber   { background: rgba(245,158,11,0.12); color:#f59e0b; border-color: rgba(245,158,11,0.25); }
.p-badge-emerald { background: rgba(16,185,129,0.12); color:#10b981; border-color: rgba(16,185,129,0.25); }
.p-badge-rose    { background: rgba(244,63,94,0.12);  color:#f43f5e; border-color: rgba(244,63,94,0.25); }
.p-badge-indigo  { background: rgba(99,102,241,0.12); color:#818cf8; border-color: rgba(99,102,241,0.25); }

/* Totales */
.total-box {
    background: linear-gradient(135deg, rgba(20,184,166,0.08), rgba(16,185,129,0.06));
    border: 1px solid rgba(20,184,166,0.25);
    border-radius: 14px;
    padding: 18px 22px;
}
.total-row {
    display: flex; justify-content: space-between; align-items: center;
    font-size: 12px; padding: 5px 0;
    border-bottom: 1px solid rgba(148,163,184,0.05);
}
.total-row:last-child { border-bottom: none; }

/* Light theme overrides */
.light-theme .p-card { background: #ffffff; border-color: #e2e8f0; }
.light-theme .p-input { background: #f8fafc; border-color: #e2e8f0; color: #0f172a; }
.light-theme .p-input option { background: #fff; color: #0f172a; }
.light-theme .item-row { background: #f8fafc; border-color: #e2e8f0; }
.light-theme .item-row-header { border-color: #e2e8f0; }
.light-theme .total-box { background: rgba(20,184,166,0.04); border-color: rgba(20,184,166,0.2); }
.light-theme .total-row { border-color: #f1f5f9; }
</style>

<div x-data="pagoWizard()" class="space-y-6">

    {{-- Header --}}
    <div>
        <a href="{{ route('pagos.index') }}" class="text-xs text-slate-400 hover:text-teal-400 flex items-center font-medium transition">
            <i class="fa-solid fa-arrow-left mr-1.5"></i> Volver al Historial
        </a>
        <h1 class="text-2xl font-black tracking-tight text-slate-100 mt-1 flex items-center gap-3">
            <span class="w-10 h-10 rounded-2xl flex items-center justify-center text-white flex-shrink-0"
                  style="background:linear-gradient(135deg,#14b8a6,#0d9488);">
                <i class="fa-solid fa-file-invoice-dollar text-sm"></i>
            </span>
            Procesar Pago al Personal
        </h1>
        <p class="text-sm text-slate-400 mt-1 ml-[52px]">Registra uno o varios trabajos dentro del mismo pago con sus jornales, contratos y montos.</p>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/25 text-rose-400 text-xs font-semibold space-y-1">
            @foreach($errors->all() as $err)
                <div class="flex items-start gap-2"><i class="fa-solid fa-circle-exclamation mt-0.5 flex-shrink-0"></i>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('pagos.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf

        {{-- ══════════ COL IZQUIERDA: Info General ══════════ --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- Saldo en Caja --}}
            <div class="p-3.5 rounded-xl border {{ $saldo_caja >= 0 ? 'bg-emerald-500/5 border-emerald-500/20 text-emerald-400' : 'bg-rose-500/5 border-rose-500/20 text-rose-400' }} flex items-center justify-between text-xs font-semibold">
                <span><i class="fa-solid fa-vault mr-2"></i> Saldo en Caja:</span>
                <span class="font-mono font-bold text-sm">Bs. {{ number_format($saldo_caja, 2) }}</span>
            </div>

            <div class="p-card p-5 space-y-4">
                <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2 border-b border-slate-800/60 pb-3">
                    <i class="fa-solid fa-circle-info text-teal-400"></i> Datos Generales del Pago
                </h3>

                {{-- Filtro Bocamina --}}
                <div>
                    <label class="p-label">Filtrar por Bocamina</label>
                    <select x-model="bocaminaFiltroId" @change="trabajadorId = ''; limpiarWorker()" class="p-input">
                        <option value="">— Todas las Bocaminas —</option>
                        @foreach($bocaminas as $b)
                            <option value="{{ $b->id }}">{{ $b->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Seleccionar Trabajador --}}
                <div>
                    <label class="p-label">Trabajador <span class="text-rose-400">*</span></label>
                    <select name="trabajador_id" required x-model="trabajadorId" @change="onTrabajadorChange()" class="p-input font-bold">
                        <option value="">— Seleccionar Trabajador —</option>
                        <template x-for="t in filteredTrabajadores" :key="t.id">
                            <option :value="t.id" x-text="t.nombre + ' (' + (t.rol ? t.rol.toUpperCase() : 'AYUDANTE') + ')'"></option>
                        </template>
                    </select>
                </div>

                {{-- Mini Ficha del trabajador --}}
                <div x-show="trabajadorId && !loading" x-cloak
                     class="rounded-xl bg-teal-500/5 border border-teal-500/15 p-3 space-y-1.5 text-xs">
                    <div class="font-black text-slate-100 text-sm" x-text="trabajador ? trabajador.nombre : ''"></div>
                    <div class="text-slate-400"><i class="fa-solid fa-mountain mr-1 text-teal-400"></i> <span x-text="bocaminaNombre"></span></div>
                    <div class="text-slate-400"><i class="fa-solid fa-user-gear mr-1 text-indigo-400"></i> <span class="capitalize" x-text="cargo"></span></div>
                    <div class="text-slate-400"><i class="fa-solid fa-file-signature mr-1 text-emerald-400"></i> Contrato: <strong class="text-slate-200" x-text="tipoContratoNombre"></strong></div>
                </div>

                {{-- Fecha --}}
                <div>
                    <label class="p-label">Fecha de Pago <span class="text-rose-400">*</span></label>
                    <input type="date" name="fecha" required x-model="fecha" class="p-input font-mono">
                </div>

                {{-- N.º de Nota --}}
                <div>
                    <label class="p-label">
                        <i class="fa-solid fa-receipt mr-1 text-amber-400"></i>
                        N.º de Nota / Comprobante
                    </label>
                    <input type="text" name="numero_nota" x-model="numeroNota"
                           placeholder="Ej. NOTA-001, V-2026-0045"
                           class="p-input font-mono">
                    <p class="text-[10px] text-slate-500 mt-1">Opcional — Para búsqueda y control posterior</p>
                </div>

                {{-- Bonos y Descuentos --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="p-label">Bonos Extra (+)</label>
                        <input type="number" name="bonos" step="0.01" min="0" x-model="bonos"
                               @input="recalculate()" placeholder="0.00" class="p-input font-mono" required>
                    </div>
                    <div>
                        <label class="p-label">Descuentos Extra (−)</label>
                        <input type="number" name="descuentos" step="0.01" min="0" x-model="descuentos"
                               @input="recalculate()" placeholder="0.00" class="p-input font-mono" required>
                    </div>
                </div>

                {{-- Modalidad de pago --}}
                <div x-show="trabajadorId" x-cloak>
                    <label class="p-label">Modalidad</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button type="button" @click="tipoPago='completo'; userEditedMontoPagado=false; recalculate()"
                                :class="tipoPago==='completo' ? 'bg-gradient-to-r from-teal-500 to-emerald-500 text-white font-bold shadow-lg' : 'p-btn-ghost'"
                                class="p-btn justify-center text-xs py-2.5 rounded-xl">
                            <i class="fa-solid fa-circle-check"></i> Completo
                        </button>
                        <button type="button" @click="tipoPago='parcial'; userEditedMontoPagado=true; montoPagado=(neto*0.5).toFixed(2); recalculate()"
                                :class="tipoPago==='parcial' ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white font-bold shadow-lg' : 'p-btn-ghost'"
                                class="p-btn justify-center text-xs py-2.5 rounded-xl">
                            <i class="fa-solid fa-hourglass-half"></i> Parcial
                        </button>
                    </div>
                </div>

                {{-- Monto Pagado --}}
                <div x-show="trabajadorId" x-cloak>
                    <label class="p-label">
                        <span x-show="tipoPago==='completo'">Total a Pagar (Neto)</span>
                        <span x-show="tipoPago==='parcial'">Monto Entregado</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-xs font-black text-teal-400 font-mono pointer-events-none">Bs.</span>
                        <input type="number" name="monto_pagado" step="0.01" min="0"
                               x-model="montoPagado"
                               :disabled="tipoPago==='completo'"
                               @input="userEditedMontoPagado=true; recalculate()"
                               :class="tipoPago==='completo' ? 'opacity-60 cursor-not-allowed' : ''"
                               class="p-input font-mono font-black !pl-11 text-teal-400">
                    </div>
                    <div x-show="tipoPago==='parcial' && parseFloat(montoPagado) < parseFloat(neto)"
                         class="mt-1.5 text-[10px] text-amber-400 font-bold flex items-center gap-1" x-cloak>
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        Saldo pendiente: Bs. <span x-text="(parseFloat(neto)-parseFloat(montoPagado)).toFixed(2)"></span>
                    </div>
                </div>

                {{-- Forma de Pago --}}
                <div>
                    <label class="p-label">Forma de Pago</label>
                    <select name="metodo_pago" class="p-input font-bold" style="color:#14b8a6">
                        <option value="efectivo">💵 Efectivo</option>
                        <option value="cheque">📋 Cheque</option>
                        <option value="transferencia">🏦 Transferencia</option>
                    </select>
                </div>

                {{-- Observación --}}
                <div>
                    <label class="p-label flex items-center justify-between">
                        <span>Observación</span>
                        <span x-show="parseFloat(descuentos) > 0" class="text-rose-400 text-[10px]" x-cloak>* Requerido por descuento</span>
                    </label>
                    <textarea name="observacion" rows="2" x-model="observacion"
                              :required="parseFloat(descuentos) > 0"
                              placeholder="Ej. Liquidación semanal..."
                              class="p-input resize-none text-xs"></textarea>
                </div>

                {{-- Entregado por --}}
                <div>
                    <label class="p-label">Entregado por</label>
                    <input type="text" name="entregado_por"
                           value="{{ Auth::user()->name ?? 'Administración General' }}"
                           class="p-input text-xs">
                </div>

                {{-- Tipo de cambio (Editable) --}}
                <div>
                    <label class="p-label flex items-center justify-between">
                        <span><i class="fa-solid fa-dollar-sign text-emerald-400 mr-1"></i> Tipo de Cambio Dólar (Bs./USD) <span class="text-teal-400">*</span></span>
                        <span class="text-[10px] text-emerald-400 font-mono font-bold" x-show="tipoCambio > 0" x-text="'$US ' + (neto / (parseFloat(tipoCambio) || 6.96)).toFixed(2)"></span>
                    </label>
                    <div class="relative">
                        <input type="number" step="0.01" min="0.01" name="tipo_cambio" x-model="tipoCambio" @input="recalculate()" required
                               class="p-input font-mono font-extrabold text-emerald-400 pr-12">
                        <span class="absolute right-3 top-2.5 text-xs text-slate-400 font-bold font-mono">Bs/$</span>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1">Se reflejará en el recibo impreso en Bs. y $US.</p>
                </div>

                {{-- Submit --}}
                <div class="pt-1">
                    <button type="submit"
                            :disabled="!trabajadorId || items.length === 0 || (parseFloat(descuentos)>0 && !observacion.trim())"
                            class="p-btn p-btn-teal w-full justify-center py-3.5 rounded-xl text-sm disabled:opacity-40 disabled:cursor-not-allowed">
                        <i class="fa-solid fa-circle-check"></i> Procesar y Confirmar Pago
                    </button>
                </div>
            </div>
        </div>

        {{-- ══════════ COL DERECHA: Conceptos + Anticipos + Resumen ══════════ --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Loading --}}
            <div x-show="loading" class="p-card p-10 flex flex-col items-center justify-center gap-3 text-slate-400">
                <i class="fa-solid fa-circle-notch fa-spin text-3xl text-teal-400"></i>
                <span class="text-sm">Cargando datos del trabajador...</span>
            </div>

            {{-- Estado vacío --}}
            <div x-show="!trabajadorId && !loading" class="p-card p-10 text-center space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-slate-900 flex items-center justify-center mx-auto text-slate-600 border border-slate-800">
                    <i class="fa-solid fa-user-plus text-2xl"></i>
                </div>
                <div>
                    <h4 class="text-base font-bold text-slate-200">Ningún trabajador seleccionado</h4>
                    <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">Selecciona un trabajador del panel izquierdo para configurar los conceptos del pago.</p>
                </div>
            </div>

            {{-- Contenido principal --}}
            <div x-show="trabajadorId && !loading" x-cloak class="space-y-5">

                {{-- ── SECCIÓN 1: Conceptos del Pago (Multi-ítem) ── --}}
                <div class="p-card overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b"
                         style="border-color:rgba(148,163,184,0.08); background:rgba(20,184,166,0.04)">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-teal-500/15 border border-teal-500/25 flex items-center justify-center text-teal-400">
                                <i class="fa-solid fa-list-check text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-100">Conceptos del Pago</h3>
                                <p class="text-[10px] text-slate-500">Agrega jornales, metros, volquetas u otro concepto. Cada línea = un contrato o tipo de trabajo.</p>
                            </div>
                        </div>
                        <button type="button" @click="addItem()"
                                class="p-btn p-btn-teal py-2 cursor-pointer">
                            <i class="fa-solid fa-plus text-xs"></i> Agregar Ítem
                        </button>
                    </div>

                    <div class="p-5 space-y-4">

                        {{-- Empty items --}}
                        <template x-if="items.length === 0">
                            <div class="py-10 text-center text-slate-500">
                                <i class="fa-solid fa-inbox text-3xl block mb-2 opacity-30"></i>
                                <p class="text-sm">Sin conceptos aún. Haz clic en <strong class="text-teal-400">+ Agregar Ítem</strong>.</p>
                            </div>
                        </template>

                        <template x-for="(item, idx) in items" :key="idx">
                            <div class="item-row">
                                {{-- Cabecera del ítem --}}
                                <div class="item-row-header">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center text-xs font-black"
                                              x-text="idx + 1"></span>
                                        <span class="text-xs font-bold text-slate-300">Concepto de Pago</span>
                                    </div>
                                    <button type="button" @click="removeItem(idx)"
                                            x-show="items.length > 1"
                                            class="p-btn p-btn-rose p-btn-icon cursor-pointer">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                                    {{-- Contrato (opcional) --}}
                                    <div>
                                        <label class="p-label">
                                            <i class="fa-solid fa-file-signature mr-1 text-emerald-400"></i>
                                            Contrato del Trabajador
                                        </label>
                                        <select :name="'items['+idx+'][contrato_id]'"
                                                x-model="item.contrato_id"
                                                @change="onContratoChange(idx)"
                                                class="p-input text-xs">
                                            <option value="">— Sin contrato específico —</option>
                                            <template x-for="c in contratos" :key="c.id">
                                                <option :value="c.id"
                                                        x-text="c.label"
                                                        :class="c.estado === 'activo' ? 'text-emerald-400' : 'text-slate-400'">
                                                </option>
                                            </template>
                                        </select>
                                    </div>

                                    {{-- Tipo de Trabajo --}}
                                    <div>
                                        <label class="p-label">
                                            <i class="fa-solid fa-hammer mr-1 text-amber-400"></i>
                                            Tipo de Trabajo <span class="text-rose-400">*</span>
                                        </label>
                                        <input type="text" :name="'items['+idx+'][tipo_trabajo]'"
                                               x-model="item.tipo_trabajo"
                                               list="tipos_trabajo_list"
                                               required
                                               placeholder="Ej. Jornal, Carguío, Transporte..."
                                               class="p-input text-xs">
                                        <datalist id="tipos_trabajo_list">
                                            @foreach($tiposTrabajo ?? [] as $tt)
                                                <option value="{{ $tt->nombre }}"></option>
                                            @endforeach
                                            <option value="Jornal"></option>
                                            <option value="Carguío"></option>
                                            <option value="Descarguío"></option>
                                            <option value="Transporte"></option>
                                            <option value="Mantenimiento"></option>
                                            <option value="Perforación"></option>
                                            <option value="Limpieza"></option>
                                            <option value="Otro"></option>
                                        </datalist>
                                    </div>

                                    {{-- Descripción --}}
                                    <div class="sm:col-span-2">
                                        <label class="p-label">Descripción / Detalle</label>
                                        <input type="text" :name="'items['+idx+'][descripcion]'"
                                               x-model="item.descripcion"
                                               placeholder="Ej. Semana del 10 al 14 de Agosto, Interior galería 3..."
                                               class="p-input text-xs">
                                    </div>

                                    {{-- Cantidad / Jornales / Viajes / Horas --}}
                                    <div>
                                        <label class="p-label">
                                            <i class="fa-solid fa-hashtag mr-1 text-cyan-400"></i>
                                            <span x-text="getUnitLabel(item.tipo_trabajo)"></span> <span class="text-rose-400">*</span>
                                        </label>
                                        <input type="number" step="0.01" min="0"
                                               :name="'items['+idx+'][cantidad]'"
                                               x-model="item.cantidad"
                                               @input="calcItemTotal(idx)"
                                               placeholder="Ej. 10" required
                                               class="p-input font-mono text-cyan-400">
                                    </div>

                                    {{-- Precio unitario (por viaje, por hora, por jornal) --}}
                                    <div>
                                        <label class="p-label">
                                            <i class="fa-solid fa-coins mr-1 text-amber-400"></i>
                                            <span x-text="getUnitPriceLabel(item.tipo_trabajo)"></span> <span class="text-rose-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-amber-400 font-bold font-mono pointer-events-none">Bs.</span>
                                            <input type="number" step="0.01" min="0"
                                                   :name="'items['+idx+'][precio_unitario]'"
                                                   x-model="item.precio_unitario"
                                                   @input="calcItemTotal(idx)"
                                                   placeholder="0.00" required
                                                   class="p-input font-mono !pl-10">
                                        </div>
                                    </div>

                                    {{-- Subtotal del ítem --}}
                                    <div class="sm:col-span-2">
                                        <input type="hidden" :name="'items['+idx+'][subtotal]'" :value="item.subtotal">

                                        {{-- Desglose visual de unidades / viajes / horas --}}
                                        <div x-show="parseFloat(item.cantidad) > 0 && parseFloat(item.precio_unitario) > 0"
                                             class="rounded-xl bg-slate-900/60 border border-slate-800 p-3">
                                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-500 mb-2">
                                                Desglose de <span x-text="getUnitBreakdownTitle(item.tipo_trabajo)"></span>
                                            </p>
                                            <div class="flex flex-wrap gap-1.5 mb-2" x-show="parseInt(item.cantidad) <= 31">
                                                <template x-for="n in Math.min(parseInt(item.cantidad) || 0, 31)" :key="n">
                                                    <div class="flex flex-col items-center px-2 py-1 rounded-lg bg-teal-500/10 border border-teal-500/20 text-center min-w-[48px]">
                                                        <span class="text-[9px] text-teal-500/70 font-bold" x-text="getPillPrefix(item.tipo_trabajo) + n"></span>
                                                        <span class="text-[10px] text-teal-300 font-mono font-black"
                                                              x-text="'Bs.' + parseFloat(item.precio_unitario || 0).toFixed(0)"></span>
                                                    </div>
                                                </template>
                                                <div x-show="parseInt(item.cantidad) > 31"
                                                     class="flex items-center px-3 py-1.5 rounded-lg bg-slate-800/60 text-[10px] text-slate-400">
                                                    + <span x-text="parseInt(item.cantidad) - 31"></span> más...
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-between pt-2 border-t border-slate-800/60">
                                                <span class="text-xs text-slate-400 font-mono">
                                                    <span class="text-cyan-400 font-bold" x-text="parseFloat(item.cantidad||0).toFixed(2)"></span>
                                                    ×
                                                    <span class="text-amber-400 font-bold" x-text="'Bs.' + parseFloat(item.precio_unitario||0).toFixed(2)"></span>
                                                </span>
                                                <span class="text-base font-black text-teal-400 font-mono"
                                                      x-text="'Bs. ' + parseFloat(item.subtotal||0).toFixed(2)"></span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </template>

                        {{-- Totales de items --}}
                        <div x-show="items.length > 1" x-cloak
                             class="flex items-center justify-between px-4 py-3 rounded-xl bg-slate-900/40 border border-teal-500/15">
                            <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">
                                <i class="fa-solid fa-sigma mr-1.5 text-teal-400"></i>
                                Subtotal de <span x-text="items.length"></span> conceptos:
                            </span>
                            <span class="text-lg font-black text-teal-400 font-mono"
                                  x-text="'Bs. ' + subtotalItems.toFixed(2)"></span>
                        </div>

                    </div>
                </div>

                {{-- ── SECCIÓN 2: Saldos Pendientes de Semanas Anteriores ── --}}
                <div x-show="saldosPendientes.length > 0" x-cloak class="p-card p-5 border border-teal-500/15">
                    <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-clock-rotate-left text-teal-400"></i>
                        Saldos Pendientes de Semanas Anteriores
                    </h3>
                    <div class="space-y-2">
                        <template x-for="sal in saldosPendientes" :key="sal.id">
                            <div class="flex justify-between items-center p-3 rounded-xl bg-teal-500/5 border border-teal-500/10 text-xs">
                                <div>
                                    <div class="text-slate-300 font-bold"
                                         x-text="'Planilla del ' + new Date(sal.fecha.replace(/-/g,'/')).toLocaleDateString('es-ES',{year:'numeric',month:'2-digit',day:'2-digit'})"></div>
                                    <div class="text-slate-500 text-[10px] font-mono"
                                         x-text="'Neto: Bs.' + parseFloat(sal.neto).toFixed(2) + ' | Pagado: Bs.' + parseFloat(sal.monto_pagado).toFixed(2)"></div>
                                </div>
                                <div class="text-teal-400 font-black font-mono text-sm"
                                     x-text="'Bs. ' + parseFloat(sal.saldo_pendiente).toFixed(2)"></div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- ── SECCIÓN 3: Anticipos a Descontar ── --}}
                <div class="p-card p-5">
                    <h3 class="text-sm font-bold text-slate-200 flex items-center gap-2 mb-3">
                        <i class="fa-solid fa-money-bill-trend-up text-rose-400"></i>
                        Anticipos a Descontar
                    </h3>

                    <template x-if="anticipos.length === 0">
                        <p class="text-center text-slate-500 text-xs py-4">
                            <i class="fa-solid fa-circle-check mr-1 text-emerald-400"></i>
                            Sin anticipos pendientes.
                        </p>
                    </template>

                    <template x-if="anticipos.length > 0">
                        <div class="space-y-2">
                            <template x-for="ant in anticipos" :key="ant.id">
                                <div class="flex items-center justify-between p-3 rounded-xl border transition"
                                     :class="ant.aplicado ? 'bg-rose-500/5 border-rose-500/20' : 'bg-slate-900/30 border-slate-800'">
                                    <div class="flex items-center gap-3">
                                        <input type="checkbox" :id="'ant-'+ant.id" x-model="ant.aplicado"
                                               @change="recalculate()"
                                               class="rounded border-slate-700 text-rose-500 focus:ring-rose-500 bg-slate-950">
                                        <label :for="'ant-'+ant.id" class="cursor-pointer">
                                            <div class="text-xs font-bold text-slate-200"
                                                 x-text="'Anticipo del ' + new Date(ant.fecha.replace(/-/g,'/')).toLocaleDateString('es-ES',{year:'numeric',month:'2-digit',day:'2-digit'})"></div>
                                            <div class="text-[10px] text-slate-400 font-mono"
                                                 x-text="'Saldo: Bs. ' + parseFloat(ant.saldo).toFixed(2)"></div>
                                        </label>
                                    </div>
                                    <div x-show="ant.aplicado" class="flex items-center gap-2">
                                        <span class="text-[10px] text-slate-500">Descontar Bs.</span>
                                        <input type="number" step="0.01" min="0" :max="ant.saldo"
                                               :name="'deducciones_anticipos['+ant.id+']'"
                                               x-model="ant.liveDeduccion"
                                               @input="recalculate()"
                                               class="w-24 px-2 py-1 bg-slate-950 border border-rose-500/30 rounded-lg text-right text-xs font-mono font-bold text-rose-400 focus:outline-none focus:border-rose-500">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- ── SECCIÓN 4: Resumen Final ── --}}
                <div class="total-box">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="fa-solid fa-receipt text-teal-400"></i>
                        <h3 class="text-sm font-bold text-slate-100">Resumen de Liquidación</h3>
                        <span x-show="numeroNota" class="p-badge p-badge-amber font-mono text-[10px]" x-cloak>
                            <i class="fa-solid fa-hashtag"></i>
                            <span x-text="numeroNota"></span>
                        </span>
                    </div>

                    <div class="space-y-0.5">
                        <template x-for="(it, i) in items" :key="i">
                            <div class="total-row">
                                <span class="text-slate-400 flex items-center gap-1.5">
                                    <span class="w-4 h-4 rounded bg-teal-500/20 flex items-center justify-center text-teal-400 font-bold" style="font-size:9px" x-text="i+1"></span>
                                    <span x-text="it.tipo_trabajo || 'Concepto'"></span>
                                    <span class="text-slate-600 font-mono text-[10px]"
                                          x-text="'(' + parseFloat(it.cantidad||0).toFixed(2) + ' × Bs.' + parseFloat(it.precio_unitario||0).toFixed(2) + ')'"></span>
                                </span>
                                <span class="text-slate-200 font-mono font-bold"
                                      x-text="'Bs. ' + parseFloat(it.subtotal||0).toFixed(2)"></span>
                            </div>
                        </template>

                        <div class="total-row mt-2 pt-2" style="border-top:1px solid rgba(148,163,184,0.1)">
                            <span class="text-slate-400">Subtotal Conceptos:</span>
                            <span class="text-slate-200 font-mono font-bold" x-text="'Bs. ' + subtotalItems.toFixed(2)"></span>
                        </div>
                        <div class="total-row">
                            <span class="text-slate-400">Bonos (+):</span>
                            <span class="text-emerald-400 font-mono" x-text="'+Bs. ' + (parseFloat(bonos)||0).toFixed(2)"></span>
                        </div>
                        <div class="total-row">
                            <span class="text-slate-400">Descuentos (−):</span>
                            <span class="text-rose-400 font-mono" x-text="'-Bs. ' + (parseFloat(descuentos)||0).toFixed(2)"></span>
                        </div>
                        <div class="total-row" x-show="totalSaldosPendientes > 0" x-cloak>
                            <span class="text-slate-400">Saldos Anteriores (+):</span>
                            <span class="text-teal-400 font-mono font-bold" x-text="'+Bs. ' + totalSaldosPendientes.toFixed(2)"></span>
                        </div>
                        <div class="total-row">
                            <span class="text-slate-400">Anticipos Descontados (−):</span>
                            <span class="text-rose-400 font-mono" x-text="'-Bs. ' + anticiposDescontados.toFixed(2)"></span>
                        </div>

                        {{-- TOTAL NETO --}}
                        <div class="flex items-center justify-between pt-4 mt-3 border-t border-teal-500/20">
                            <span class="text-base font-black uppercase tracking-widest text-slate-100">
                                💰 Total Neto a Pagar:
                            </span>
                            <span class="text-3xl font-black text-teal-400 font-mono" x-text="'Bs. ' + parseFloat(neto).toFixed(2)"></span>
                        </div>

                        <div x-show="tipoPago==='parcial'" x-cloak
                             class="mt-3 px-4 py-2.5 rounded-xl bg-amber-500/8 border border-amber-500/20 flex items-center justify-between text-xs">
                            <span class="text-amber-400 font-bold"><i class="fa-solid fa-hand-holding-dollar mr-1"></i> Monto a entregar ahora:</span>
                            <span class="text-amber-300 font-black font-mono text-base" x-text="'Bs. ' + parseFloat(montoPagado||0).toFixed(2)"></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
function pagoWizard() {
    return {
        // Worker selection
        trabajadorId: '',
        bocaminaFiltroId: '',
        trabajadoresList: @json($trabajadores),
        loading: false,

        // Worker data
        trabajador: null,
        bocaminaNombre: '',
        cargo: '',
        tipoContratoNombre: '',
        tarifaAcordada: 0,
        contratos: [],

        // Form general
        fecha: '{{ now()->toDateString() }}',
        numeroNota: '',
        bonos: 0,
        descuentos: 0,
        tipoCambio: 6.96,
        observacion: '',

        // Payment mode
        tipoPago: 'completo',
        montoPagado: 0,
        userEditedMontoPagado: false,

        // Items (multi-concepto)
        items: [],

        // Advances
        anticipos: [],
        anticiposDescontados: 0,
        saldosPendientes: [],
        totalSaldosPendientes: 0,

        // Calculated
        neto: 0,

        get filteredTrabajadores() {
            if (!this.bocaminaFiltroId) return this.trabajadoresList;
            return this.trabajadoresList.filter(t => t.bocamina_id == this.bocaminaFiltroId);
        },

        get subtotalItems() {
            return this.items.reduce((sum, it) => sum + (parseFloat(it.subtotal) || 0), 0);
        },

        addItem() {
            const firstCont = (this.contratos && this.contratos.length > 0) ? this.contratos[0] : null;
            this.items.push({
                contrato_id: firstCont ? firstCont.id : '',
                tipo_trabajo: firstCont ? firstCont.tipo_trabajo : (this.tipoContratoNombre !== 'Sin Contrato' ? this.tipoContratoNombre : 'Jornales'),
                descripcion: '',
                cantidad: '',
                precio_unitario: firstCont ? (firstCont.tarifa_acordada > 0 ? firstCont.tarifa_acordada : '') : (this.tarifaAcordada > 0 ? this.tarifaAcordada : ''),
                subtotal: 0
            });
        },

        removeItem(idx) {
            this.items.splice(idx, 1);
            this.recalculate();
        },

        calcItemTotal(idx) {
            const it = this.items[idx];
            const c  = parseFloat(it.cantidad) || 0;
            const p  = parseFloat(it.precio_unitario) || 0;
            this.items[idx].subtotal = (c * p).toFixed(2);
            this.recalculate();
        },

        getUnitLabel(tipo) {
            if (!tipo) return 'Cantidad / Unidades';
            const t = tipo.toLowerCase();
            if (t.includes('viaje') || t.includes('volqueta')) return 'N.º de Viajes';
            if (t.includes('hora')) return 'N.º de Horas';
            if (t.includes('jornal')) return 'N.º de Jornales';
            if (t.includes('volquetada')) return 'N.º de Volquetadas';
            if (t.includes('metro')) return 'Metros Avanzados';
            return 'Cantidad / Unidades';
        },

        getUnitPriceLabel(tipo) {
            if (!tipo) return 'Precio Unitario (Bs.)';
            const t = tipo.toLowerCase();
            if (t.includes('viaje') || t.includes('volqueta')) return 'Precio por Viaje (Bs.)';
            if (t.includes('hora')) return 'Precio por Hora (Bs.)';
            if (t.includes('jornal')) return 'Precio por Jornal (Bs.)';
            if (t.includes('volquetada')) return 'Precio por Volquetada (Bs.)';
            if (t.includes('metro')) return 'Precio por Metro (Bs.)';
            return 'Precio Unitario (Bs.)';
        },

        getUnitBreakdownTitle(tipo) {
            if (!tipo) return 'Unidades / Jornales';
            const t = tipo.toLowerCase();
            if (t.includes('viaje') || t.includes('volqueta')) return 'Viajes';
            if (t.includes('hora')) return 'Horas';
            if (t.includes('jornal')) return 'Jornales';
            if (t.includes('volquetada')) return 'Volquetadas';
            if (t.includes('metro')) return 'Metros';
            return 'Unidades';
        },

        getPillPrefix(tipo) {
            if (!tipo) return '#';
            const t = tipo.toLowerCase();
            if (t.includes('viaje') || t.includes('volqueta')) return 'Viaje #';
            if (t.includes('hora')) return 'Hora #';
            if (t.includes('jornal')) return 'Jornal #';
            if (t.includes('volquetada')) return 'Volq #';
            if (t.includes('metro')) return 'm #';
            return '#';
        },

        onContratoChange(idx) {
            const contId = this.items[idx].contrato_id;
            if (!contId) return;
            const cont = this.contratos.find(c => c.id == contId);
            if (cont) {
                this.items[idx].tipo_trabajo     = cont.tipo_trabajo;
                this.items[idx].precio_unitario  = cont.tarifa_acordada > 0 ? cont.tarifa_acordada : this.items[idx].precio_unitario;
                this.calcItemTotal(idx);
            }
        },

        recalculate() {
            const sub  = this.subtotalItems;
            const b    = parseFloat(this.bonos) || 0;
            const d    = parseFloat(this.descuentos) || 0;
            const prev = parseFloat(this.totalSaldosPendientes) || 0;

            let capacidad = sub + b - d + prev;
            if (capacidad < 0) capacidad = 0;

            let totalDed = 0;
            this.anticipos.forEach(a => {
                if (!a.aplicado) { a.liveDeduccion = 0; return; }
                if (!a.liveDeduccion || a.liveDeduccion === 0) {
                    a.liveDeduccion = Math.min(parseFloat(a.saldo), capacidad);
                } else {
                    a.liveDeduccion = Math.min(parseFloat(a.saldo), parseFloat(a.liveDeduccion) || 0);
                }
                a.liveDeduccion = Math.min(a.liveDeduccion, capacidad);
                totalDed  += a.liveDeduccion;
                capacidad -= a.liveDeduccion;
            });

            this.anticiposDescontados = totalDed;
            this.neto = sub + b - d - totalDed + prev;

            if (this.tipoPago === 'completo' || !this.userEditedMontoPagado) {
                this.montoPagado = this.neto.toFixed(2);
            }
        },

        async onTrabajadorChange() {
            if (!this.trabajadorId) { this.limpiarWorker(); return; }
            this.loading = true;
            try {
                const res  = await fetch('/pagos/trabajador-data/' + this.trabajadorId);
                const data = await res.json();

                this.trabajador        = data.trabajador;
                this.bocaminaNombre    = data.bocamina_nombre;
                this.cargo             = data.cargo;
                this.tipoContratoNombre= data.tipo_contrato_nombre;
                this.tarifaAcordada    = parseFloat(data.tarifa_acordada) || 0;
                this.contratos         = data.contratos || [];

                this.saldosPendientes     = data.saldos_pendientes || [];
                this.totalSaldosPendientes= parseFloat(data.total_saldos_pendientes) || 0;

                this.anticipos = data.anticipos.map(a => ({...a, aplicado: false, liveDeduccion: 0}));

                this.userEditedMontoPagado = false;
                this.tipoPago = 'completo';

                // Add first item pre-filled with worker's contract
                const firstCont = (this.contratos && this.contratos.length > 0) ? this.contratos[0] : null;
                this.items = [{
                    contrato_id: firstCont ? firstCont.id : '',
                    tipo_trabajo: firstCont ? firstCont.tipo_trabajo : (data.tipo_contrato_nombre !== 'Sin Contrato' ? data.tipo_contrato_nombre : 'Jornales'),
                    descripcion: '',
                    cantidad: '',
                    precio_unitario: firstCont ? (firstCont.tarifa_acordada > 0 ? firstCont.tarifa_acordada : '') : (this.tarifaAcordada > 0 ? this.tarifaAcordada : ''),
                    subtotal: 0
                }];

                this.recalculate();
            } catch(e) {
                console.error('Error loading worker', e);
            } finally {
                this.loading = false;
            }
        },

        limpiarWorker() {
            this.trabajador = null;
            this.bocaminaNombre = '';
            this.cargo = '';
            this.tipoContratoNombre = '';
            this.tarifaAcordada = 0;
            this.contratos = [];
            this.items = [];
            this.anticipos = [];
            this.saldosPendientes = [];
            this.totalSaldosPendientes = 0;
            this.anticiposDescontados = 0;
            this.neto = 0;
            this.montoPagado = 0;
            this.userEditedMontoPagado = false;
            this.tipoPago = 'completo';
        }
    };
}
</script>
@endpush
