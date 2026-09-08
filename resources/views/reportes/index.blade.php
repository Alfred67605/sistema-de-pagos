@extends('layouts.app')

@section('title', 'Reportes del Personal')

@section('content')
<style>
/* ===========================================================
   REPORTES DEL PERSONAL — ERP PREMIUM DESIGN SYSTEM
   =========================================================== */
.rpt-page { --rpt-bg-card: #ffffff; --rpt-border: #e2e8f0; --rpt-text: #0f172a; --rpt-text-sub: #475569; --rpt-text-muted: #94a3b8; --rpt-row-hover: #f8fafc; --rpt-th-bg: #f1f5f9; --rpt-th-color: #334155; }
html:not(.light-theme) .rpt-page { --rpt-bg-card: rgba(15,23,42,0.65); --rpt-border: rgba(255,255,255,0.08); --rpt-text: #f1f5f9; --rpt-text-sub: #94a3b8; --rpt-text-muted: #64748b; --rpt-row-hover: rgba(255,255,255,0.03); --rpt-th-bg: rgba(15,23,42,0.7); --rpt-th-color: #94a3b8; }

.rpt-card {
    background: var(--rpt-bg-card);
    border: 1px solid var(--rpt-border);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    backdrop-filter: blur(12px);
    overflow: hidden;
}

/* ── Barra de Pestañas ── */
.rpt-tabs-bar {
    display: flex;
    gap: 6px;
    padding: 6px;
    border-radius: 16px;
    border: 1.5px solid var(--rpt-border);
    background: var(--rpt-bg-card);
    backdrop-filter: blur(12px);
}

.rpt-tab-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 14px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 0.02em;
    transition: all 0.2s ease;
    color: var(--rpt-text-sub);
    background: transparent;
    font-family: 'Outfit', sans-serif;
    white-space: nowrap;
}

.rpt-tab-btn:hover {
    background: rgba(245,158,11,0.08);
    color: #d97706;
}

.rpt-tab-btn.rpt-active {
    background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%);
    color: #ffffff !important;
    box-shadow: 0 4px 16px rgba(245,158,11,0.35);
}

/* ── Inputs de Filtro ── */
.rpt-filter-input {
    width: 100%;
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    font-family: 'Outfit', sans-serif;
    border: 1.5px solid var(--rpt-border);
    background: var(--rpt-bg-card);
    color: var(--rpt-text);
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
    appearance: none;
    cursor: pointer;
}

.rpt-filter-input:focus {
    border-color: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245,158,11,0.15);
}

.rpt-filter-input option {
    background: #ffffff; color: #0f172a;
}
html:not(.light-theme) .rpt-filter-input option {
    background: #0f172a; color: #f1f5f9;
}

