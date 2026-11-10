@extends('layouts.app')

@section('title', 'Personal y Contratos')

@section('content')
<!-- Custom Premium Styles for Personal y Contratos View -->
<style>
    /* Theme variables adapting dynamically to light-theme/dark-theme */
    :root {
        --modal-bg: #0a0f1d;
        --modal-header-bg: #0f1527;
        --modal-border: rgba(99, 102, 241, 0.15); /* Subtle indigo border glow */
        --modal-text-main: #f8fafc;
        --modal-text-muted: #94a3b8;
        --modal-input-bg: #060a13;
        --modal-input-border: rgba(99, 102, 241, 0.25); /* Indigo tinted borders in dark mode */
        --modal-input-text: #f8fafc;
        --modal-input-placeholder: #475569;
        --modal-input-focus-bg: #03060c;
        --modal-accent: #6366f1; /* Deep Indigo Accent */
        --modal-accent-gradient: linear-gradient(135deg, #6366f1 0%, #0ea5e9 100%);
        --modal-accent-glow: rgba(99, 102, 241, 0.2);
        --modal-footer-bg: #0f1527;
        --modal-btn-cancel-bg: #1e293b;
        --modal-btn-cancel-text: #94a3b8;
        --modal-btn-cancel-border: rgba(255, 255, 255, 0.08);
        --backdrop-color: rgba(2, 6, 23, 0.55);
    }

    .light-theme {
        --modal-bg: #f8fafc; /* Elegant light-grey body background */
        --modal-header-bg: #ffffff; /* Clean white header */
        --modal-border: rgba(79, 70, 229, 0.08);
        --modal-text-main: #0f172a;
        --modal-text-muted: #475569;
        --modal-input-bg: #ffffff; /* White input backgrounds for contrast */
        --modal-input-border: rgba(79, 70, 229, 0.2); /* Soft Indigo/Blue borders in light mode */
        --modal-input-text: #0f172a;
        --modal-input-placeholder: #94a3b8;
        --modal-input-focus-bg: #ffffff;
        --modal-accent: #4f46e5; /* Royal Indigo Accent */
        --modal-accent-gradient: linear-gradient(135deg, #4f46e5 0%, #0284c7 100%);
        --modal-accent-glow: rgba(79, 70, 229, 0.12);
        --modal-footer-bg: #ffffff; /* Clean white footer */
        --modal-btn-cancel-bg: #f1f5f9;
        --modal-btn-cancel-text: #334155;
        --modal-btn-cancel-border: rgba(15, 23, 42, 0.08);
        --backdrop-color: rgba(15, 23, 42, 0.28); /* Subtle dark overlay in light mode */
    }

    /* Premium Centered Filter Inputs & Dropdowns */
    .premium-filter-input {
        text-align: center !important;
        text-align-last: center !important;
        font-weight: 700 !important;
        transition: all 0.2s ease !important;
    }
    
    .light-theme .premium-filter-input {
        color: #0f172a !important;
        background-color: #ffffff !important;
        border: 1.5px solid rgba(79, 70, 229, 0.22) !important;
        box-shadow: 
            inset 0 1.5px 3px rgba(15, 23, 42, 0.05), 
            0 1px 0 rgba(255, 255, 255, 0.8) !important;
    }

    html:not(.light-theme) .premium-filter-input {
        color: #f8fafc !important;
        background-color: #060a13 !important;
        border: 1.5px solid rgba(99, 102, 241, 0.25) !important;
        box-shadow: 
            inset 0 2px 4px rgba(0, 0, 0, 0.06), 
            0 1px 0 rgba(255, 255, 255, 0.04) !important;
    }

    .premium-filter-input:hover {
        border-color: var(--modal-accent) !important;
    }

    .premium-filter-input:focus {
        outline: none !important;
        border-color: var(--modal-accent) !important;
        box-shadow: 
            0 0 0 4px var(--modal-accent-glow), 
            inset 0 1px 2px rgba(0, 0, 0, 0.08) !important;
        transform: translateY(-0.5px);
    }

    /* Bulletproof backdrop wrapper position & styling covering the ENTIRE viewport */
    .premium-backdrop {
        position: fixed !important;
        inset: 0 !important; /* Spans the entire screen */
        z-index: 99999 !important; /* Above sidebar and header */
        display: flex; /* Removed !important to allow AlpineJS x-show (display: none) to work! */
        align-items: center !important;
        justify-content: center !important;
        padding: 2.5rem 1.5rem !important; /* Margins around the modal so it floats */
        background: var(--backdrop-color) !important;
        backdrop-filter: blur(2px) !important; /* Subtle blur: "no tan borroso" */
        -webkit-backdrop-filter: blur(2px) !important;
        transition: all 0.3s ease !important;
    }

    /* Bulletproof modal box constraint with a gradient top accent line - WIDER for spacious view */
    .premium-form-modal {
        max-width: 900px !important; /* Made wider: "un poquito mas grandecito" */
        width: 100% !important;
        background: var(--modal-bg) !important;
        color: var(--modal-text-main) !important;
        border: 1px solid var(--modal-border) !important;
        /* Spectacular 3D Shadow with Top Inner Bevel Highlight */
        box-shadow: 
            0 20px 50px -12px rgba(0, 0, 0, 0.3), 
            0 30px 80px -20px rgba(99, 102, 241, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.08) !important;
        border-radius: 24px !important; /* Curveadas las puntitas */
        overflow: hidden !important;
        position: relative !important;
        display: block !important; /* Replaced flex with block layout to solve flexbox height overflow bug */
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    .light-theme .premium-form-modal {
        box-shadow: 
            0 20px 40px -10px rgba(15, 23, 42, 0.12), 
            0 30px 80px -20px rgba(79, 70, 229, 0.1),
            inset 0 1px 0 rgba(255, 255, 255, 0.6) !important;
    }

    /* Premium Top Accent Gradient Bar - Colorful Royal Blue / Indigo / Emerald */
    .premium-form-modal::before {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 4px !important;
        background: linear-gradient(90deg, #4f46e5, #0ea5e9, #10b981) !important;
        z-index: 10 !important;
    }
    
    .premium-form-modal .modal-header {
        background: var(--modal-header-bg) !important;
        border-bottom: 1.5px solid var(--modal-border) !important;
        padding: 1.75rem 2.5rem !important; /* Spacious padding */
    }
    
    /* Strict height cap & native scroll on body to ensure footer fits on all screens */
    .premium-form-modal .modal-body {
        background: var(--modal-bg) !important;
        padding: 2.25rem 2.5rem !important; /* Spacious body padding: "grandecito arriba y abajo" */
        max-height: 60vh !important; /* Taller for more space on larger screen heights */
        overflow-y: auto !important; /* Native internal scrollbar */
    }
    
    .premium-form-modal .modal-footer {
        background: var(--modal-footer-bg) !important;
        border-top: 1.5px solid var(--modal-border) !important;
        padding: 1.5rem 2.5rem !important; /* Spacious footer padding */
    }

    /* Decorative jewel icon container at the header */
    .premium-form-modal .jewel-icon-container {
        width: 2.25rem !important;
        height: 2.25rem !important;
        border-radius: 10px !important;
        background: rgba(99, 102, 241, 0.08) !important;
        border: 1.5px solid rgba(99, 102, 241, 0.22) !important;
        color: var(--modal-accent) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.1) !important;
    }

    .light-theme .premium-form-modal .jewel-icon-container {
        background: rgba(79, 70, 229, 0.08) !important;
        border: 1.5px solid rgba(79, 70, 229, 0.22) !important;
    }
    
    /* Section title styling with left accent bar and colored titles */
    .premium-form-modal .section-title {
        font-size: 11.5px !important;
        font-weight: 800 !important;
        letter-spacing: 0.15em !important;
        text-transform: uppercase !important;
        padding-bottom: 0.5rem !important;
        margin-bottom: 1.25rem !important;
        display: flex !important;
        align-items: center !important;
        gap: 0.6rem !important;
        border-bottom: 1px solid var(--modal-border) !important;
    }

    /* Custom underline highlights for personal vs laboral */
    .premium-form-modal .section-personal {
        color: #0ea5e9 !important; /* Sky Blue */
        border-bottom-color: rgba(14, 165, 233, 0.15) !important;
        border-left: 3.5px solid #0ea5e9 !important;
        padding-left: 0.75rem !important;
    }

    .premium-form-modal .section-laboral {
        color: #10b981 !important; /* Emerald Green */
        border-bottom-color: rgba(16, 185, 129, 0.15) !important;
        border-left: 3.5px solid #10b981 !important;
        padding-left: 0.75rem !important;
    }

    .premium-form-modal label {
        color: var(--modal-text-muted) !important;
        font-size: 10.5px !important;
        font-weight: 700 !important;
        letter-spacing: 0.08em !important;
        text-transform: uppercase !important;
        margin-bottom: 0.45rem !important;
        display: block !important;
    }
    
    /* Recessed 3D effect on inputs */
    .premium-form-modal input[type="text"], 
    .premium-form-modal input[type="number"], 
    .premium-form-modal input[type="date"], 
    .premium-form-modal select, 
    .premium-form-modal textarea {
        border-radius: 12px !important;
        padding: 0.75rem 1.1rem !important; /* Slightly taller inputs: "grandecito arriba y abajo" */
        font-size: 13.5px !important;
        font-family: 'Outfit', sans-serif !important;
        width: 100% !important;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    }

    /* Light theme inputs text color and background - EXPLICIT OVERRIDES TO PREVENT FAINT TEXT */
    .light-theme .premium-form-modal input[type="text"], 
    .light-theme .premium-form-modal input[type="number"], 
    .light-theme .premium-form-modal input[type="date"], 
    .light-theme select, 
    .light-theme textarea {
        color: #0f172a !important; /* Dark Slate Navy content text - highly visible */
        background-color: #ffffff !important; /* Solid white background */
        border: 1.5px solid rgba(79, 70, 229, 0.22) !important;
        box-shadow: 
            inset 0 1.5px 3px rgba(15, 23, 42, 0.06), 
            0 1px 0 rgba(255, 255, 255, 0.8) !important;
    }

    /* Dark theme inputs text color and background - EXPLICIT OVERRIDES */
    html:not(.light-theme) .premium-form-modal input[type="text"], 
    html:not(.light-theme) .premium-form-modal input[type="number"], 
    html:not(.light-theme) .premium-form-modal input[type="date"], 
    html:not(.light-theme) select, 
    html:not(.light-theme) textarea {
        color: #f8fafc !important; /* F8FAFC white text */
        background-color: #060a13 !important; /* Dark navy background */
        border: 1.5px solid rgba(99, 102, 241, 0.25) !important;
        box-shadow: 
            inset 0 2px 4px rgba(0, 0, 0, 0.06), 
            0 1px 0 rgba(255, 255, 255, 0.04) !important;
    }
    
    .premium-form-modal input[type="text"]::placeholder,
    .premium-form-modal input[type="number"]::placeholder,
    .premium-form-modal textarea::placeholder {
        color: var(--modal-input-placeholder) !important;
    }
    
    .premium-form-modal input[type="text"]:hover, 
    .premium-form-modal input[type="number"]:hover, 
    .premium-form-modal input[type="date"]:hover, 
    .premium-form-modal select:hover, 
    .premium-form-modal textarea:hover {
        border-color: var(--modal-accent) !important;
        box-shadow: 
            inset 0 2px 4px rgba(0, 0, 0, 0.06),
            0 0 0 2px var(--modal-accent-glow) !important;
    }
    
    .premium-form-modal input[type="text"]:focus, 
    .premium-form-modal input[type="number"]:focus, 
    .premium-form-modal input[type="date"]:focus, 
    .premium-form-modal select:focus, 
    .premium-form-modal textarea:focus {
        outline: none !important;
        border-color: var(--modal-accent) !important;
        box-shadow: 
            0 0 0 4px var(--modal-accent-glow), 
            0 6px 16px -2px rgba(99, 102, 241, 0.12),
            inset 0 1px 2px rgba(0, 0, 0, 0.08) !important;
        transform: translateY(-0.5px);
    }

    /* Premium Input Group with Left Prefix Badge (Stripe-like 3D Group) */
    .premium-input-group {
        display: flex !important;
        align-items: stretch !important;
        border-radius: 12px !important;
        overflow: hidden !important;
        transition: all 0.2s ease !important;
        width: 100% !important;
    }
    
    .light-theme .premium-input-group {
        background-color: #ffffff !important;
        border: 1.5px solid rgba(79, 70, 229, 0.22) !important;
        box-shadow: 
            inset 0 1.5px 3px rgba(15, 23, 42, 0.06), 
            0 1px 0 rgba(255, 255, 255, 0.8) !important;
    }

    html:not(.light-theme) .premium-input-group {
        background-color: #060a13 !important;
        border: 1.5px solid rgba(99, 102, 241, 0.25) !important;
        box-shadow: 
            inset 0 2px 4px rgba(0, 0, 0, 0.06), 
            0 1px 0 rgba(255, 255, 255, 0.04) !important;
    }

    .premium-input-group .prefix-badge {
        display: inline-flex !important;
        align-items: center !important;
        padding: 0 1rem !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        user-select: none !important;
    }

    .light-theme .premium-input-group .prefix-badge {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border-right: 1.5px solid rgba(79, 70, 229, 0.18) !important;
    }

    html:not(.light-theme) .premium-input-group .prefix-badge {
        background-color: #0b0f19 !important;
        color: #94a3b8 !important;
        border-right: 1.5px solid rgba(99, 102, 241, 0.18) !important;
    }

    .premium-input-group input {
        flex: 1 !important;
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
        padding: 0.75rem 1.1rem !important;
        outline: none !important;
        margin: 0 !important;
    }

    .light-theme .premium-input-group input {
        color: #0f172a !important;
    }

    html:not(.light-theme) .premium-input-group input {
        color: #f8fafc !important;
    }

    .premium-input-group:hover {
        border-color: var(--modal-accent) !important;
    }

    .premium-input-group:focus-within {
        border-color: var(--modal-accent) !important;
        box-shadow: 
            0 0 0 4px var(--modal-accent-glow), 
            0 6px 16px -2px rgba(99, 102, 241, 0.12),
            inset 0 1px 2px rgba(0, 0, 0, 0.08) !important;
        transform: translateY(-0.5px);
    }

    /* Webkit datepicker calendar icon */
    .premium-form-modal input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
    }
    
    .light-theme .premium-form-modal input[type="date"]::-webkit-calendar-picker-indicator {
        filter: none !important;
    }
    
    html:not(.light-theme) .premium-form-modal input[type="date"]::-webkit-calendar-picker-indicator {
        filter: invert(1) brightness(0.95) !important;
    }

    .premium-form-modal .help-text {
        font-size: 10px !important;
        color: var(--modal-text-muted) !important;
        opacity: 0.85;
        margin-top: 0.35rem !important;
        display: block !important;
    }

    .premium-form-modal .error-message {
        background-color: rgba(244, 63, 94, 0.08) !important;
        border: 1px solid rgba(244, 63, 94, 0.2) !important;
        color: #f43f5e !important; /* Rose-500 */
        font-size: 11px !important;
        font-weight: 605;
        padding: 0.4rem 0.7rem !important;
        border-radius: 8px !important;
        margin-top: 0.4rem !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.4rem !important;
    }

    /* Tactile 3D primary save button */
    .btn-3d-save {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%) !important;
        color: #ffffff !important;
        border: 1px solid #4338ca !important;
        border-bottom: 4px solid #312e81 !important;
        box-shadow: 
            0 4px 10px rgba(79, 70, 229, 0.25), 
            inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
        font-weight: 800 !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.15);
        transform: translateY(0);
        transition: all 0.15s ease !important;
        cursor: pointer !important;
    }
    
    .btn-3d-save:hover:not(:disabled) {
        background: linear-gradient(135deg, #5a4ff3 0%, #4892fc 100%) !important;
        transform: translateY(-1px);
        box-shadow: 
            0 6px 15px rgba(79, 70, 229, 0.35), 
            inset 0 1px 0 rgba(255, 255, 255, 0.25) !important;
    }
    
    .btn-3d-save:active:not(:disabled) {
        transform: translateY(2px) !important;
        border-bottom-width: 1px !important;
        box-shadow: 0 2px 4px rgba(79, 70, 229, 0.1) !important;
    }

    .btn-3d-save:disabled {
        background: #e2e8f0 !important; /* Soft slate grey in light mode */
        color: #94a3b8 !important;
        border: 1px solid #cbd5e1 !important;
        border-bottom: 1px solid #cbd5e1 !important;
        box-shadow: none !important;
        transform: none !important;
        cursor: not-allowed !important;
        opacity: 0.85 !important;
    }

    html:not(.light-theme) .btn-3d-save:disabled {
        background: #1e293b !important; /* Slate-800 in dark mode */
        color: #475569 !important;
        border: 1px solid #334155 !important;
        border-bottom: 1px solid #334155 !important;
        box-shadow: none !important;
        transform: none !important;
        opacity: 0.85 !important;
    }

    /* Tactile 3D cancel button */
    .btn-3d-cancel {
        background: var(--modal-btn-cancel-bg) !important;
        color: var(--modal-btn-cancel-text) !important;
        border: 1.5px solid var(--modal-btn-cancel-border) !important;
        border-bottom: 3.5px solid rgba(0, 0, 0, 0.25) !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
        font-weight: 700 !important;
        transition: all 0.15s ease !important;
    }

    .light-theme .btn-3d-cancel {
        border-bottom: 3.5px solid rgba(148, 163, 184, 0.4) !important;
    }
    
    .btn-3d-cancel:hover {
        background: rgba(148, 163, 184, 0.15) !important;
        color: var(--modal-text-main) !important;
        transform: translateY(-1px);
    }
    
    .btn-3d-cancel:active {
        transform: translateY(2px) !important;
        border-bottom-width: 1px !important;
    }
</style>

<div x-data="{ 
    openModal: false, 
    editMode: false, 
    trabajadorId: null,
    codigo: '',
    nombre: '', 
    ci: '', 
    telefono: '', 
    rol: 'ayudante',
    rol_otro: '',
    bocamina_id: '',
    tipo_contrato_id: '',
    tipo_contrato_otro: '',
    fecha_contrato: '',
    tarifa_acordada: '',
    estado: 'activo', 
    observaciones: '',
    editActionUrl: '',
    
    isNombreValido() {
        return true; /* Capitalized automatically on blur & submit */
    },
    isTelefonoValido() {
        try {
            if (!this.telefono) return true;
            const telStr = String(this.telefono).replace(/[^0-9]/g, '');
            if (telStr === '') return true;
            return telStr.length === 8;
        } catch (e) {
            console.error('Error in isTelefonoValido:', e);
            return false;
        }
    },
    isFormValido() {
        try {
            if (!this.nombre || !this.nombre.trim()) return false;
            if (!this.isTelefonoValido()) return false;
            if (!this.bocamina_id) return false;
            if (!this.tipo_contrato_id) return false;
            if (!this.fecha_contrato) return false;
            
            if (this.tipo_contrato_id === 'otro') {
                if (!this.tipo_contrato_otro || !this.tipo_contrato_otro.trim()) {
                    return false;
                }
            }
            if (this.rol === 'otro') {
                if (!this.rol_otro || !this.rol_otro.trim()) {
                    return false;
                }
            }
            return true;
        } catch (e) {
            console.error('Error in isFormValido:', e);
            return false;
        }
    },

    openCreate() {
        this.editMode = false;
        this.trabajadorId = null;
        this.codigo = '';
        this.nombre = '';
        this.ci = '';
        this.telefono = '';
        this.rol = 'ayudante';
        this.rol_otro = '';
        this.bocamina_id = '';
        this.tipo_contrato_id = '';
        this.tipo_contrato_otro = '';
        this.fecha_contrato = new Date().toISOString().split('T')[0];
        this.tarifa_acordada = '';
        this.estado = 'activo';
        this.observaciones = '';
        this.openModal = true;
    },
    openEdit(trabajador) {
        this.editMode = true;
        this.trabajadorId = trabajador.id;
        this.codigo = trabajador.codigo || '';
        this.nombre = trabajador.nombre;
        this.ci = trabajador.ci || '';
        this.telefono = trabajador.telefono || '';
        
        // Handle standard vs custom role/cargo
        const standardRoles = ['contratista', 'chofer', 'sereno', 'ayudante', 'operador'];
        const dbRol = trabajador.rol || 'ayudante';
        if (standardRoles.includes(dbRol)) {
            this.rol = dbRol;
            this.rol_otro = '';
        } else {
            this.rol = 'otro';
            this.rol_otro = dbRol;
        }
        
        this.bocamina_id = trabajador.bocamina_id || '';
        this.tipo_contrato_id = trabajador.tipo_contrato_id || '';
        this.tipo_contrato_otro = '';
        this.fecha_contrato = trabajador.fecha_contrato ? trabajador.fecha_contrato.substring(0, 10) : '';
        this.tarifa_acordada = trabajador.tarifa_acordada || '';
        this.estado = trabajador.estado;
        this.observaciones = trabajador.observaciones || '';
        this.editActionUrl = '/trabajadores/' + trabajador.id;
        this.openModal = true;
    }
}" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100">Personal y Contratos</h1>
            <p class="text-sm text-slate-400 mt-1">Registra y administra los datos personales, contratos, tarifas y bocaminas del personal minero.</p>
        </div>
        <div class="flex flex-wrap gap-2.5">
            <a href="{{ route('reportes.index', ['tab' => 'trabajador']) }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-bold uppercase tracking-wider text-slate-200 transition-colors shadow-sm">
                <i class="fa-solid fa-file-export mr-2 text-amber-500"></i> Exportar a Reportes
            </a>
            <button @click="openCreate()" class="btn-gold px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider inline-flex items-center shadow-md">
                <i class="fa-solid fa-user-plus mr-2"></i> Nuevo Personal
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="mining-card rounded-xl p-5 no-print">
        <form action="{{ route('trabajadores.index') }}" method="GET" onsubmit="event.preventDefault(); submitFilterRealTime(this);" class="grid grid-cols-1 gap-4 sm:grid-cols-5 items-end">
            <div>
                <label for="buscar" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Buscar por Nombre o CI</label>
                <input type="text" name="buscar" id="buscar" value="{{ request('buscar') }}" 
                       oninput="clearTimeout(searchDebounceTimeout); searchDebounceTimeout = setTimeout(() => submitFilterRealTime(this.form), 250)"
                       class="mt-1 block w-full px-3.5 py-2 bg-slate-900 border border-white/10 rounded-xl text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm"
                       placeholder="Ej. Juan Pérez">
            </div>

            <div>
                <label for="bocamina_id_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Bocamina</label>
                <select name="bocamina_id" id="bocamina_id_filter" 
                        onchange="submitFilterRealTime(this.form)"
                        class="mt-1 block w-full px-3.5 py-2 bg-slate-900 border border-white/10 rounded-xl text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="">Todas las Bocaminas</option>
                    @foreach($bocaminas as $bocamina)
                        <option value="{{ $bocamina->id }}" {{ request('bocamina_id') == $bocamina->id ? 'selected' : '' }}>{{ $bocamina->nombre }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="rol_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Cargo / Función</label>
                <select name="rol" id="rol_filter" 
                        onchange="submitFilterRealTime(this.form)"
                        class="mt-1 block w-full px-3.5 py-2 bg-slate-900 border border-white/10 rounded-xl text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="">Todos los Cargos</option>
                    <option value="contratista" {{ request('rol') === 'contratista' ? 'selected' : '' }}>Contratista</option>
                    <option value="chofer" {{ request('rol') === 'chofer' ? 'selected' : '' }}>Chofer</option>
                    <option value="sereno" {{ request('rol') === 'sereno' ? 'selected' : '' }}>Sereno</option>
                    <option value="ayudante" {{ request('rol') === 'ayudante' ? 'selected' : '' }}>Ayudante</option>
                    <option value="operador" {{ request('rol') === 'operador' ? 'selected' : '' }}>Operador</option>
                    <option value="otro" {{ request('rol') === 'otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>

            <div>
                <label for="estado_filter" class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Estado</label>
                <select name="estado" id="estado_filter" 
                        onchange="submitFilterRealTime(this.form)"
                        class="mt-1 block w-full px-3.5 py-2 bg-slate-950 border border-white/10 rounded-xl text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500 focus:border-amber-500 text-sm">
                    <option value="">Todos los Estados</option>
                    <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            <div class="flex space-x-2">
                <button type="button" onclick="document.getElementById('buscar').value = ''; document.getElementById('bocamina_id_filter').value = ''; document.getElementById('rol_filter').value = ''; document.getElementById('estado_filter').value = ''; submitFilterRealTime(this.form);" class="flex-1 inline-flex items-center justify-center px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 border border-white/10 transition-colors" title="Limpiar Filtros">
                    <i class="fa-solid fa-rotate-left mr-2"></i> Limpiar
                </button>
            </div>
        </form>
    </div>    <!-- Table Section -->
    <div id="table-container" class="glass-card shimmer-card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-800">
                <thead>
                    <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900/40">
                        <th class="px-6 py-4 font-semibold w-24">Código</th>
                        <th class="px-6 py-4 font-semibold">Nombre Completo</th>
                        <th class="px-6 py-4 font-semibold w-28">C.I.</th>
                        <th class="px-6 py-4 font-semibold w-28">Teléfono</th>
                        <th class="px-6 py-4 font-semibold">Bocamina</th>
                        <th class="px-6 py-4 font-semibold w-36">Cargo</th>
                        <th class="px-6 py-4 font-semibold">Contrato</th>
                        <th class="px-6 py-4 font-semibold text-right">Tarifa / Monto</th>
                        <th class="px-6 py-4 font-semibold text-center w-28">Estado</th>
                        <th class="px-6 py-4 font-semibold no-print text-center w-32">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/40 text-sm text-slate-300">
                    @forelse($trabajadores as $trabajador)
                        <tr class="interactive-table-row">
                            <td class="px-6 py-4 font-mono text-xs font-bold text-sky-400">{{ $trabajador->codigo ?: '-' }}</td>
                            <td class="px-6 py-4 font-medium text-slate-100 text-sm">{{ $trabajador->nombre }}</td>
                            <td class="px-6 py-4 font-mono text-slate-350 text-xs">{{ $trabajador->ci ?: '-' }}</td>
                            <td class="px-6 py-4 font-mono text-slate-350 text-xs">{{ $trabajador->telefono ?: '-' }}</td>
                            <td class="px-6 py-4">
                                @if($trabajador->bocamina)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-850 text-slate-200 border border-slate-700/60 shadow-sm">
                                        <i class="fa-solid fa-mountain mr-1.5 text-sky-500 text-[10px]"></i> {{ $trabajador->bocamina->nombre }}
                                    </span>
                                @else
                                    <span class="text-slate-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold capitalize bg-sky-500/10 text-sky-400 border border-sky-500/25">
                                    {{ $trabajador->rol ?: 'ayudante' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-semibold text-slate-100 block">
                                    {{ $trabajador->tipoContrato ? $trabajador->tipoContrato->nombre : '-' }}
                                </span>
                                @if($trabajador->fecha_contrato)
                                    <span class="text-[10px] text-slate-500 font-mono font-semibold mt-0.5 block" title="Fecha del Contrato">
                                        <i class="fa-regular fa-calendar-days mr-1 text-slate-650"></i> {{ $trabajador->fecha_contrato->format('d/m/Y') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-xs font-bold text-slate-200">
                                @if($trabajador->tarifa_acordada)
                                    Bs. {{ number_format($trabajador->tarifa_acordada, 2) }}
                                    @if($trabajador->rol === 'sereno')
                                        <span class="text-[9px] text-slate-500 block font-sans">/ Mes</span>
                                    @else
                                        <span class="text-[9px] text-slate-500 block font-sans">/ Contrato</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $trabajador->estado === 'activo' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/25' : 'bg-slate-850 text-slate-450 border border-slate-700' }}">
                                    @if($trabajador->estado === 'activo')
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-emerald-400 pulse-dot"></span>
                                    @endif
                                    {{ ucfirst($trabajador->estado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 no-print text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('reportes.index', ['tab' => 'trabajador', 'trabajador_id' => $trabajador->id]) }}" 
                                       class="w-8 h-8 rounded-lg flex items-center justify-center bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border border-emerald-500/20 transition-all hover:scale-105 active:scale-95 shadow-sm"
                                       title="Ver Historial en Reportes">
                                        <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                                    </a>
                                    <button @click='openEdit(@json($trabajador))' 
                                        class="w-8 h-8 rounded-lg flex items-center justify-center bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/20 transition-all hover:scale-105 active:scale-95 shadow-sm"
                                        title="Editar Personal">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </button>
                                    <form action="{{ route('trabajadores.destroy', $trabajador->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar a este trabajador?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 transition-all hover:scale-105 active:scale-95 shadow-sm"
                                            title="Eliminar Personal">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-slate-500">
                                <i class="fa-solid fa-user-slash text-4xl mb-3 block text-slate-650"></i>
                                No se encontraron registros de personal.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- AlpineJS Modal (Create/Edit - Redesigned to be Spacious, Theme-Adaptive, Frosted, and Highly Premium) -->
    <div x-show="openModal" class="premium-backdrop" x-cloak>
        <div @click.away="openModal = false" class="premium-form-modal relative">
            <!-- Modal Header -->
            <div class="modal-header px-8 py-5 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="jewel-icon-container">
                        <i class="fa-solid fa-id-card-clip text-md"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900 dark:text-white" x-text="editMode ? 'Ficha de Personal: Editar Registro' : 'Ficha de Personal: Nuevo Registro'"></h3>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-0.5 font-bold">Gestión de Personal Minero y Contrataciones</p>
                    </div>
                </div>
                <button type="button" 
                        @click="openModal = false" 
                        class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-500 hover:text-slate-850 dark:text-slate-400 dark:hover:text-slate-200 transition-all duration-150 relative z-50 cursor-pointer pointer-events-auto"
                        title="Cerrar modal">
                    <i class="fa-solid fa-xmark text-lg pointer-events-none"></i>
                </button>
            </div>

            <!-- Modal Form -->
            <form :action="editMode ? editActionUrl : '{{ route('trabajadores.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="modal-body">
                    <!-- Section 1: Información Personal -->
                    <div class="space-y-5">
                        <h4 class="section-title section-personal">
                            <i class="fa-solid fa-user text-xs"></i> Datos Personales
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                            <div class="md:col-span-1">
                                <label for="modal_codigo">Código (Opcional)</label>
                                <input id="modal_codigo" name="codigo" type="text" x-model="codigo"
                                       class="uppercase"
                                       placeholder="Ej. CON-1024 o vacío">
                                <span class="help-text">Vacío para autogenerar.</span>
                            </div>
                            <div class="md:col-span-2">
                                <label for="modal_nombre">Nombre Completo <span class="text-red-500 font-bold">*</span></label>
                                <input id="modal_nombre" name="nombre" type="text" required x-model="nombre"
                                       @blur="nombre = (nombre || '').trim().toLowerCase().replace(/(^|[^a-záéíóúüñ\'])([a-záéíóúüñ\'])/gi, (m, separator, letter) => separator + letter.toUpperCase())"
                                       placeholder="Ej. Juan Carlos Pérez">
                                <span class="help-text">El sistema capitaliza al salir del campo.</span>
                                <div x-show="!nombre || !nombre.trim()" class="text-[11px] text-amber-500 font-medium mt-1 flex items-center gap-1.5" x-cloak>
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i> Campo obligatorio: escriba el nombre completo del trabajador.
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="modal_ci">Cédula de Identidad (C.I.) (Opcional)</label>
                                <input id="modal_ci" name="ci" type="text" x-model="ci"
                                       placeholder="Ej. 1029384-LP">
                            </div>

                            <div>
                                <label for="modal_telefono">Teléfono / Celular (Opcional)</label>
                                <input id="modal_telefono" name="telefono" type="text" x-model="telefono"
                                       @input="telefono = (telefono || '').replace(/[^0-9]/g, '')"
                                       @blur="telefono = (telefono || '').replace(/[^0-9]/g, '')"
                                       maxlength="8"
                                       placeholder="Ej. 71234567">
                                <div x-show="telefono && !isTelefonoValido()" class="error-message" x-cloak>
                                    <i class="fa-solid fa-triangle-exclamation"></i> Debe tener exactamente 8 números.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Información Laboral y Contrato -->
                    <div class="space-y-5 mt-8">
                        <h4 class="section-title section-laboral">
                            <i class="fa-solid fa-file-contract text-xs"></i> Asignación y Contrato Laboral
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-start">
                            <div>
                                <label for="modal_bocamina">Bocamina <span class="text-red-500 font-bold">*</span></label>
                                <select id="modal_bocamina" name="bocamina_id" required x-model="bocamina_id">
                                    <option value="">Seleccione una bocamina...</option>
                                    @foreach($bocaminas as $bocamina)
                                        <option value="{{ $bocamina->id }}">{{ $bocamina->nombre }}</option>
                                    @endforeach
                                </select>
                                <div x-show="!bocamina_id" class="text-[11px] text-amber-500 font-medium mt-1 flex items-center gap-1.5" x-cloak>
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i> Campo obligatorio: seleccione la bocamina de asignación.
                                </div>
                            </div>

                            <div>
                                <label for="modal_rol">Cargo / Función <span class="text-red-500 font-bold">*</span></label>
                                <select id="modal_rol" name="rol" required x-model="rol">
                                    <option value="contratista">Contratista</option>
                                    <option value="chofer">Chofer</option>
                                    <option value="sereno">Sereno</option>
                                    <option value="ayudante">Ayudante</option>
                                    <option value="operador">Operador</option>
                                    <option value="otro">Otro</option>
                                </select>

                                <!-- Custom Text input directly below Cargo dropdown in the same column grid -->
                                <div x-show="rol === 'otro'" x-cloak class="mt-3 transition duration-150">
                                    <input id="modal_rol_otro" name="rol_otro" type="text" x-model="rol_otro"
                                           :required="rol === 'otro'"
                                           class="!border-indigo-500/50 focus:!border-indigo-500 focus:!ring-2 focus:!ring-indigo-500/20 font-bold"
                                           placeholder="Escriba cargo personalizado aquí...">
                                    <div x-show="rol === 'otro' && (!rol_otro || !rol_otro.trim())" class="text-[11px] text-amber-500 font-medium mt-1 flex items-center gap-1.5" x-cloak>
                                        <i class="fa-solid fa-circle-exclamation text-xs"></i> Especifique el cargo o función personalizado.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-start">
                            <!-- Tipo de Contrato -->
                            <div>
                                <label for="modal_tipo_contrato">Tipo de Contrato <span class="text-red-500 font-bold">*</span></label>
                                <select id="modal_tipo_contrato" name="tipo_contrato_id" required x-model="tipo_contrato_id">
                                    <option value="">Seleccione un tipo...</option>
                                    @foreach($contratos as $contrato)
                                        <option value="{{ $contrato->id }}">{{ $contrato->nombre }}</option>
                                    @endforeach
                                    <option value="otro">Otro (Escribir personalizado)</option>
                                </select>
                                <div x-show="!tipo_contrato_id" class="text-[11px] text-amber-500 font-medium mt-1 flex items-center gap-1.5" x-cloak>
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i> Campo obligatorio: seleccione el tipo de contrato.
                                </div>

                                <!-- Custom Text input directly below dropdown in the same column grid -->
                                <div x-show="tipo_contrato_id === 'otro'" x-cloak class="mt-3 transition duration-150">
                                    <input id="modal_tipo_contrato_otro" name="tipo_contrato_otro" type="text" x-model="tipo_contrato_otro"
                                           :required="tipo_contrato_id === 'otro'"
                                           class="!border-indigo-500/50 focus:!border-indigo-500 focus:!ring-2 focus:!ring-indigo-500/20 font-bold"
                                           placeholder="Escriba tipo de contrato aquí...">
                                    <div x-show="tipo_contrato_id === 'otro' && (!tipo_contrato_otro || !tipo_contrato_otro.trim())" class="text-[11px] text-amber-500 font-medium mt-1 flex items-center gap-1.5" x-cloak>
                                        <i class="fa-solid fa-circle-exclamation text-xs"></i> Especifique el nombre del contrato personalizado.
                                    </div>
                                </div>
                            </div>

                            <!-- Fecha del Contrato -->
                            <div>
                                <label for="modal_fecha_contrato">Fecha del Contrato <span class="text-red-500 font-bold">*</span></label>
                                <input id="modal_fecha_contrato" name="fecha_contrato" type="date" required x-model="fecha_contrato">
                                <div x-show="!fecha_contrato" class="text-[11px] text-amber-500 font-medium mt-1 flex items-center gap-1.5" x-cloak>
                                    <i class="fa-solid fa-circle-exclamation text-xs"></i> Campo obligatorio: indique la fecha de inicio del contrato.
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-start">
                            <!-- Tarifa Acordada / Cantidad de Dinero -->
                            <div>
                                <label for="modal_tarifa">Monto / Tarifa Acordada (Bs.)</label>
                                <div class="premium-input-group">
                                    <span class="prefix-badge">
                                        Bs.
                                    </span>
                                    <input id="modal_tarifa" name="tarifa_acordada" type="number" step="0.01" min="0" x-model="tarifa_acordada"
                                           placeholder="0.00">
                                </div>
                                
                                <!-- Dinero / Tarifa Info Banner based on Role -->
                                <div class="mt-2.5 transition-all duration-200">
                                    <div x-show="rol === 'sereno'" class="text-xs font-semibold text-sky-600 dark:text-sky-400 bg-sky-500/10 border border-sky-500/20 px-3.5 py-2.5 rounded-xl flex items-center gap-2" x-cloak>
                                        <i class="fa-solid fa-circle-info text-sm text-sky-500"></i>
                                        <span>Para el cargo de <strong>Sereno</strong>, la tarifa se calcula y paga por mes (Mensual).</span>
                                    </div>
                                    <div x-show="rol === 'contratista'" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-3.5 py-2.5 rounded-xl flex items-center gap-2" x-cloak>
                                        <i class="fa-solid fa-circle-info text-sm text-emerald-500"></i>
                                        <span>Para <strong>Contratista</strong>, el pago se calcula según avance y volumen de contrato.</span>
                                    </div>
                                    <div x-show="rol !== 'sereno' && rol !== 'contratista'" class="text-xs font-semibold text-slate-500 dark:text-slate-400 bg-slate-500/5 border border-slate-700/20 px-3.5 py-2.5 rounded-xl flex items-center gap-2" x-cloak>
                                        <i class="fa-solid fa-circle-info text-sm text-slate-400"></i>
                                        <span>La tarifa se aplicará de acuerdo a la asistencia diaria o producción regular.</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Estado -->
                            <div>
                                <label for="modal_estado">Estado</label>
                                <select id="modal_estado" name="estado" required x-model="estado">
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="modal_observaciones">Observaciones</label>
                            <textarea id="modal_observaciones" name="observaciones" rows="3" x-model="observaciones"
                                      placeholder="Escriba anotaciones o comentarios adicionales..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer flex items-center justify-between">
                    <div x-show="!isFormValido()" class="text-[11px] text-amber-500 font-semibold flex items-center gap-1.5" x-cloak>
                        <i class="fa-solid fa-circle-info text-amber-400"></i>
                        <span>Complete los campos requeridos (*) para habilitar el guardado.</span>
                    </div>
                    <div class="flex items-center space-x-3 ml-auto">
                        <button type="button" @click="openModal = false" class="btn-3d-cancel px-5 py-3 text-xs font-bold uppercase tracking-wider rounded-xl transition-all duration-150 border border-slate-200 dark:border-slate-800">
                            Cancelar
                        </button>
                        <button type="submit" 
                                :disabled="!isFormValido()"
                                class="btn-3d-save inline-flex items-center justify-center px-6 py-3 rounded-xl text-xs font-extrabold uppercase tracking-wider text-white transition duration-150">
                            Guardar Personal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let searchDebounceTimeout;
    
    function submitFilterRealTime(form) {
        const url = new URL(form.action || window.location.href);
        const formData = new FormData(form);
        
        for (const [key, value] of formData.entries()) {
            if (value !== '') {
                url.searchParams.set(key, value);
            } else {
                url.searchParams.delete(key);
            }
        }

        window.history.pushState({}, '', url.toString());

        const container = document.getElementById('table-container');
        if (!container) {
            form.submit();
            return;
        }

        container.style.opacity = '0.5';
        container.style.transition = 'opacity 0.15s ease';

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContainer = doc.getElementById('table-container');
            if (newContainer) {
                container.innerHTML = newContainer.innerHTML;
            } else {
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error filtering real-time:', error);
            form.submit();
        })
        .finally(() => {
            container.style.opacity = '1';
        });
    }
</script>
@endpush