.rpt-label {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--rpt-text-sub);
    margin-bottom: 6px;
    font-family: 'Outfit', sans-serif;
}
.rpt-label i { color: #f59e0b; font-size: 11px; }

/* ── Botones de Exportar ── */
.rpt-export-btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 10px 18px; border-radius: 10px;
    font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.06em;
    border: none; cursor: pointer;
    font-family: 'Outfit', sans-serif;
    transition: all 0.18s ease;
}
.rpt-export-btn:hover { transform: translateY(-2px); }
.btn-excel { background: #16a34a; color: #fff; box-shadow: 0 4px 12px rgba(22,163,74,0.3); }
.btn-excel:hover { background: #15803d; box-shadow: 0 6px 18px rgba(22,163,74,0.45); }
.btn-pdf { background: #dc2626; color: #fff; box-shadow: 0 4px 12px rgba(220,38,38,0.3); }
.btn-pdf:hover { background: #b91c1c; box-shadow: 0 6px 18px rgba(220,38,38,0.45); }
.btn-print { background: #d97706; color: #fff; box-shadow: 0 4px 12px rgba(217,119,6,0.3); }
.btn-print:hover { background: #b45309; box-shadow: 0 6px 18px rgba(217,119,6,0.45); }

/* ── Tablas ── */
.rpt-section {
    border-radius: 14px;
    border: 1.5px solid var(--rpt-border);
    overflow: hidden;
    background: var(--rpt-bg-card);
}
.rpt-section-header {
    padding: 14px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1.5px solid var(--rpt-border);
    background: var(--rpt-th-bg);
    font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.07em;
    color: var(--rpt-text-sub);
    font-family: 'Outfit', sans-serif;
}

table.rpt-tbl { width: 100%; border-collapse: collapse; }
table.rpt-tbl thead tr th {
    padding: 12px 18px;
    text-align: left;
    font-size: 11px; font-weight: 800;
    text-transform: uppercase; letter-spacing: 0.05em;
    color: var(--rpt-th-color);
    background: var(--rpt-th-bg);
    border-bottom: 1.5px solid var(--rpt-border);
    white-space: nowrap;
    font-family: 'Outfit', sans-serif;
    cursor: pointer;
}
table.rpt-tbl tbody tr td {
    padding: 12px 18px;
    font-size: 13px;
    color: var(--rpt-text-sub);
    border-bottom: 1px solid var(--rpt-border);
    white-space: nowrap;
    font-family: 'Outfit', sans-serif;
}
table.rpt-tbl tbody tr:hover td { background: var(--rpt-row-hover); }

/* ===========================================================
   EXECUTIVE CORPORATE REPORT STYLES (PDF & PRINT)
   =========================================================== */
.exec-report-container {
    display: none;
    background: #ffffff !important;
    color: #0f172a !important;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

.exec-doc {
    background: #ffffff !important;
    color: #0f172a !important;
    padding: 10px 14px;
    width: 100%;
}

.exec-header {
    border-bottom: 3px solid #0f172a;
    padding-bottom: 12px;
    margin-bottom: 16px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}

.exec-header-left {
    display: flex;
    align-items: center;
    gap: 14px;
}

.exec-logo-icon {
    width: 48px;
    height: 48px;
    background: #0f172a;
    color: #f59e0b;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
    box-shadow: 0 4px 10px rgba(15,23,42,0.15);
}

.exec-company-name {
    font-size: 19px;
    font-weight: 900;
    color: #0f172a;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    margin: 0;
    line-height: 1.1;
}

.exec-report-title {
    font-size: 13px;
    font-weight: 800;
    color: #d97706;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    margin: 3px 0 0 0;
}

.exec-report-subtitle {
    font-size: 9.5px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 2px 0 0 0;
}

.exec-meta-box {
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    background: #f8fafc;
    padding: 6px 12px;
    font-size: 9.5px;
    min-width: 250px;
}

.exec-meta-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 2px;
    align-items: center;
}
.exec-meta-row:last-child { margin-bottom: 0; }
.exec-meta-lbl { font-weight: 800; color: #475569; text-transform: uppercase; font-size: 9px; }
.exec-meta-val { font-weight: 700; color: #0f172a; font-family: monospace; font-size: 9.5px; }
.exec-badge-audited {
    background: #047857;
    color: #ffffff;
    font-size: 8px;
    font-weight: 800;
    padding: 1px 6px;
    border-radius: 4px;
    letter-spacing: 0.05em;
}

/* KPI Bar */
.exec-kpi-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
}
.exec-kpi-card {
    flex: 1;
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    padding: 8px 12px;
}
.exec-kpi-title {
    font-size: 8.5px;
    font-weight: 800;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.05em;
}
.exec-kpi-val {
    font-size: 15px;
    font-weight: 900;
    color: #0f172a;
    font-family: Consolas, monospace;
    margin-top: 2px;
}

/* Tables */
.exec-table {
    width: 100%;
    border-collapse: collapse;
    border: 1.5px solid #cbd5e1;
    font-size: 10px;
    margin-bottom: 14px;
    background: #ffffff;
}
.exec-table th {
    background: #1e293b;
    color: #ffffff;
    border: 1px solid #cbd5e1;
    padding: 7px 8px;
    font-weight: 800;
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}
.exec-table td {
    border: 1px solid #cbd5e1;
    padding: 6px 8px;
    color: #1e293b;
    font-size: 10px;
}
.exec-table tr:nth-child(even) td {
    background: #f8fafc;
}
.exec-subtotal-row td {
    background: #f1f5f9 !important;
    color: #0f172a !important;
    font-weight: 800 !important;
    border-top: 2px solid #94a3b8 !important;
}
.exec-grandtotal-row td {
    background: #0f172a !important;
    color: #ffffff !important;
    font-weight: 900 !important;
    font-size: 10.5px !important;
}
.exec-badge {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 4px;
    font-size: 8.5px;
    font-weight: 700;
    text-transform: uppercase;
    background: #e2e8f0;
    color: #334155;
}
.exec-sig-cell {
    width: 90px;
    height: 14px;
    border-bottom: 1px dashed #94a3b8;
    margin: 4px auto 0 auto;
}

.exec-section {
    margin-bottom: 18px;
    page-break-inside: avoid;
    break-inside: avoid;
}
.exec-section-header {
    background: #0f172a;
    color: #ffffff;
    padding: 7px 12px;
    border-radius: 6px 6px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Signatures */
.exec-signatures {
    margin-top: 26px;
    display: flex;
    justify-content: space-between;
    gap: 30px;
    page-break-inside: avoid;
    break-inside: avoid;
}
.exec-sig-box {
    flex: 1;
    text-align: center;
    border-top: 1.5px solid #0f172a;
    padding-top: 6px;
}
.exec-sig-name {
    font-size: 11px;
    font-weight: 800;
    color: #0f172a;
    margin: 0;
}
.exec-sig-title {
    font-size: 9px;
    font-weight: 800;
    color: #d97706;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 2px 0 0 0;
}
.exec-sig-sub {
    font-size: 8.5px;
    color: #64748b;
    margin: 2px 0 0 0;
}

/* Footer */
.exec-footer {
    margin-top: 18px;
    padding-top: 8px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    font-size: 8px;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

@media print {
    @page {
        size: letter landscape;
        margin: 8mm 10mm;
    }
    
    header, nav, aside, .no-print, .rpt-interactive-view, .rpt-tabs-bar, #toast-container, .global-button-spark {
        display: none !important;
    }
    
    body, html, main, .rpt-page {
        background: #ffffff !important;
        color: #0f172a !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        height: auto !important;
        overflow: visible !important;
        box-shadow: none !important;
        border: none !important;
    }
    
    .exec-report-container {
        display: block !important;
        width: 100% !important;
    }
    
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
    }
}
</style>

<div class="rpt-page space-y-6"
     x-data="{
        tab: '{{ $tab }}',
        searchTerm: '',
        sortCol: '',
        sortAsc: true,

        sortBy(col) {
            if (this.sortCol === col) {
                this.sortAsc = !this.sortAsc;
            } else {
                this.sortCol = col;
                this.sortAsc = true;
            }
        }
     }">

    {{-- ═══════════════ INTERACTIVE WEB DASHBOARD VIEW ═══════════════ --}}
    <div class="rpt-interactive-view space-y-6">

    {{-- ═══════════════ HEADER & GLOBAL ACTIONS ═══════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 no-print">
        <div>
            <h1 class="text-3xl font-extrabold flex items-center gap-3 tracking-tight text-slate-100">
                <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-amber-500/20 flex-shrink-0">
                    <i class="fa-solid fa-chart-line text-lg"></i>
                </span>
                Reportes del Personal
            </h1>
            <p class="text-xs text-slate-400 mt-1 ml-13">Monitoreo de planillas, desglose de anticipos, balance de bocaminas e historial de contratistas.</p>
        </div>
        <div class="flex flex-wrap gap-2.5">
            <button class="rpt-export-btn btn-excel" onclick="window.doExportExcel()">
                <i class="fa-solid fa-file-excel"></i> Excel
            </button>
            <button class="rpt-export-btn btn-pdf" onclick="window.doExportPDF()">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </button>
            <button class="rpt-export-btn btn-print" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Imprimir
            </button>
        </div>
    </div>

    {{-- ═══════════════ TAB NAVIGATION ═══════════════ --}}
    <div class="rpt-tabs-bar no-print">
        <button class="rpt-tab-btn" :class="{ 'rpt-active': tab === 'general' }" @click="tab = 'general'; window.history.replaceState({}, '', '?tab=general')">
            <i class="fa-solid fa-chart-pie text-amber-500"></i> 📈 Resumen General
        </button>
        <button class="rpt-tab-btn" :class="{ 'rpt-active': tab === 'trabajador' }" @click="tab = 'trabajador'; window.history.replaceState({}, '', '?tab=trabajador')">
            <i class="fa-solid fa-user-group text-blue-500"></i> 👷 Trabajadores
        </button>
        <button class="rpt-tab-btn" :class="{ 'rpt-active': tab === 'bocamina' }" @click="tab = 'bocamina'; window.history.replaceState({}, '', '?tab=bocamina')">
            <i class="fa-solid fa-mountain text-emerald-500"></i> ⛏️ Bocaminas
        </button>
        <button class="rpt-tab-btn" :class="{ 'rpt-active': tab === 'anticipos' }" @click="tab = 'anticipos'; window.history.replaceState({}, '', '?tab=anticipos')">
            <i class="fa-solid fa-hand-holding-dollar text-rose-500"></i> 💵 Anticipos
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 1: 📈 RESUMEN GENERAL --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'general'" space-y-6 x-cloak>
        
        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            
            {{-- Card 1: Total Pagado --}}
            <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-indigo-500 to-purple-650 text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-100/90">Total Pagado (Planillas)</span>
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                        <i class="fa-solid fa-receipt text-xs text-white"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-extrabold tracking-tight font-mono">Bs. {{ number_format($genTotalPagado, 2) }}</h2>
                <p class="text-[10px] text-indigo-100/80 font-medium mt-2">Sumatoria de planillas liquidadas</p>
            </div>

            {{-- Card 2: Total Anticipos --}}
            <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-rose-500 to-red-650 text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-rose-100/90">Total Anticipos (Adelantos)</span>
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                        <i class="fa-solid fa-hand-holding-dollar text-xs text-white"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-extrabold tracking-tight font-mono">Bs. {{ number_format($genTotalAnticipos, 2) }}</h2>
                <p class="text-[10px] text-rose-100/80 font-medium mt-2">Egresado por adelantos otorgados</p>
            </div>

            {{-- Card 3: Saldo de Caja del Personal --}}
            @php
                $posGen = $genSaldoCaja >= 0;
                $gradGen = $posGen ? 'from-emerald-500 to-teal-600' : 'from-amber-500 to-orange-650';
            @endphp
            <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br {{ $gradGen }} text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-white/90">Saldo de la Caja del Personal</span>
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                        <i class="fa-solid fa-vault text-xs text-white"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-extrabold tracking-tight font-mono">Bs. {{ number_format(abs($genSaldoCaja), 2) }}</h2>
                <p class="text-[10px] text-emerald-100/80 font-medium mt-2">
                    {{ $posGen ? '● Efectivo disponible para planillas' : '▲ Caja en déficit' }}
                </p>
            </div>

            {{-- Card 4: Trabajadores Activos --}}
            <div class="relative overflow-hidden rounded-2xl p-5 bg-gradient-to-br from-cyan-500 to-blue-650 text-white shadow-lg transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="absolute right-8 -top-8 w-16 h-16 bg-white/10 rounded-full pointer-events-none"></div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-100/90">Trabajadores Activos</span>
                    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-white/20">
                        <i class="fa-solid fa-users text-xs text-white"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-extrabold tracking-tight font-mono">{{ $genTrabajadoresActivos }}</h2>
                <p class="text-[10px] text-cyan-100/80 font-medium mt-2">Personal registrado activo</p>
            </div>
        </div>

        {{-- Interactive Charts Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pt-4">
            
            {{-- Chart 1: Gastos por Semana --}}
            <div class="rpt-card p-6 lg:col-span-2 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-chart-column text-amber-500"></i> Gastos por Semana (Planillas vs Anticipos)
                    </h3>
                    <span class="text-[10px] font-mono text-slate-400">Últimas 8 semanas</span>
                </div>
                <div class="h-64 relative">
                    <canvas id="chartGastosSemana"></canvas>
                </div>
            </div>

            {{-- Chart 2 & 3: Doughnut breakdown --}}
            <div class="rpt-card p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <h3 class="text-sm font-extrabold text-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-pie-chart text-emerald-500"></i> Gastos por Bocamina
                    </h3>
                </div>
                <div class="h-64 relative flex items-center justify-center">
                    <canvas id="chartGastosBocamina"></canvas>
                </div>
            </div>

        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 2: 👷 TRABAJADORES --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'trabajador'" space-y-6 x-cloak>
        
        {{-- Filters Section --}}
        <div class="rpt-card p-6 no-print">
            <form action="{{ route('reportes.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5 items-end">
                <input type="hidden" name="tab" value="trabajador">

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-user"></i> Trabajador</label>
                    <select name="trabajador_id" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todos los Trabajadores</option>
                        @foreach($allTrabajadores as $t)
                            <option value="{{ $t->id }}" {{ $trabId == $t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-user-tag"></i> Tipo de Trabajador</label>
                    <select name="rol" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todos los Cargos</option>
                        @foreach($roles as $r)
                            <option value="{{ $r }}" {{ $trabRol == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-file-contract"></i> Tipo de Contrato</label>
                    <select name="tipo_contrato_id" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todos los Contratos</option>
                        @foreach($tiposContrato as $tc)
                            <option value="{{ $tc->id }}" {{ $trabContratoId == $tc->id ? 'selected' : '' }}>{{ $tc->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-mountain"></i> Bocamina</label>
                    <select name="bocamina_id" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todas las Bocaminas</option>
                        @foreach($bocaminas as $b)
                            <option value="{{ $b->id }}" {{ $trabBocaminaId == $b->id ? 'selected' : '' }}>{{ $b->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="rpt-export-btn btn-print flex-1 justify-center">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>

        {{-- Workers KPI Summary Cards --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="rpt-card p-5 border-l-4 border-l-indigo-500">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Pagado</span>
                <h3 class="text-2xl font-black font-mono text-indigo-400 mt-1">Bs. {{ number_format($totPagadoTrabajador, 2) }}</h3>
            </div>
            <div class="rpt-card p-5 border-l-4 border-l-rose-500">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Anticipos</span>
                <h3 class="text-2xl font-black font-mono text-rose-400 mt-1">Bs. {{ number_format($totAnticiposTrabajador, 2) }}</h3>
            </div>
            <div class="rpt-card p-5 border-l-4 border-l-emerald-500">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Neto Recibido en Planillas</span>
                <h3 class="text-2xl font-black font-mono text-emerald-400 mt-1">Bs. {{ number_format($netoRecibidoTrabajador, 2) }}</h3>
            </div>
        </div>

        {{-- Tables Section: Pagos & Anticipos --}}
        <div class="rpt-section">
            <div class="rpt-section-header">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-receipt text-amber-500"></i> Historial de Liquidaciones y Planillas de Pago
                </div>
                <input type="text" x-model="searchTerm" placeholder="🔍 Buscar en tabla..." 
                       class="px-3 py-1 bg-slate-900 border border-slate-700/80 rounded-lg text-xs text-slate-100 font-sans focus:outline-none focus:border-amber-500">
            </div>
            <div class="overflow-x-auto">
                <table class="rpt-tbl">
                    <thead>
                        <tr>
                            <th @click="sortBy('id')">ID ⇕</th>
                            <th @click="sortBy('fecha')">Fecha ⇕</th>
                            <th @click="sortBy('nombre')">Trabajador ⇕</th>
                            <th>Bocamina</th>
                            <th>Subtotal Trabajos</th>
                            <th>Bonos (+)</th>
                            <th>Descuentos (-)</th>
                            <th>Anticipos (-)</th>
                            <th @click="sortBy('neto')">Pago Neto ⇕</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($listPagosTrabajador as $pago)
                            <tr>
                                <td class="td-mono">{{ $pago->id }}</td>
                                <td class="td-mono">{{ $pago->fecha->format('d/m/Y') }}</td>
                                <td class="td-name">{{ $pago->trabajador->nombre }}</td>
                                <td>{{ $pago->trabajador->bocamina->nombre ?? 'N/A' }}</td>
                                <td class="td-mono">Bs. {{ number_format($pago->subtotal, 2) }}</td>
                                <td class="td-mono text-emerald-400">+Bs. {{ number_format($pago->bonos, 2) }}</td>
                                <td class="td-mono text-red-400">-Bs. {{ number_format($pago->descuentos, 2) }}</td>
                                <td class="td-mono text-red-400">-Bs. {{ number_format($pago->anticipos_descontados, 2) }}</td>
                                <td class="td-mono text-emerald-400 font-bold">Bs. {{ number_format($pago->neto, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-8 text-slate-500">No se encontraron pagos registrados con los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 3: ⛏️ BOCAMINAS --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'bocamina'" space-y-6 x-cloak>
        
        {{-- Filters Section --}}
        <div class="rpt-card p-6 no-print">
            <form action="{{ route('reportes.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 items-end">
                <input type="hidden" name="tab" value="bocamina">

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-mountain"></i> Bocamina</label>
                    <select name="boc_bocamina_id" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todas las Bocaminas</option>
                        @foreach($bocaminas as $b)
                            <option value="{{ $b->id }}" {{ $bocFiltroId == $b->id ? 'selected' : '' }}>{{ $b->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-user-tag"></i> Tipo de Trabajador</label>
                    <select name="boc_rol" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todos los Cargos</option>
                        @foreach($roles as $r)
                            <option value="{{ $r }}" {{ $bocRol == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-file-contract"></i> Tipo de Contrato</label>
                    <select name="boc_tipo_contrato_id" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todos los Contratos</option>
                        @foreach($tiposContrato as $tc)
                            <option value="{{ $tc->id }}" {{ $bocContratoId == $tc->id ? 'selected' : '' }}>{{ $tc->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="rpt-export-btn btn-print flex-1 justify-center">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>

        {{-- Bocamina Details Grid --}}
        <div class="space-y-6">
            @foreach($bocaminasResumen as $bRes)
                <div class="rpt-card p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white font-bold">
                                <i class="fa-solid fa-mountain"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-slate-100">{{ $bRes['bocamina']->nombre }}</h2>
                                <p class="text-xs text-slate-400">{{ $bRes['cant_trabajadores'] }} trabajador(es) asignado(s)</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-4 font-mono text-xs">
                            <div class="bg-slate-900/60 px-3 py-1.5 rounded-lg border border-slate-800">
                                <span class="text-slate-500 block text-[9px]">TOTAL GASTADO:</span>
                                <span class="font-bold text-slate-100 text-sm">Bs. {{ number_format($bRes['total_gastado'], 2) }}</span>
                            </div>
                            <div class="bg-indigo-500/10 px-3 py-1.5 rounded-lg border border-indigo-500/20">
                                <span class="text-indigo-400 block text-[9px]">PAGOS PLANILLAS:</span>
                                <span class="font-bold text-indigo-400 text-sm">Bs. {{ number_format($bRes['total_pagos'], 2) }}</span>
                            </div>
                            <div class="bg-rose-500/10 px-3 py-1.5 rounded-lg border border-rose-500/20">
                                <span class="text-rose-400 block text-[9px]">ANTICIPOS:</span>
                                <span class="font-bold text-rose-400 text-sm">Bs. {{ number_format($bRes['total_anticipos'], 2) }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Workers table --}}
                    <div class="overflow-x-auto">
                        <table class="rpt-tbl">
                            <thead>
                                <tr>
                                    <th>Trabajador</th>
                                    <th>C.I.</th>
                                    <th>Cargo</th>
                                    <th>Contrato</th>
                                    <th class="text-right">Total Pagos</th>
                                    <th class="text-right">Total Anticipos</th>
                                    <th class="text-right">Total Egresado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bRes['trabajadores_detalle'] as $wd)
                                    <tr>
                                        <td class="td-name">{{ $wd['trabajador']->nombre }}</td>
                                        <td class="font-mono text-xs text-slate-400">{{ $wd['trabajador']->ci ?: 'S/N' }}</td>
                                        <td><span class="badge badge-gray">{{ ucfirst($wd['trabajador']->rol ?? 'Trabajador') }}</span></td>
                                        <td>{{ $wd['trabajador']->tipoContrato->nombre ?? 'N/A' }}</td>
                                        <td class="td-mono text-right">Bs. {{ number_format($wd['pagos'], 2) }}</td>
                                        <td class="td-mono text-right text-rose-400">Bs. {{ number_format($wd['anticipos'], 2) }}</td>
                                        <td class="td-mono text-right text-emerald-400 font-bold">Bs. {{ number_format($wd['total'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 border-slate-700 bg-slate-900/50 font-bold">
                                    <td colspan="4" class="text-right text-xs uppercase text-slate-400 py-3">Subtotal {{ $bRes['bocamina']->nombre }}:</td>
                                    <td class="td-mono text-right text-indigo-400 py-3">Bs. {{ number_format($bRes['total_pagos'], 2) }}</td>
                                    <td class="td-mono text-right text-rose-400 py-3">Bs. {{ number_format($bRes['total_anticipos'], 2) }}</td>
                                    <td class="td-mono text-right text-emerald-400 font-extrabold py-3">Bs. {{ number_format($bRes['total_gastado'], 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- TAB 4: 💵 ANTICIPOS --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'anticipos'" space-y-6 x-cloak>
        
        {{-- Filters Section --}}
        <div class="rpt-card p-6 no-print">
            <form action="{{ route('reportes.index') }}" method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5 items-end">
                <input type="hidden" name="tab" value="anticipos">

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-user"></i> Trabajador</label>
                    <select name="ant_trabajador_id" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todos los Trabajadores</option>
                        @foreach($allTrabajadores as $t)
                            <option value="{{ $t->id }}" {{ $antTrabId == $t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-user-tag"></i> Tipo de Trabajador</label>
                    <select name="ant_rol" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todos los Cargos</option>
                        @foreach($roles as $r)
                            <option value="{{ $r }}" {{ $antRol == $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-mountain"></i> Bocamina</label>
                    <select name="ant_bocamina_id" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="">Todas las Bocaminas</option>
                        @foreach($bocaminas as $b)
                            <option value="{{ $b->id }}" {{ $antBocaminaId == $b->id ? 'selected' : '' }}>{{ $b->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="rpt-label"><i class="fa-solid fa-toggle-on"></i> Estado de Saldo</label>
                    <select name="ant_estado" class="rpt-filter-input" onchange="this.form.submit()">
                        <option value="todos" {{ $antEstado === 'todos' ? 'selected' : '' }}>Todos los Anticipos</option>
                        <option value="pendiente" {{ $antEstado === 'pendiente' ? 'selected' : '' }}>Pendientes por Descontar</option>
                        <option value="descontado" {{ $antEstado === 'descontado' ? 'selected' : '' }}>Totalmente Descontados</option>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="rpt-export-btn btn-print flex-1 justify-center">
                        <i class="fa-solid fa-filter"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>

        {{-- Anticipos KPI Summary Cards --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rpt-card p-5 border-l-4 border-l-cyan-500">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Total Vales Emitidos</span>
                <h3 class="text-2xl font-black font-mono text-cyan-400 mt-1">{{ $antConteo }}</h3>
            </div>
            <div class="rpt-card p-5 border-l-4 border-l-rose-500">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Monto Total Anticipado</span>
                <h3 class="text-2xl font-black font-mono text-rose-400 mt-1">Bs. {{ number_format($antMontoTotal, 2) }}</h3>
            </div>
            <div class="rpt-card p-5 border-l-4 border-l-amber-500">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Anticipos Pendientes</span>
                <h3 class="text-2xl font-black font-mono text-amber-400 mt-1">Bs. {{ number_format($antMontoPendiente, 2) }}</h3>
            </div>
            <div class="rpt-card p-5 border-l-4 border-l-emerald-500">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Anticipos Descontados</span>
                <h3 class="text-2xl font-black font-mono text-emerald-400 mt-1">Bs. {{ number_format($antMontoDescontado, 2) }}</h3>
            </div>
        </div>

        {{-- Anticipos History Table --}}
        <div class="rpt-section">
            <div class="rpt-section-header">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-hand-holding-dollar text-rose-500"></i> Historial de Vales de Anticipo
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="rpt-tbl">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Trabajador</th>
                            <th>Bocamina</th>
                            <th>Monto Original</th>
                            <th>Saldo Restante</th>
                            <th>Estado</th>
                            <th class="no-print">Comprobante</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($listAnticiposTab as $ant)
                            <tr>
                                <td class="td-mono">{{ $ant->id }}</td>
                                <td class="td-mono">{{ $ant->fecha->format('d/m/Y') }}</td>
                                <td class="td-name">{{ $ant->trabajador->nombre }}</td>
                                <td>{{ $ant->trabajador->bocamina->nombre ?? 'N/A' }}</td>
                                <td class="td-mono">Bs. {{ number_format($ant->monto, 2) }}</td>
                                <td class="td-mono text-rose-400 font-bold">Bs. {{ number_format($ant->saldo, 2) }}</td>
                                <td>
                                    <span class="badge {{ $ant->saldo == 0 ? 'badge-gray' : 'badge-red' }}">
                                        {{ $ant->saldo == 0 ? 'Descontado' : 'Pendiente' }}
                                    </span>
                                </td>
                                <td class="no-print">
                                    <a href="{{ route('anticipos.recibo', $ant->id) }}" target="_blank"
                                       class="p-1.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-emerald-400 transition" title="Imprimir Vale">
                                        <i class="fa-solid fa-print text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-8 text-slate-500">No se encontraron anticipos registrados con los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    </div> {{-- /rpt-interactive-view --}}

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- 🏛️ EXECUTIVE CORPORATE REPORT VIEW (PDF EXPORT & PRINT)        --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div id="executiveReportView" class="exec-report-container">

        {{-- ── TAB 3 EXEC: BOCAMINAS ── --}}
        <div id="exec-report-bocamina" x-show="tab === 'bocamina'" class="exec-tab-pane">
            <div class="exec-doc">
                {{-- Corporate Header --}}
                <div class="exec-header">
                    <div class="exec-header-left">
                        <div class="exec-logo-icon">
                            <i class="fa-solid fa-mountain"></i>
                        </div>
                        <div>
                            <h1 class="exec-company-name">EMPRESA MINERA SCPM — TORMAN</h1>
                            <div class="exec-report-title">REPORTE EJECUTIVO Y AUDITORÍA DE COSTOS POR BOCAMINA</div>
                            <div class="exec-report-subtitle">SISTEMA INTEGRADO DE CONTROL OPERATIVO, PLANILLAS Y NÓMINA DE PERSONAL</div>
                        </div>
                    </div>
                    <div class="exec-meta-box">
                        <div class="exec-meta-row">
                            <span class="exec-meta-lbl">N° DOCUMENTO:</span>
                            <span class="exec-meta-val">SCPM-BOC-{{ date('Ymd-His') }}</span>
                        </div>
                        <div class="exec-meta-row">
                            <span class="exec-meta-lbl">FECHA EMISIÓN:</span>
                            <span class="exec-meta-val">{{ now()->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="exec-meta-row">
                            <span class="exec-meta-lbl">AUDITOR:</span>
                            <span class="exec-meta-val">{{ auth()->user()->name ?? 'Administración' }}</span>
                        </div>
                        <div class="exec-meta-row">
                            <span class="exec-meta-lbl">PERIODO:</span>
                            <span class="exec-meta-val">
                                {{ $fechaDesde ? date('d/m/Y', strtotime($fechaDesde)) : 'Historial Total' }} - {{ $fechaHasta ? date('d/m/Y', strtotime($fechaHasta)) : date('d/m/Y') }}
                            </span>
                        </div>
                        <div class="exec-meta-row">
                            <span class="exec-meta-lbl">CERTIFICACIÓN:</span>
                            <span class="exec-badge-audited"><i class="fa-solid fa-circle-check"></i> AUDITADO OFICIAL</span>
                        </div>
                    </div>
                </div>

                {{-- Executive KPI Summary Bar --}}
                <div class="exec-kpi-bar">
                    <div class="exec-kpi-card">
                        <div class="exec-kpi-title">TOTAL BOCAMINAS</div>
                        <div class="exec-kpi-val">{{ count($bocaminasResumen) }} Unidades</div>
                    </div>
                    <div class="exec-kpi-card">
                        <div class="exec-kpi-title">PERSONAL ASIGNADO</div>
                        <div class="exec-kpi-val">{{ $bocaminasResumen->sum('cant_trabajadores') }} Operarios</div>
                    </div>
                    <div class="exec-kpi-card">
                        <div class="exec-kpi-title">TOTAL PLANILLAS</div>
                        <div class="exec-kpi-val" style="color: #2563eb;">Bs. {{ number_format($bocaminasResumen->sum('total_pagos'), 2) }}</div>
                    </div>
                    <div class="exec-kpi-card">
                        <div class="exec-kpi-title">TOTAL ANTICIPOS</div>
                        <div class="exec-kpi-val" style="color: #be123c;">Bs. {{ number_format($bocaminasResumen->sum('total_anticipos'), 2) }}</div>
                    </div>
                    <div class="exec-kpi-card" style="background: #f0fdf4; border-color: #86efac;">
                        <div class="exec-kpi-title" style="color: #15803d;">GRAN TOTAL EGRESADO</div>
                        <div class="exec-kpi-val" style="color: #15803d; font-size: 16px;">Bs. {{ number_format($bocaminasResumen->sum('total_gastado'), 2) }}</div>
                    </div>
                </div>

                {{-- Matriz Resumen Comparativo de Bocaminas --}}
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 11px; font-weight: 800; color: #1e293b; text-transform: uppercase; margin-bottom: 6px; letter-spacing: 0.05em; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-chart-pie" style="color: #d97706;"></i> MATRIZ RESUMEN COMPARATIVO DE COSTOS POR BOCAMINA
                    </div>
                    <table class="exec-table">
                        <thead>
                            <tr>
                                <th style="width: 35px; text-align: center;">N°</th>
                                <th style="text-align: left;">BOCAMINA</th>
                                <th style="width: 140px; text-align: center;">OPERARIOS ASIGNADOS</th>
                                <th style="width: 150px; text-align: right;">TOTAL PLANILLAS (Bs.)</th>
                                <th style="width: 150px; text-align: right;">TOTAL ANTICIPOS (Bs.)</th>
                                <th style="width: 170px; text-align: right;">TOTAL COSTO EGRESADO (Bs.)</th>
                                <th style="width: 100px; text-align: center;">% INCIDENCIA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totGastadoAll = $bocaminasResumen->sum('total_gastado'); @endphp
                            @foreach($bocaminasResumen as $idx => $bRes)
                                @php 
                                    $pct = $totGastadoAll > 0 ? ($bRes['total_gastado'] / $totGastadoAll) * 100 : 0;
                                @endphp
                                <tr>
                                    <td style="text-align: center; font-weight: bold; color: #64748b;">{{ $idx + 1 }}</td>
                                    <td style="font-weight: 800; color: #0f172a;">
                                        <i class="fa-solid fa-mountain" style="color: #059669; font-size: 10px; margin-right: 4px;"></i>
                                        {{ $bRes['bocamina']->nombre }}
                                    </td>
                                    <td style="text-align: center; font-weight: 700;">{{ $bRes['cant_trabajadores'] }} operario(s)</td>
                                    <td style="text-align: right; font-family: monospace; font-weight: 600;">Bs. {{ number_format($bRes['total_pagos'], 2) }}</td>
                                    <td style="text-align: right; font-family: monospace; font-weight: 600; color: #be123c;">Bs. {{ number_format($bRes['total_anticipos'], 2) }}</td>
                                    <td style="text-align: right; font-family: monospace; font-weight: 800; color: #047857;">Bs. {{ number_format($bRes['total_gastado'], 2) }}</td>
                                    <td style="text-align: center; font-family: monospace; font-weight: 700; color: #475569;">{{ number_format($pct, 1) }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="exec-grandtotal-row">
                                <td colspan="2" style="text-align: right; font-weight: 900; text-transform: uppercase;">TOTALES GENERALES CONSOLIDADOS:</td>
                                <td style="text-align: center; font-weight: 900;">{{ $bocaminasResumen->sum('cant_trabajadores') }} operarios</td>
                                <td style="text-align: right; font-family: monospace; font-weight: 900;">Bs. {{ number_format($bocaminasResumen->sum('total_pagos'), 2) }}</td>
                                <td style="text-align: right; font-family: monospace; font-weight: 900; color: #fda4af;">Bs. {{ number_format($bocaminasResumen->sum('total_anticipos'), 2) }}</td>
                                <td style="text-align: right; font-family: monospace; font-weight: 900; color: #6ee7b7;">Bs. {{ number_format($bocaminasResumen->sum('total_gastado'), 2) }}</td>
                                <td style="text-align: center; font-weight: 900;">100.0%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Detailed Breakdown Sections for each Bocamina --}}
                @foreach($bocaminasResumen as $bRes)
                    <div class="exec-section">
                        <div class="exec-section-header">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="background: #f59e0b; color: #000; padding: 2px 7px; border-radius: 4px; font-weight: 900; font-size: 10px;">BOC-{{ $bRes['bocamina']->id }}</span>
                                <span style="font-size: 12px; font-weight: 900; letter-spacing: 0.05em;">BOCAMINA: {{ strtoupper($bRes['bocamina']->nombre) }}</span>
                            </div>
                            <div style="display: flex; gap: 14px; font-size: 10px; font-family: monospace;">
                                <span>Dotación: <strong style="color: #67e8f9;">{{ $bRes['cant_trabajadores'] }} operarios</strong></span>
                                <span>Planillas: <strong style="color: #ffffff;">Bs. {{ number_format($bRes['total_pagos'], 2) }}</strong></span>
                                <span>Anticipos: <strong style="color: #fda4af;">Bs. {{ number_format($bRes['total_anticipos'], 2) }}</strong></span>
                                <span>Total Bocamina: <strong style="color: #6ee7b7; font-size: 11px;">Bs. {{ number_format($bRes['total_gastado'], 2) }}</strong></span>
                            </div>
                        </div>
                        <table class="exec-table">
                            <thead>
                                <tr>
                                    <th style="width: 30px; text-align: center;">#</th>
                                    <th style="text-align: left;">APELLIDOS Y NOMBRES</th>
                                    <th style="width: 85px; text-align: center;">C.I.</th>
                                    <th style="width: 110px; text-align: center;">CARGO / ROL</th>
                                    <th style="width: 140px; text-align: center;">MODALIDAD CONTRATO</th>
                                    <th style="width: 115px; text-align: right;">TOTAL PLANILLAS (Bs.)</th>
                                    <th style="width: 115px; text-align: right;">TOTAL ANTICIPOS (Bs.)</th>
                                    <th style="width: 130px; text-align: right;">TOTAL EGRESADO (Bs.)</th>
                                    <th style="width: 130px; text-align: center;">FIRMA / CONFORMIDAD</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bRes['trabajadores_detalle'] as $idx => $wd)
                                    <tr>
                                        <td style="text-align: center; font-weight: bold; color: #64748b;">{{ $idx + 1 }}</td>
                                        <td style="font-weight: 700; color: #0f172a;">{{ $wd['trabajador']->nombre }}</td>
                                        <td style="text-align: center; font-family: monospace; font-size: 10px; color: #475569;">{{ $wd['trabajador']->ci ?: 'S/N' }}</td>
                                        <td style="text-align: center;">
                                            <span class="exec-badge">{{ ucfirst($wd['trabajador']->rol ?? 'Operario') }}</span>
                                        </td>
                                        <td style="text-align: center; font-size: 10.5px; color: #334155;">{{ $wd['trabajador']->tipoContrato->nombre ?? 'N/A' }}</td>
                                        <td style="text-align: right; font-family: monospace; font-weight: 600; color: #0f172a;">Bs. {{ number_format($wd['pagos'], 2) }}</td>
                                        <td style="text-align: right; font-family: monospace; font-weight: 600; color: #be123c;">Bs. {{ number_format($wd['anticipos'], 2) }}</td>
                                        <td style="text-align: right; font-family: monospace; font-weight: 800; color: #047857;">Bs. {{ number_format($wd['total'], 2) }}</td>
                                        <td style="text-align: center;">
                                            <div class="exec-sig-cell"></div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" style="text-align: center; color: #94a3b8; padding: 12px; font-style: italic;">No hay personal registrado en esta bocamina según los filtros actuales.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="exec-subtotal-row">
                                    <td colspan="5" style="text-align: right; font-weight: 800; text-transform: uppercase;">
                                        SUBTOTAL {{ strtoupper($bRes['bocamina']->nombre) }}:
                                    </td>
                                    <td style="text-align: right; font-family: monospace; font-weight: 800; color: #0f172a;">
                                        Bs. {{ number_format($bRes['total_pagos'], 2) }}
                                    </td>
                                    <td style="text-align: right; font-family: monospace; font-weight: 800; color: #be123c;">
                                        Bs. {{ number_format($bRes['total_anticipos'], 2) }}
                                    </td>
                                    <td style="text-align: right; font-family: monospace; font-weight: 900; color: #047857;">
                                        Bs. {{ number_format($bRes['total_gastado'], 2) }}
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endforeach

                {{-- Triple Audit Signatures --}}
                <div class="exec-signatures">
                    <div class="exec-sig-box">
                        <p class="exec-sig-name">{{ auth()->user()->name ?? 'Encargado de Planillas' }}</p>
                        <p class="exec-sig-title">ELABORADO POR</p>
                        <p class="exec-sig-sub">Responsable de Nómina y Pagos</p>
                    </div>
                    <div class="exec-sig-box">
                        <p class="exec-sig-name">Ing. Residente / Supervisor</p>
                        <p class="exec-sig-title">REVISADO POR</p>
                        <p class="exec-sig-sub">Control Técnico de Operaciones Mina</p>
                    </div>
                    <div class="exec-sig-box">
                        <p class="exec-sig-name">Gerencia General / Finanzas</p>
                        <p class="exec-sig-title">APROBADO POR</p>
                        <p class="exec-sig-sub">Corporación Minera SCPM - TORMAN</p>
                    </div>
                </div>

                {{-- Audit Footer --}}
                <div class="exec-footer">
                    <span>CORPORACIÓN MINERA SCPM — SISTEMA DE CONTROL DE PERSONAL Y COSTOS</span>
                    <span>EMISIÓN: {{ now()->format('d/m/Y H:i:s') }} | AUDITOR: {{ auth()->user()->name ?? 'Admin' }}</span>
                    <span>CONFIDENCIAL &middot; VALIDEZ OFICIAL</span>
                </div>
            </div>
        </div>

        {{-- ── TAB 2 EXEC: TRABAJADORES ── --}}
        <div id="exec-report-trabajador" x-show="tab === 'trabajador'" class="exec-tab-pane">
            <div class="exec-doc">
                <div class="exec-header">
                    <div class="exec-header-left">
                        <div class="exec-logo-icon"><i class="fa-solid fa-user-group"></i></div>
                        <div>
                            <h1 class="exec-company-name">EMPRESA MINERA SCPM — TORMAN</h1>
                            <div class="exec-report-title">REPORTE CONSOLIDADO DE HABERES Y LIQUIDACIONES DE TRABAJADORES</div>
                            <div class="exec-report-subtitle">HISTORIAL AUDITADO DE PAGOS, BONIFICACIONES Y DESCUENTOS POR PLANILLA</div>
                        </div>
                    </div>
                    <div class="exec-meta-box">
                        <div class="exec-meta-row">
                            <span class="exec-meta-lbl">N° DOCUMENTO:</span>
                            <span class="exec-meta-val">SCPM-TRAB-{{ date('Ymd-His') }}</span>
                        </div>
                        <div class="exec-meta-row">
                            <span class="exec-meta-lbl">FECHA:</span>
                            <span class="exec-meta-val">{{ now()->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="exec-meta-row">
                            <span class="exec-meta-lbl">AUDITOR:</span>
                            <span class="exec-meta-val">{{ auth()->user()->name ?? 'Administración' }}</span>
                        </div>
                        <div class="exec-meta-row">
                            <span class="exec-meta-lbl">PERIODO:</span>
                            <span class="exec-meta-val">
                                {{ $fechaDesde ? date('d/m/Y', strtotime($fechaDesde)) : 'Historial Total' }} - {{ $fechaHasta ? date('d/m/Y', strtotime($fechaHasta)) : date('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="exec-kpi-bar">
                    <div class="exec-kpi-card">
                        <div class="exec-kpi-title">TOTAL PLANILLAS</div>
                        <div class="exec-kpi-val">{{ count($listPagosTrabajador) }} Registros</div>
                    </div>
                    <div class="exec-kpi-card">
                        <div class="exec-kpi-title">TOTAL LIQUIDADO BRUTO</div>
                        <div class="exec-kpi-val" style="color: #2563eb;">Bs. {{ number_format($totPagadoTrabajador, 2) }}</div>
                    </div>
                    <div class="exec-kpi-card">
                        <div class="exec-kpi-title">TOTAL ANTICIPOS RETENIDOS</div>
                        <div class="exec-kpi-val" style="color: #be123c;">Bs. {{ number_format($totAnticiposTrabajador, 2) }}</div>
                    </div>
                    <div class="exec-kpi-card" style="background: #f0fdf4; border-color: #86efac;">
                        <div class="exec-kpi-title" style="color: #15803d;">NETO LÍQUIDO RECIBIDO</div>
                        <div class="exec-kpi-val" style="color: #15803d; font-size: 16px;">Bs. {{ number_format($netoRecibidoTrabajador, 2) }}</div>
                    </div>
                </div>

                <table class="exec-table">
                    <thead>
                        <tr>
                            <th style="width: 35px; text-align: center;">ID</th>
                            <th style="width: 75px; text-align: center;">FECHA</th>
                            <th style="text-align: left;">TRABAJADOR</th>
                            <th style="width: 100px; text-align: left;">BOCAMINA</th>
                            <th style="width: 90px; text-align: center;">CARGO</th>
                            <th style="width: 90px; text-align: right;">SUBTOTAL</th>
                            <th style="width: 80px; text-align: right;">BONOS (+)</th>
                            <th style="width: 80px; text-align: right;">DESC. (-)</th>
                            <th style="width: 85px; text-align: right;">ANTICIPOS (-)</th>
                            <th style="width: 105px; text-align: right;">NETO PAGADO</th>
                            <th style="width: 110px; text-align: center;">FIRMA CONFORME</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($listPagosTrabajador as $pago)
                            <tr>
                                <td style="text-align: center; font-family: monospace; font-weight: bold; color: #64748b;">{{ $pago->id }}</td>
                                <td style="text-align: center; font-family: monospace;">{{ $pago->fecha->format('d/m/Y') }}</td>
                                <td style="font-weight: 700; color: #0f172a;">{{ $pago->trabajador->nombre }}</td>
                                <td>{{ $pago->trabajador->bocamina->nombre ?? 'N/A' }}</td>
                                <td style="text-align: center;"><span class="exec-badge">{{ ucfirst($pago->trabajador->rol ?? 'Operario') }}</span></td>
                                <td style="text-align: right; font-family: monospace;">Bs. {{ number_format($pago->subtotal, 2) }}</td>
                                <td style="text-align: right; font-family: monospace; color: #047857;">+{{ number_format($pago->bonos, 2) }}</td>
                                <td style="text-align: right; font-family: monospace; color: #be123c;">-{{ number_format($pago->descuentos, 2) }}</td>
                                <td style="text-align: right; font-family: monospace; color: #be123c;">-{{ number_format($pago->anticipos_descontados, 2) }}</td>
                                <td style="text-align: right; font-family: monospace; font-weight: 800; color: #047857;">Bs. {{ number_format($pago->neto, 2) }}</td>
                                <td style="text-align: center;"><div class="exec-sig-cell"></div></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" style="text-align: center; color: #94a3b8; padding: 12px;">Sin registros con los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="exec-grandtotal-row">
                            <td colspan="5" style="text-align: right; font-weight: 900; text-transform: uppercase;">TOTALES GENERALES:</td>
                            <td style="text-align: right; font-family: monospace; font-weight: 900;">Bs. {{ number_format($listPagosTrabajador->sum('subtotal'), 2) }}</td>
                            <td style="text-align: right; font-family: monospace; font-weight: 900; color: #6ee7b7;">+Bs. {{ number_format($listPagosTrabajador->sum('bonos'), 2) }}</td>
                            <td style="text-align: right; font-family: monospace; font-weight: 900; color: #fda4af;">-Bs. {{ number_format($listPagosTrabajador->sum('descuentos'), 2) }}</td>
                            <td style="text-align: right; font-family: monospace; font-weight: 900; color: #fda4af;">-Bs. {{ number_format($listPagosTrabajador->sum('anticipos_descontados'), 2) }}</td>
                            <td style="text-align: right; font-family: monospace; font-weight: 900; color: #6ee7b7;">Bs. {{ number_format($netoRecibidoTrabajador, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="exec-signatures">
                    <div class="exec-sig-box">
                        <p class="exec-sig-name">{{ auth()->user()->name ?? 'Encargado de Planillas' }}</p>
                        <p class="exec-sig-title">ELABORADO POR</p>
                        <p class="exec-sig-sub">Responsable de Nómina y Pagos</p>
                    </div>
                    <div class="exec-sig-box">
                        <p class="exec-sig-name">Ing. Supervisor / Residente</p>
                        <p class="exec-sig-title">REVISADO POR</p>
                        <p class="exec-sig-sub">Control Técnico de Operaciones Mina</p>
                    </div>
                    <div class="exec-sig-box">
                        <p class="exec-sig-name">Gerencia General / Finanzas</p>
                        <p class="exec-sig-title">APROBADO POR</p>
                        <p class="exec-sig-sub">Corporación Minera SCPM - TORMAN</p>
                    </div>
                </div>

                <div class="exec-footer">
                    <span>CORPORACIÓN MINERA SCPM — CONTROL DE PLANILLAS</span>
                    <span>EMISIÓN: {{ now()->format('d/m/Y H:i:s') }}</span>
                    <span>DOCUMENTO AUDITADO OFICIAL</span>
                </div>
            </div>
        </div>

        {{-- ── TAB 4 EXEC: ANTICIPOS ── --}}
        <div id="exec-report-anticipos" x-show="tab === 'anticipos'" class="exec-tab-pane">
            <div class="exec-doc">
                <div class="exec-header">
                    <div class="exec-header-left">
                        <div class="exec-logo-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                        <div>
                            <h1 class="exec-company-name">EMPRESA MINERA SCPM — TORMAN</h1>
                            <div class="exec-report-title">ESTADO CONSOLIDADO DE VALES DE ANTICIPO Y CUENTAS DEUDORAS</div>
                            <div class="exec-report-subtitle">CONTROL Y SEGUIMIENTO DE SALDOS POR DESCONTAR AL PERSONAL</div>
                        </div>
                    </div>
                    <div class="exec-meta-box">
                        <div class="exec-meta-row">
                            <span class="exec-meta-lbl">N° DOCUMENTO:</span>
                            <span class="exec-meta-val">SCPM-ANT-{{ date('Ymd-His') }}</span>
                        </div>
                        <div class="exec-meta-row">
                            <span class="exec-meta-lbl">FECHA:</span>
                            <span class="exec-meta-val">{{ now()->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="exec-meta-row">
                            <span class="exec-meta-lbl">AUDITOR:</span>
                            <span class="exec-meta-val">{{ auth()->user()->name ?? 'Administración' }}</span>
                        </div>
                    </div>
                </div>

                <div class="exec-kpi-bar">
                    <div class="exec-kpi-card">
                        <div class="exec-kpi-title">VALES EMITIDOS</div>
                        <div class="exec-kpi-val">{{ $antConteo }} Vales</div>
                    </div>
                    <div class="exec-kpi-card">
                        <div class="exec-kpi-title">MONTO TOTAL ANTICIPADO</div>
                        <div class="exec-kpi-val" style="color: #2563eb;">Bs. {{ number_format($antMontoTotal, 2) }}</div>
                    </div>
                    <div class="exec-kpi-card" style="background: #fff1f2; border-color: #fecdd3;">
                        <div class="exec-kpi-title" style="color: #be123c;">SALDO DEUDOR PENDIENTE</div>
                        <div class="exec-kpi-val" style="color: #be123c; font-size: 16px;">Bs. {{ number_format($antMontoPendiente, 2) }}</div>
                    </div>
                    <div class="exec-kpi-card">
                        <div class="exec-kpi-title">TOTAL DESCONTADO</div>
                        <div class="exec-kpi-val" style="color: #047857;">Bs. {{ number_format($antMontoDescontado, 2) }}</div>
                    </div>
                </div>

                <table class="exec-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; text-align: center;">N° VALE</th>
                            <th style="width: 80px; text-align: center;">FECHA</th>
                            <th style="text-align: left;">TRABAJADOR</th>
                            <th style="width: 90px; text-align: center;">C.I.</th>
                            <th style="width: 120px; text-align: left;">BOCAMINA</th>
                            <th style="width: 100px; text-align: center;">CARGO</th>
                            <th style="width: 110px; text-align: right;">MONTO VALE</th>
                            <th style="width: 110px; text-align: right;">DESCONTADO</th>
                            <th style="width: 115px; text-align: right;">SALDO PENDIENTE</th>
                            <th style="width: 95px; text-align: center;">ESTADO</th>
                            <th style="width: 110px; text-align: center;">V°B° / CONFORMIDAD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($listAnticiposTab as $ant)
                            @php $desc = $ant->monto - $ant->saldo; @endphp
                            <tr>
                                <td style="text-align: center; font-family: monospace; font-weight: bold;">#{{ $ant->id }}</td>
                                <td style="text-align: center; font-family: monospace;">{{ $ant->fecha->format('d/m/Y') }}</td>
                                <td style="font-weight: 700; color: #0f172a;">{{ $ant->trabajador->nombre }}</td>
                                <td style="text-align: center; font-family: monospace; font-size: 10px;">{{ $ant->trabajador->ci ?: 'S/N' }}</td>
                                <td>{{ $ant->trabajador->bocamina->nombre ?? 'N/A' }}</td>
                                <td style="text-align: center;"><span class="exec-badge">{{ ucfirst($ant->trabajador->rol ?? 'Operario') }}</span></td>
                                <td style="text-align: right; font-family: monospace; font-weight: 600;">Bs. {{ number_format($ant->monto, 2) }}</td>
                                <td style="text-align: right; font-family: monospace; font-weight: 600; color: #047857;">Bs. {{ number_format($desc, 2) }}</td>
                                <td style="text-align: right; font-family: monospace; font-weight: 800; color: {{ $ant->saldo > 0 ? '#be123c' : '#64748b' }};">
                                    Bs. {{ number_format($ant->saldo, 2) }}
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-size: 8.5px; font-weight: 800; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; background: {{ $ant->saldo == 0 ? '#e2e8f0; color: #475569;' : '#fee2e2; color: #b91c1c;' }}">
                                        {{ $ant->saldo == 0 ? 'Liquidado' : 'Pendiente' }}
                                    </span>
                                </td>
                                <td style="text-align: center;"><div class="exec-sig-cell"></div></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" style="text-align: center; color: #94a3b8; padding: 12px;">Sin anticipos con los filtros seleccionados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="exec-grandtotal-row">
                            <td colspan="6" style="text-align: right; font-weight: 900; text-transform: uppercase;">TOTALES DEUDORES:</td>
                            <td style="text-align: right; font-family: monospace; font-weight: 900;">Bs. {{ number_format($antMontoTotal, 2) }}</td>
                            <td style="text-align: right; font-family: monospace; font-weight: 900; color: #6ee7b7;">Bs. {{ number_format($antMontoDescontado, 2) }}</td>
                            <td style="text-align: right; font-family: monospace; font-weight: 900; color: #fda4af;">Bs. {{ number_format($antMontoPendiente, 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>

                <div class="exec-signatures">
                    <div class="exec-sig-box">
                        <p class="exec-sig-name">{{ auth()->user()->name ?? 'Encargado de Planillas' }}</p>
                        <p class="exec-sig-title">ELABORADO POR</p>
                        <p class="exec-sig-sub">Responsable de Nómina y Anticipos</p>
                    </div>
                    <div class="exec-sig-box">
                        <p class="exec-sig-name">Ing. Supervisor / Residente</p>
                        <p class="exec-sig-title">REVISADO POR</p>
                        <p class="exec-sig-sub">Control Técnico de Operaciones Mina</p>
                    </div>
                    <div class="exec-sig-box">
                        <p class="exec-sig-name">Gerencia General / Finanzas</p>
                        <p class="exec-sig-title">APROBADO POR</p>
                        <p class="exec-sig-sub">Corporación Minera SCPM - TORMAN</p>
                    </div>
                </div>

                <div class="exec-footer">
                    <span>CORPORACIÓN MINERA SCPM — CONTROL DE ANTICIPOS</span>
                    <span>EMISIÓN: {{ now()->format('d/m/Y H:i:s') }}</span>
                    <span>DOCUMENTO AUDITADO OFICIAL</span>
                </div>
            </div>
        </div>

        {{-- ── TAB 1 EXEC: RESUMEN GENERAL ── --}}
        <div id="exec-report-general" x-show="tab === 'general'" class="exec-tab-pane">
            <div class="exec-doc">
                <div class="exec-header">
                    <div class="exec-header-left">
                        <div class="exec-logo-icon"><i class="fa-solid fa-chart-pie"></i></div>
                        <div>
                            <h1 class="exec-company-name">EMPRESA MINERA SCPM — TORMAN</h1>
                            <div class="exec-report-title">INFORME GERENCIAL Y BALANCE FINANCIERO OPERATIVO</div>
                            <div class="exec-report-subtitle">CONSOLIDADO DE FONDOS DE CAJA, PLANILLAS Y FLUJO DE EGRESOS</div>
                        </div>
                    </div>
                    <div class="exec-meta-box">
                        <div class="exec-meta-row">
                            <span class="exec-meta-lbl">N° DOCUMENTO:</span>
                            <span class="exec-meta-val">SCPM-GEN-{{ date('Ymd-His') }}</span>
                        </div>
                        <div class="exec-meta-row">
                            <span class="exec-meta-lbl">FECHA:</span>
                            <span class="exec-meta-val">{{ now()->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="exec-meta-row">
                            <span class="exec-meta-lbl">AUDITOR:</span>
                            <span class="exec-meta-val">{{ auth()->user()->name ?? 'Administración' }}</span>
                        </div>
                    </div>
                </div>

                <div class="exec-kpi-bar">
                    <div class="exec-kpi-card">
                        <div class="exec-kpi-title">TOTAL RECARGADO CAJA</div>
                        <div class="exec-kpi-val" style="color: #2563eb;">Bs. {{ number_format($genTotalRecargado, 2) }}</div>
                    </div>
                    <div class="exec-kpi-card">
                        <div class="exec-kpi-title">TOTAL PAGOS PLANILLA</div>
                        <div class="exec-kpi-val" style="color: #7c3aed;">Bs. {{ number_format($genTotalPagado, 2) }}</div>
                    </div>
                    <div class="exec-kpi-card">
                        <div class="exec-kpi-title">TOTAL ANTICIPOS</div>
                        <div class="exec-kpi-val" style="color: #be123c;">Bs. {{ number_format($genTotalAnticipos, 2) }}</div>
                    </div>
                    <div class="exec-kpi-card" style="background: {{ $genSaldoCaja >= 0 ? '#f0fdf4; border-color: #86efac;' : '#fff1f2; border-color: #fecdd3;' }}">
                        <div class="exec-kpi-title" style="color: {{ $genSaldoCaja >= 0 ? '#15803d;' : '#be123c;' }}">SALDO DISPONIBLE CAJA</div>
                        <div class="exec-kpi-val" style="color: {{ $genSaldoCaja >= 0 ? '#15803d;' : '#be123c;' }}; font-size: 16px;">
                            Bs. {{ number_format(abs($genSaldoCaja), 2) }}
                        </div>
                    </div>
                    <div class="exec-kpi-card">
                        <div class="exec-kpi-title">TRABAJADORES ACTIVOS</div>
                        <div class="exec-kpi-val">{{ $genTrabajadoresActivos }} Operarios</div>
                    </div>
                </div>

                {{-- Bocamina summary table --}}
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 11px; font-weight: 800; color: #1e293b; text-transform: uppercase; margin-bottom: 6px;">
                        ⛏️ DISTRIBUCIÓN DE EGRESOS POR BOCAMINA
                    </div>
                    <table class="exec-table">
                        <thead>
                            <tr>
                                <th style="width: 40px; text-align: center;">#</th>
                                <th style="text-align: left;">BOCAMINA</th>
                                <th style="text-align: right;">TOTAL EGRESADO (Bs.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bocaminasChart as $idx => $bc)
                                <tr>
                                    <td style="text-align: center; font-weight: bold; color: #64748b;">{{ $idx + 1 }}</td>
                                    <td style="font-weight: 700; color: #0f172a;">{{ $bc['nombre'] }}</td>
                                    <td style="text-align: right; font-family: monospace; font-weight: 800; color: #047857;">Bs. {{ number_format($bc['total'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Weekly expenses table --}}
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 11px; font-weight: 800; color: #1e293b; text-transform: uppercase; margin-bottom: 6px;">
                        📅 FLUJO HISTÓRICO SEMANAL (ÚLTIMAS 8 SEMANAS)
                    </div>
                    <table class="exec-table">
                        <thead>
                            <tr>
                                <th style="text-align: left;">SEMANA</th>
                                <th style="text-align: right;">PAGOS PLANILLAS (Bs.)</th>
                                <th style="text-align: right;">ANTICIPOS (Bs.)</th>
                                <th style="text-align: right;">TOTAL SEMANAL (Bs.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($semanasChart as $sem)
                                <tr>
                                    <td style="font-weight: 700; color: #0f172a;">{{ $sem['label'] }}</td>
                                    <td style="text-align: right; font-family: monospace;">Bs. {{ number_format($sem['pagos'], 2) }}</td>
                                    <td style="text-align: right; font-family: monospace; color: #be123c;">Bs. {{ number_format($sem['anticipos'], 2) }}</td>
                                    <td style="text-align: right; font-family: monospace; font-weight: 800; color: #047857;">Bs. {{ number_format($sem['total'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="exec-signatures">
                    <div class="exec-sig-box">
                        <p class="exec-sig-name">{{ auth()->user()->name ?? 'Encargado de Planillas' }}</p>
                        <p class="exec-sig-title">ELABORADO POR</p>
                        <p class="exec-sig-sub">Responsable de Nómina y Pagos</p>
                    </div>
                    <div class="exec-sig-box">
                        <p class="exec-sig-name">Ing. Supervisor / Residente</p>
                        <p class="exec-sig-title">REVISADO POR</p>
                        <p class="exec-sig-sub">Control Técnico de Operaciones Mina</p>
                    </div>
                    <div class="exec-sig-box">
                        <p class="exec-sig-name">Gerencia General / Finanzas</p>
                        <p class="exec-sig-title">APROBADO POR</p>
                        <p class="exec-sig-sub">Corporación Minera SCPM - TORMAN</p>
                    </div>
                </div>

                <div class="exec-footer">
                    <span>CORPORACIÓN MINERA SCPM — BALANCE GENERAL</span>
                    <span>EMISIÓN: {{ now()->format('d/m/Y H:i:s') }}</span>
                    <span>DOCUMENTO AUDITADO OFICIAL</span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Delay initialization slightly so Alpine.js has time to render the tab container.
    // This prevents Chart.js from rendering at 0x0 pixels due to display:none (x-cloak).
    setTimeout(() => {
        // Chart 1: Gastos por Semana
        const ctxSemana = document.getElementById('chartGastosSemana');
        if (ctxSemana) {
            const dataSemana = @json($semanasChart);
            new Chart(ctxSemana, {
                type: 'bar',
                data: {
                    labels: dataSemana.map(d => d.label),
                    datasets: [
                        {
                            label: 'Planillas (Pagos)',
                            data: dataSemana.map(d => d.pagos),
                            backgroundColor: '#6366f1',
                            borderRadius: 6
                        },
                        {
                            label: 'Anticipos',
                            data: dataSemana.map(d => d.anticipos),
                            backgroundColor: '#f43f5e',
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { labels: { color: '#94a3b8', font: { family: 'Outfit', weight: 'bold' } } }
                    },
                    scales: {
                        x: { ticks: { color: '#94a3b8' }, grid: { display: false } },
                        y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,0.05)' } }
                    }
                }
            });
        }

        // Chart 2: Gastos por Bocamina
        const ctxBocamina = document.getElementById('chartGastosBocamina');
        if (ctxBocamina) {
            const dataBocamina = @json($bocaminasChart);
            new Chart(ctxBocamina, {
                type: 'doughnut',
                data: {
                    labels: dataBocamina.map(d => d.nombre),
                    datasets: [{
                        data: dataBocamina.map(d => d.total),
                        backgroundColor: ['#10b981', '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { color: '#94a3b8', font: { family: 'Outfit', weight: 'bold' } } }
                    }
                }
            });
        }
    }, 300); // 300ms delay ensures the DOM is fully visible before Chart.js measures it
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
function doExportPDF() {
    const btn = event ? event.currentTarget : null;
    const originalHtml = btn ? btn.innerHTML : '';
    if (btn) btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generando PDF...';

    // 1. Detect active tab
    const alpineEl = document.querySelector('.rpt-page');
    let currentTab = 'bocamina';
    if (alpineEl && alpineEl.__x && alpineEl.__x.$data) {
        currentTab = alpineEl.__x.$data.tab;
    } else {
        const urlParams = new URLSearchParams(window.location.search);
        currentTab = urlParams.get('tab') || 'general';
    }

    // 2. Locate target element inside executive report view
    const sourceEl = document.getElementById('exec-report-' + currentTab);
    if (!sourceEl) {
        window.print();
        if (btn) btn.innerHTML = originalHtml;
        return;
    }

    // 3. Clone and wrap in a clean 1140px landscape rendering container
    const wrapper = document.createElement('div');
    wrapper.style.position = 'fixed';
    wrapper.style.left = '-9999px';
    wrapper.style.top = '0';
    wrapper.style.width = '1140px';
    wrapper.style.maxWidth = '1140px';
    wrapper.style.background = '#ffffff';
    wrapper.style.color = '#0f172a';
    wrapper.style.zIndex = '-1000';
    wrapper.style.padding = '12px 18px';

    const clone = sourceEl.cloneNode(true);
    clone.style.display = 'block';
    wrapper.appendChild(clone);
    document.body.appendChild(wrapper);

    const tabNames = {
        'bocamina': 'Bocaminas',
        'trabajador': 'Trabajadores',
        'anticipos': 'Anticipos',
        'general': 'General'
    };
    const tabName = tabNames[currentTab] || 'Personal';

    const opt = {
        margin:       [6, 6, 6, 6],
        filename:     'Reporte_Ejecutivo_SCPM_' + tabName + '_' + new Date().toISOString().slice(0,10) + '.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { 
            scale: 2, 
            useCORS: true, 
            backgroundColor: '#ffffff',
            windowWidth: 1160,
            scrollX: 0,
            scrollY: 0
        },
        jsPDF:        { unit: 'mm', format: 'letter', orientation: 'landscape' },
        pagebreak:    { mode: ['avoid-all', 'css', 'legacy'] }
    };

    html2pdf().set(opt).from(wrapper).save().then(() => {
        wrapper.remove();
        if (btn) btn.innerHTML = originalHtml;
    }).catch(err => {
        console.error('Error generating PDF:', err);
        wrapper.remove();
        if (btn) btn.innerHTML = originalHtml;
        window.print();
    });
}

function doExportExcel() {
    const alpineEl = document.querySelector('.rpt-page');
    let currentTab = 'bocamina';
    if (alpineEl && alpineEl.__x && alpineEl.__x.$data) {
        currentTab = alpineEl.__x.$data.tab;
    } else {
        const urlParams = new URLSearchParams(window.location.search);
        currentTab = urlParams.get('tab') || 'general';
    }

    const execContainer = document.getElementById('exec-report-' + currentTab);
    const tables = execContainer ? execContainer.querySelectorAll('table.exec-table') : [];
    
    let rowsHtml = '';
    if (tables.length > 0) {
        tables.forEach(table => {
            const rows = table.querySelectorAll('tr');
            rows.forEach(tr => {
                const isHeader = tr.parentElement && tr.parentElement.tagName === 'THEAD';
                const cells = tr.querySelectorAll(isHeader ? 'th' : 'td');
                let rowStr = '<tr>';
                cells.forEach(cell => {
                    const txt = cell.textContent.replace(/\s+/g, ' ').trim();
                    if (isHeader) {
                        rowStr += `<th style="background-color:#0f172a; color:#ffffff; font-weight:bold; padding:8px; border:1px solid #334155; text-align:center;">${txt}</th>`;
                    } else {
                        const isNum = txt.startsWith('Bs.') || !isNaN(parseFloat(txt.replace('Bs.','').replace(/,/g,'')));
                        rowStr += `<td style="border:1px solid #cbd5e1; padding:6px 10px; ${isNum ? 'text-align:right;' : ''}">${txt}</td>`;
                    }
                });
                rowStr += '</tr>';
                rowsHtml += rowStr;
            });
            rowsHtml += '<tr><td colspan="9">&nbsp;</td></tr>';
        });
    } else {
        const activeTable = document.querySelector('.rpt-page table:not(.no-print)');
        if (!activeTable) {
            alert('No hay datos en la tabla para exportar.');
            return;
        }
        const rows = activeTable.querySelectorAll('tr');
        rows.forEach(tr => {
            const isHeader = tr.parentElement && tr.parentElement.tagName === 'THEAD';
            const cells = tr.querySelectorAll(isHeader ? 'th' : 'td');
            let rowStr = '<tr>';
            cells.forEach(td => {
                const txt = td.textContent.replace(/\s+/g, ' ').trim();
                if (isHeader) {
                    rowStr += `<th style="background-color:#059669; color:#ffffff; font-weight:bold; padding:8px; border:1px solid #047857; text-align:center;">${txt}</th>`;
                } else {
                    rowStr += `<td style="border:1px solid #cbd5e1; padding:7px 10px;">${txt}</td>`;
                }
            });
            rowStr += '</tr>';
            rowsHtml += rowStr;
        });
    }

    const tabNames = { 'bocamina': 'Bocaminas', 'trabajador': 'Trabajadores', 'anticipos': 'Anticipos', 'general': 'General' };
    const tabName = tabNames[currentTab] || 'Personal';

    const htmlContent = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="utf-8">
            <style>
                body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #1e293b; }
                .header-banner { background-color: #0f172a; color: #f59e0b; font-size: 15px; font-weight: bold; text-align: center; padding: 14px; }
                .info-sub { background-color: #1e293b; color: #ffffff; font-size: 10px; font-weight: bold; padding: 6px 12px; }
            </style>
        </head>
        <body>
            <table style="width:100%; border-collapse:collapse;">
                <tr><td colspan="9" class="header-banner">CORPORACIÓN MINERA SCPM — REPORTE OFICIAL DE ${tabName.toUpperCase()}</td></tr>
                <tr><td colspan="9" class="info-sub">FECHA DE GENERACIÓN: ${new Date().toLocaleDateString('es-BO')} ${new Date().toLocaleTimeString('es-BO')}</td></tr>
                <tr><td colspan="9">&nbsp;</td></tr>
                ${rowsHtml}
            </table>
        </body>
        </html>
    `;

    const blob = new Blob(['\ufeff' + htmlContent], { type: 'application/vnd.ms-excel;charset=utf-8' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'Reporte_SCPM_' + tabName + '_' + new Date().toISOString().slice(0,10) + '.xls';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endpush
