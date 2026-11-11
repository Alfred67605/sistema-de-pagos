<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950 text-slate-100">
<head>
    <script>
        // Inline script to prevent theme flash
        const currentTheme = localStorage.getItem('theme') || 'dark';
        if (currentTheme === 'light') {
            document.documentElement.classList.add('light-theme');
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Gestión Minera') - KOYOSITA S.R.L.</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/logo_koyosita.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="/app.ico">
    <link rel="manifest" href="/manifest.webmanifest">
    
    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Chart.js & AlpineJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SheetJS (XLSX) & html2pdf -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        /* === SISTEMA DE DISEÑO PREMIUM MINERO (DARK GRAPHITE & GOLD AMBER) === */
        :root {
            --bg-canvas: #090d16;
            --bg-surface-1: #0f172a;
            --bg-surface-2: #131d31;
            --bg-surface-3: #18243c;
            --bg-input: #0b1120;
            --border-subtle: rgba(255, 255, 255, 0.07);
            --border-medium: rgba(255, 255, 255, 0.12);
            --border-highlight: rgba(245, 158, 11, 0.35);
            --accent-gold: #f59e0b;
            --accent-gold-hover: #fbbf24;
            --accent-gold-glow: rgba(245, 158, 11, 0.15);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-tertiary: #64748b;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.4), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
            --card-shadow-hover: 0 20px 30px -10px rgba(0, 0, 0, 0.5), 0 0 20px rgba(245, 158, 11, 0.12);
        }

        .light-theme {
            --bg-canvas: #f1f5f9;
            --bg-surface-1: #ffffff;
            --bg-surface-2: #f8fafc;
            --bg-surface-3: #e2e8f0;
            --bg-input: #ffffff;
            --border-subtle: rgba(15, 23, 42, 0.08);
            --border-medium: rgba(15, 23, 42, 0.14);
            --border-highlight: rgba(217, 119, 6, 0.45);
            --accent-gold: #d97706;
            --accent-gold-hover: #b45309;
            --accent-gold-glow: rgba(217, 119, 6, 0.12);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-tertiary: #64748b;
            --card-shadow: 0 4px 12px -2px rgba(15, 23, 42, 0.06), 0 2px 4px -1px rgba(15, 23, 42, 0.04);
            --card-shadow-hover: 0 12px 24px -4px rgba(15, 23, 42, 0.1), 0 0 16px rgba(217, 119, 6, 0.1);
        }

        [x-cloak] { display: none !important; }

        html, body {
            background-color: var(--bg-canvas) !important;
            color: var(--text-primary) !important;
            font-family: 'Outfit', 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            transition: background-color 0.25s ease, color 0.25s ease;
        }

        /* Sleek scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: var(--bg-surface-1);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(245, 158, 11, 0.4);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent-gold);
        }

        /* Surface & Card classes */
        .mining-card, .glass-card {
            background: var(--bg-surface-2) !important;
            border: 1px solid var(--border-subtle) !important;
            box-shadow: var(--card-shadow) !important;
            border-radius: 1.15rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.25s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease;
        }
        .mining-card:hover, .glass-card:hover {
            border-color: var(--border-medium) !important;
        }
        .mining-card.interactive:hover, .glass-card.interactive:hover {
            transform: translateY(-3px);
            box-shadow: var(--card-shadow-hover) !important;
            border-color: var(--border-highlight) !important;
        }

        /* Card Shimmer Sweep on Hover */
        @keyframes shimmerSweep {
            0% { transform: translateX(-150%) skewX(-20deg); }
            100% { transform: translateX(250%) skewX(-20deg); }
        }
        .shimmer-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 45%;
            height: 100%;
            background: linear-gradient(to right, transparent, rgba(255, 255, 255, 0.06), transparent);
            transform: translateX(-150%) skewX(-20deg);
            pointer-events: none;
            z-index: 5;
        }
        .shimmer-card:hover::after {
            animation: shimmerSweep 1.1s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Distinctive Mining Jewel Tone Accents */
        .accent-border-gold    { border-top: 3px solid #f59e0b !important; }
        .accent-border-copper  { border-top: 3px solid #f97316 !important; }
        .accent-border-emerald { border-top: 3px solid #10b981 !important; }
        .accent-border-cyan    { border-top: 3px solid #06b6d4 !important; }
        .accent-border-purple  { border-top: 3px solid #a855f7 !important; }
        .accent-border-rose    { border-top: 3px solid #f43f5e !important; }

        .glow-gold:hover    { box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.6), 0 0 25px rgba(245, 158, 11, 0.22) !important; }
        .glow-copper:hover  { box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.6), 0 0 25px rgba(249, 115, 22, 0.22) !important; }
        .glow-emerald:hover { box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.6), 0 0 25px rgba(16, 185, 129, 0.22) !important; }
        .glow-cyan:hover    { box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.6), 0 0 25px rgba(6, 182, 212, 0.22) !important; }
        .glow-purple:hover  { box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.6), 0 0 25px rgba(168, 85, 247, 0.22) !important; }
        .glow-rose:hover    { box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.6), 0 0 25px rgba(244, 63, 94, 0.22) !important; }

        /* Interactive Table Row Highlight with smooth slide & jewel accent */
        .interactive-table-row {
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
            border-left: 3.5px solid transparent !important;
        }
        .interactive-table-row:hover {
            background-color: rgba(245, 158, 11, 0.06) !important;
            border-left-color: #f59e0b !important;
            transform: translateX(4px);
        }

        /* Pulsing Status Dot */
        @keyframes pulseGlow {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.2); filter: drop-shadow(0 0 6px currentColor); }
        }
        .pulse-dot {
            animation: pulseGlow 2.2s infinite ease-in-out;
        }

        /* Fluid Page Load Entrance Animation */
        @keyframes pageEntrance {
            0% { opacity: 0; transform: translateY(8px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .page-fade-in {
            animation: pageEntrance 0.32s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Animated Gradient Progress Bar */
        @keyframes progressFlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .animated-progress-bar {
            background-size: 200% 200%;
            animation: progressFlow 3.5s ease infinite;
        }

        /* Top subtle gold ambient accent */
        .gold-accent-line::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2.5px;
            background: linear-gradient(90deg, transparent 0%, #f59e0b 50%, transparent 100%);
            opacity: 0.85;
        }

        /* Rich Colorful Buttons with Micro-interactions */
        .btn-gold {
            background: linear-gradient(135deg, #f59e0b 0%, #ea580c 100%) !important;
            color: #090d16 !important;
            box-shadow: 0 4px 16px rgba(245, 158, 11, 0.3) !important;
            font-weight: 800 !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .btn-gold:hover {
            background: linear-gradient(135deg, #fbbf24 0%, #f97316 100%) !important;
            box-shadow: 0 6px 22px rgba(245, 158, 11, 0.45) !important;
            transform: translateY(-2px);
        }
        .btn-gold:active {
            transform: translateY(1px) scale(0.97);
        }

        .btn-emerald {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.3) !important;
            font-weight: 700 !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .btn-emerald:hover {
            box-shadow: 0 6px 22px rgba(16, 185, 129, 0.45) !important;
            transform: translateY(-2px);
        }
        .btn-emerald:active {
            transform: translateY(1px) scale(0.97);
        }

        .btn-cyan {
            background: linear-gradient(135deg, #06b6d4 0%, #0284c7 100%) !important;
            color: #ffffff !important;
            box-shadow: 0 4px 16px rgba(6, 182, 212, 0.3) !important;
            font-weight: 700 !important;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
        }
        .btn-cyan:hover {
            box-shadow: 0 6px 22px rgba(6, 182, 212, 0.45) !important;
            transform: translateY(-2px);
        }
        .btn-cyan:active {
            transform: translateY(1px) scale(0.97);
        }

        /* Navigation item styles */
        .sidebar-nav-item {
            display: flex;
            align-items: center;
            padding: 0.55rem 0.75rem;
            border-radius: 0.65rem;
            font-size: 0.825rem;
            font-weight: 500;
            color: var(--text-secondary);
            border: 1px solid transparent;
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }
        .sidebar-nav-item:hover {
            color: var(--text-primary);
            background: var(--bg-surface-3);
            border-color: var(--border-subtle);
        }
        .sidebar-nav-item.active-item {
            color: #fbbf24 !important;
            background: rgba(245, 158, 11, 0.1) !important;
            border-color: rgba(245, 158, 11, 0.35) !important;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(245, 158, 11, 0.1);
        }
        .sidebar-nav-item.active-item i {
            color: #fbbf24 !important;
        }
        .light-theme .sidebar-nav-item.active-item {
            color: #b45309 !important;
            background: rgba(217, 119, 6, 0.12) !important;
            border-color: rgba(217, 119, 6, 0.35) !important;
        }
        .light-theme .sidebar-nav-item.active-item i {
            color: #b45309 !important;
        }

        /* Tablas mineras refinadas */
        table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        thead th {
            background-color: var(--bg-surface-3) !important;
            color: var(--text-secondary) !important;
            font-size: 0.7rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            border-bottom: 1px solid var(--border-subtle) !important;
            padding: 0.75rem 1rem !important;
            white-space: nowrap;
        }
        tbody td {
            color: var(--text-primary) !important;
            border-bottom: 1px solid var(--border-subtle) !important;
            padding: 0.75rem 1rem !important;
            font-size: 0.8125rem;
            vertical-align: middle;
        }
        tbody tr {
            transition: background-color 0.15s ease;
        }
        tbody tr:hover {
            background-color: var(--bg-surface-3) !important;
        }

        /* Inputs y formularios */
        input:not([type="checkbox"]):not([type="radio"]), select, textarea {
            background-color: var(--bg-input) !important;
            border: 1px solid var(--border-medium) !important;
            color: var(--text-primary) !important;
            border-radius: 0.6rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        input:focus, select:focus, textarea:focus {
            outline: none !important;
            border-color: var(--accent-gold) !important;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2) !important;
        }

        /* Autofill overrides */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus {
            -webkit-text-fill-color: var(--text-primary) !important;
            -webkit-box-shadow: 0 0 0px 1000px var(--bg-surface-1) inset !important;
            transition: background-color 5000s ease-in-out 0s !important;
        }

        /* Botones primarios dorados */
        .btn-gold {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: #0b0f19;
            font-weight: 700;
            border: 1px solid rgba(245, 158, 11, 0.4);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .btn-gold:hover {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            box-shadow: 0 6px 18px rgba(245, 158, 11, 0.35);
            transform: translateY(-1px);
        }
        .btn-gold:active {
            transform: translateY(0);
        }

        /* Panoramic Mining Hero Banner */
        .mining-hero-container {
            background-color: var(--bg-surface-1);
            min-height: 180px;
        }
        .mining-hero-img {
            animation: slowMiningPan 35s ease-in-out infinite alternate;
            transform-origin: center;
        }
        @keyframes slowMiningPan {
            0% { transform: scale(1.0) translate(0, 0); }
            100% { transform: scale(1.05) translate(-1%, -0.5%); }
        }
        .ambient-mining-lantern {
            position: absolute;
            right: 8%;
            top: 25%;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.22) 0%, rgba(217, 119, 6, 0.08) 45%, transparent 70%);
            border-radius: 50%;
            filter: blur(40px);
            animation: lanternPulse 7s ease-in-out infinite alternate;
        }
        @keyframes lanternPulse {
            0% { opacity: 0.35; transform: scale(0.9); }
            100% { opacity: 0.85; transform: scale(1.1); }
        }

        /* Subtle Mineral Rock Texture Accent */
        .mineral-accent-bg {
            background-image: url('{{ asset('images/mining/mineral_texture.jpg') }}');
            background-size: 320px;
        }

        /* Accesibilidad: Respeto a prefers-reduced-motion */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
            .mining-hero-img, .ambient-mining-lantern {
                animation: none !important;
                transform: none !important;
            }
        }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-slate-950" 
      x-data="{ 
          sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true',
          mobileMenuOpen: false,
          toggleSidebar() {
              this.sidebarCollapsed = !this.sidebarCollapsed;
              localStorage.setItem('sidebar_collapsed', this.sidebarCollapsed);
          }
      }">

    <!-- === DESKTOP SIDEBAR === -->
    <aside class="no-print hidden md:flex md:flex-col fixed inset-y-0 z-30 transition-all duration-200 ease-in-out border-r border-white/5"
           :class="sidebarCollapsed ? 'w-20' : 'w-64'"
           style="background-color: var(--bg-surface-1);">
        
        <div class="flex flex-col h-full">
            
            <!-- Brand / Logo Header -->
            <div class="flex items-center justify-between px-4 h-16 border-b border-white/5 flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 overflow-hidden group">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl overflow-hidden shadow-md flex-shrink-0 border border-amber-500/40 group-hover:border-amber-400 transition-colors">
                        <img src="{{ asset('images/logo_koyosita.png') }}" alt="KOYOSITA S.R.L." class="w-full h-full object-cover">
                    </div>
                    <div x-show="!sidebarCollapsed" x-transition.opacity.duration.150ms class="min-w-0">
                        <div class="flex items-center space-x-1">
                            <span class="text-xs font-black tracking-widest text-slate-100 uppercase">KOYOSITA</span>
                            <span class="text-xs font-black tracking-widest text-amber-500 uppercase">S.R.L.</span>
                        </div>
                        <span class="text-[9px] font-semibold text-slate-400 font-mono tracking-wider block">EMPRESA MINERA</span>
                    </div>
                </a>

                <!-- Collapse / Expand toggle button -->
                <button @click="toggleSidebar()" 
                        class="p-1.5 rounded-lg text-slate-400 hover:text-amber-400 hover:bg-white/5 transition-colors focus:outline-none"
                        :title="sidebarCollapsed ? 'Expandir barra lateral' : 'Plegar barra lateral'">
                    <i class="fa-solid text-xs transition-transform duration-200" :class="sidebarCollapsed ? 'fa-angles-right' : 'fa-angles-left'"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-4 space-y-4 overflow-y-auto"
                 x-data="{
                     openPersonal: localStorage.getItem('nav_open_personal') !== 'false',
                     openMovimientos: localStorage.getItem('nav_open_movimientos') !== 'false',
                     openAlmacen: localStorage.getItem('nav_open_almacen') !== 'false',
                     openSistema: localStorage.getItem('nav_open_sistema') !== 'false',
                     toggleNav(key) {
                         this[key] = !this[key];
                         localStorage.setItem('nav_' + key.replace('open', '').toLowerCase(), this[key]);
                     }
                 }"
                 x-init="
                     if ({{ request()->routeIs('bocaminas.*', 'trabajadores.*', 'tipos-trabajo.*') ? 'true' : 'false' }}) { openPersonal = true; }
                     if ({{ request()->routeIs('fondos-caja.*', 'anticipos.*', 'pagos.*', 'servicios-externos.*', 'reportes.*') ? 'true' : 'false' }}) { openMovimientos = true; }
                     if ({{ request()->routeIs('transacciones-minerales.*', 'reportes-comercializacion.*') ? 'true' : 'false' }}) { openAlmacen = true; }
                     if ({{ request()->routeIs('backups.*') ? 'true' : 'false' }}) { openSistema = true; }
                 ">

                <!-- 1. TABLERO PRINCIPAL -->
                <div>
                    <a href="{{ route('dashboard') }}" 
                       class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active-item' : '' }}"
                       :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                       title="Tablero Principal">
                        <span class="w-6 h-6 rounded-md flex items-center justify-center bg-amber-500/15 text-amber-400 border border-amber-500/25 flex-shrink-0 transition-transform group-hover:scale-105" :class="sidebarCollapsed ? '' : 'mr-3'">
                            <i class="fa-solid fa-chart-pie text-xs"></i>
                        </span>
                        <span x-show="!sidebarCollapsed" x-transition.opacity.duration.150ms class="truncate font-semibold">Tablero Principal</span>
                    </a>
                </div>

                <!-- 2. PERSONAL Y FRENTES -->
                <div>
                    <div x-show="!sidebarCollapsed" class="px-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 font-mono">
                        Personal y Mina
                    </div>
                    <button @click="toggleNav('openPersonal')" 
                            class="w-full flex items-center justify-between px-3 py-1.5 text-xs font-semibold text-slate-400 hover:text-slate-200 rounded-lg hover:bg-white/5 transition-colors focus:outline-none"
                            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                            title="Personal y Frentes">
                        <span class="flex items-center">
                            <span class="w-6 h-6 rounded-md flex items-center justify-center bg-sky-500/15 text-sky-400 border border-sky-500/25 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'">
                                <i class="fa-solid fa-people-carry-box text-xs"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Personal y Mina</span>
                        </span>
                        <i x-show="!sidebarCollapsed" class="fa-solid fa-chevron-down text-[8px] text-slate-500 transition-transform duration-200" :class="openPersonal ? '' : '-rotate-90'"></i>
                    </button>

                    <div x-show="openPersonal || sidebarCollapsed" class="mt-1 space-y-1" :class="sidebarCollapsed ? '' : 'pl-3 border-l border-white/5 ml-4'">
                        <a href="{{ route('bocaminas.index') }}" 
                           class="sidebar-nav-item {{ request()->routeIs('bocaminas.*') ? 'active-item' : '' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                           title="Bocaminas">
                            <span class="w-5 h-5 rounded flex items-center justify-center bg-orange-500/15 text-orange-400 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-2.5'">
                                <i class="fa-solid fa-mountain text-[10px]"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Bocaminas</span>
                        </a>
                        <a href="{{ route('trabajadores.index') }}" 
                           class="sidebar-nav-item {{ request()->routeIs('trabajadores.*') ? 'active-item' : '' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                           title="Personal y Contratos">
                            <span class="w-5 h-5 rounded flex items-center justify-center bg-sky-500/15 text-sky-400 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-2.5'">
                                <i class="fa-solid fa-id-card-clip text-[10px]"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Personal y Contratos</span>
                        </a>
                        <a href="{{ route('tipos-trabajo.index') }}" 
                           class="sidebar-nav-item {{ request()->routeIs('tipos-trabajo.*') ? 'active-item' : '' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                           title="Tipos de Trabajo">
                            <span class="w-5 h-5 rounded flex items-center justify-center bg-amber-500/15 text-amber-400 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-2.5'">
                                <i class="fa-solid fa-hammer text-[10px]"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Tipos de Trabajo</span>
                        </a>
                    </div>
                </div>

                <!-- 3. CAJA Y LIQUIDACIONES -->
                <div>
                    <div x-show="!sidebarCollapsed" class="px-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 font-mono">
                        Caja y Movimientos
                    </div>
                    <button @click="toggleNav('openMovimientos')" 
                            class="w-full flex items-center justify-between px-3 py-1.5 text-xs font-semibold text-slate-400 hover:text-slate-200 rounded-lg hover:bg-white/5 transition-colors focus:outline-none"
                            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                            title="Caja y Movimientos">
                        <span class="flex items-center">
                            <span class="w-6 h-6 rounded-md flex items-center justify-center bg-emerald-500/15 text-emerald-400 border border-emerald-500/25 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'">
                                <i class="fa-solid fa-vault text-xs"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Caja y Movimientos</span>
                        </span>
                        <i x-show="!sidebarCollapsed" class="fa-solid fa-chevron-down text-[8px] text-slate-500 transition-transform duration-200" :class="openMovimientos ? '' : '-rotate-90'"></i>
                    </button>

                    <div x-show="openMovimientos || sidebarCollapsed" class="mt-1 space-y-1" :class="sidebarCollapsed ? '' : 'pl-3 border-l border-white/5 ml-4'">
                        <a href="{{ route('fondos-caja.index') }}" 
                           class="sidebar-nav-item {{ request()->routeIs('fondos-caja.*') ? 'active-item' : '' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                           title="Caja del Personal">
                            <span class="w-5 h-5 rounded flex items-center justify-center bg-emerald-500/15 text-emerald-400 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-2.5'">
                                <i class="fa-solid fa-cash-register text-[10px]"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Caja del Personal</span>
                        </a>
                        <a href="{{ route('anticipos.index') }}" 
                           class="sidebar-nav-item {{ request()->routeIs('anticipos.*') ? 'active-item' : '' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                           title="Anticipos">
                            <span class="w-5 h-5 rounded flex items-center justify-center bg-rose-500/15 text-rose-400 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-2.5'">
                                <i class="fa-solid fa-hand-holding-dollar text-[10px]"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Anticipos</span>
                        </a>
                        <a href="{{ route('pagos.index') }}" 
                           class="sidebar-nav-item {{ request()->routeIs('pagos.*') ? 'active-item' : '' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                           title="Pagos y Planillas">
                            <span class="w-5 h-5 rounded flex items-center justify-center bg-teal-500/15 text-teal-400 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-2.5'">
                                <i class="fa-solid fa-file-invoice-dollar text-[10px]"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Pagos y Planillas</span>
                        </a>
                        <a href="{{ route('servicios-externos.index') }}" 
                           class="sidebar-nav-item {{ request()->routeIs('servicios-externos.*') ? 'active-item' : '' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                           title="Servicios Externos">
                            <span class="w-5 h-5 rounded flex items-center justify-center bg-indigo-500/15 text-indigo-400 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-2.5'">
                                <i class="fa-solid fa-truck-ramp-box text-[10px]"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Servicios Externos</span>
                        </a>
                        <a href="{{ route('reportes.index') }}" 
                           class="sidebar-nav-item {{ request()->routeIs('reportes.index') ? 'active-item' : '' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                           title="Reportes Consolidados">
                            <span class="w-5 h-5 rounded flex items-center justify-center bg-blue-500/15 text-blue-400 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-2.5'">
                                <i class="fa-solid fa-chart-column text-[10px]"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Reportes Personal</span>
                        </a>
                    </div>
                </div>

                <!-- 4. COMERCIALIZACIÓN Y MINERALES (MÓDULO 2) -->
                <div>
                    <div x-show="!sidebarCollapsed" class="px-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 font-mono">
                        Minerales y Lotes
                    </div>
                    <button @click="toggleNav('openAlmacen')" 
                            class="w-full flex items-center justify-between px-3 py-1.5 text-xs font-semibold text-slate-400 hover:text-slate-200 rounded-lg hover:bg-white/5 transition-colors focus:outline-none"
                            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                            title="Minerales y Lotes">
                        <span class="flex items-center">
                            <span class="w-6 h-6 rounded-md flex items-center justify-center bg-purple-500/15 text-purple-400 border border-purple-500/25 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'">
                                <i class="fa-solid fa-dolly text-xs"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Minerales y Lotes</span>
                        </span>
                        <i x-show="!sidebarCollapsed" class="fa-solid fa-chevron-down text-[8px] text-slate-500 transition-transform duration-200" :class="openAlmacen ? '' : '-rotate-90'"></i>
                    </button>

                    <div x-show="openAlmacen || sidebarCollapsed" class="mt-1 space-y-1" :class="sidebarCollapsed ? '' : 'pl-3 border-l border-white/5 ml-4'">
                        <a href="{{ route('transacciones-minerales.index', ['tab' => 'caja']) }}" 
                           class="sidebar-nav-item {{ (request()->routeIs('transacciones-minerales.*') && request('tab') === 'caja') ? 'active-item' : '' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                           title="Caja del Módulo">
                            <span class="w-5 h-5 rounded flex items-center justify-center bg-emerald-500/15 text-emerald-400 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-2.5'">
                                <i class="fa-solid fa-scale-balanced text-[10px]"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Caja del Módulo</span>
                        </a>
                        <a href="{{ route('transacciones-minerales.index', ['tab' => 'compras']) }}" 
                           class="sidebar-nav-item {{ (request()->routeIs('transacciones-minerales.*') && request('tab', 'compras') === 'compras') ? 'active-item' : '' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                           title="Compras de Mineral">
                            <span class="w-5 h-5 rounded flex items-center justify-center bg-purple-500/15 text-purple-400 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-2.5'">
                                <i class="fa-solid fa-arrow-down-to-bracket text-[10px]"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Compras de Lotes</span>
                        </a>
                        <a href="{{ route('transacciones-minerales.index', ['tab' => 'ventas']) }}" 
                           class="sidebar-nav-item {{ (request()->routeIs('transacciones-minerales.*') && request('tab') === 'ventas') ? 'active-item' : '' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                           title="Ventas de Mineral">
                            <span class="w-5 h-5 rounded flex items-center justify-center bg-cyan-500/15 text-cyan-400 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-2.5'">
                                <i class="fa-solid fa-arrow-up-from-bracket text-[10px]"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Ventas de Mineral</span>
                        </a>
                        <a href="{{ route('transacciones-minerales.index', ['tab' => 'stock']) }}" 
                           class="sidebar-nav-item {{ (request()->routeIs('transacciones-minerales.*') && request('tab') === 'stock') ? 'active-item' : '' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                           title="Stock de Mineral">
                            <span class="w-5 h-5 rounded flex items-center justify-center bg-amber-500/15 text-amber-400 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-2.5'">
                                <i class="fa-solid fa-boxes-stacked text-[10px]"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Stock de Mineral</span>
                        </a>
                        <a href="{{ route('reportes-comercializacion.index') }}" 
                           class="sidebar-nav-item {{ request()->routeIs('reportes-comercializacion.*') ? 'active-item' : '' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                           title="Reportes Comercialización">
                            <span class="w-5 h-5 rounded flex items-center justify-center bg-fuchsia-500/15 text-fuchsia-400 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-2.5'">
                                <i class="fa-solid fa-chart-line text-[10px]"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Reportes Comercializ.</span>
                        </a>
                    </div>
                </div>

                <!-- 5. SISTEMA Y RESPALDOS -->
                <div>
                    <div x-show="!sidebarCollapsed" class="px-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 font-mono">
                        Sistema
                    </div>
                    <button @click="toggleNav('openSistema')" 
                            class="w-full flex items-center justify-between px-3 py-1.5 text-xs font-semibold text-slate-400 hover:text-slate-200 rounded-lg hover:bg-white/5 transition-colors focus:outline-none"
                            :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                            title="Sistema">
                        <span class="flex items-center">
                            <span class="w-6 h-6 rounded-md flex items-center justify-center bg-slate-500/15 text-slate-300 border border-slate-500/25 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-3'">
                                <i class="fa-solid fa-gear text-xs"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Sistema</span>
                        </span>
                        <i x-show="!sidebarCollapsed" class="fa-solid fa-chevron-down text-[8px] text-slate-500 transition-transform duration-200" :class="openSistema ? '' : '-rotate-90'"></i>
                    </button>

                    <div x-show="openSistema || sidebarCollapsed" class="mt-1 space-y-1" :class="sidebarCollapsed ? '' : 'pl-3 border-l border-white/5 ml-4'">
                        <a href="{{ route('backups.index') }}" 
                           class="sidebar-nav-item {{ request()->routeIs('backups.*') ? 'active-item' : '' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                           title="Respaldos">
                            <span class="w-5 h-5 rounded flex items-center justify-center bg-blue-500/15 text-blue-400 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-2.5'">
                                <i class="fa-solid fa-database text-[10px]"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Copias de Seguridad</span>
                        </a>
                        <a href="{{ route('profile.edit') }}" 
                           class="sidebar-nav-item {{ request()->routeIs('profile.*') ? 'active-item' : '' }}"
                           :class="sidebarCollapsed ? 'justify-center px-0' : ''"
                           title="Mi Perfil">
                            <span class="w-5 h-5 rounded flex items-center justify-center bg-indigo-500/15 text-indigo-400 flex-shrink-0" :class="sidebarCollapsed ? '' : 'mr-2.5'">
                                <i class="fa-solid fa-user-shield text-[10px]"></i>
                            </span>
                            <span x-show="!sidebarCollapsed" class="truncate">Configuración / Perfil</span>
                        </a>
                    </div>
                </div>

            </nav>

            <!-- Bottom User Section -->
            <div class="p-3 border-t border-white/5 mt-auto flex-shrink-0" style="background-color: var(--bg-surface-2);">
                <div class="flex items-center justify-between" :class="sidebarCollapsed ? 'flex-col space-y-2' : ''">
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center space-x-2.5 min-w-0 group p-1 rounded-lg hover:bg-white/5 transition-colors"
                       title="Ver Perfil de Usuario">
                        <div class="relative w-8 h-8 rounded-lg bg-amber-500/15 border border-amber-500/30 flex items-center justify-center flex-shrink-0 text-amber-500 font-bold text-xs shadow-sm">
                            {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                            <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-slate-900"></span>
                        </div>
                        <div x-show="!sidebarCollapsed" x-transition.opacity.duration.150ms class="min-w-0">
                            <p class="text-xs font-semibold text-slate-100 truncate group-hover:text-amber-400 transition-colors">
                                {{ Auth::user()->name ?? 'Administrador' }}
                            </p>
                            <p class="text-[9px] font-mono text-slate-400 uppercase tracking-wider">
                                ADMIN MINERO
                            </p>
                        </div>
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="flex-shrink-0">
                        @csrf
                        <button type="submit" 
                                class="p-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 border border-transparent hover:border-rose-500/20 transition-all focus:outline-none"
                                title="Cerrar Sesión">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </aside>

    <!-- === MAIN APPLICATION WRAPPER === -->
    <div class="flex flex-col flex-1 w-full overflow-hidden transition-all duration-200 ease-in-out"
         :class="sidebarCollapsed ? 'md:pl-20' : 'md:pl-64'">
        
        <!-- === TOPBAR === -->
        <header class="no-print h-16 border-b border-white/5 px-4 md:px-8 flex items-center justify-between flex-shrink-0 relative z-20"
                style="background-color: var(--bg-surface-1);">
            
            <!-- Left Side: Mobile Menu Button & Context Breadcrumb -->
            <div class="flex items-center space-x-3">
                <button @click="mobileMenuOpen = true" 
                        class="md:hidden p-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/5 focus:outline-none">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>

                <!-- Breadcrumbs -->
                <div class="flex items-center space-x-2 text-xs">
                    <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-amber-400 transition-colors flex items-center">
                        <i class="fa-solid fa-house-chimney text-[11px] mr-1.5 text-amber-500/80"></i>
                        <span>SCPM</span>
                    </a>
                    <i class="fa-solid fa-chevron-right text-[8px] text-slate-600"></i>
                    <span class="font-medium text-slate-200">@yield('title', 'Tablero')</span>
                </div>
            </div>

            <!-- Right Side: Theme Toggle & Clock -->
            <div class="flex items-center space-x-3 md:space-x-4">

                <!-- Live Spanish Clock -->
                <div class="hidden sm:inline-flex items-center px-3 py-1 rounded-lg text-xs font-mono bg-white/5 border border-white/5 text-slate-300">
                    <i class="fa-regular fa-clock text-amber-500 mr-2 text-[11px]"></i>
                    <span id="realtime-clock">--:--:--</span>
                </div>

                <!-- Theme Toggle Button -->
                <button onclick="toggleTheme()" 
                        class="w-9 h-9 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-amber-400 hover:text-amber-300 transition-all flex items-center justify-center focus:outline-none shadow-sm active:scale-95" 
                        title="Alternar Modo Oscuro / Modo Claro">
                    <i id="theme-toggle-icon" class="fa-solid fa-sun text-xs"></i>
                </button>
            </div>

        </header>

        <!-- === MAIN PAGE CONTENT AREA === -->
        <main class="flex-1 relative overflow-y-auto p-4 md:p-6 lg:p-8 z-10 focus:outline-none page-fade-in">
            
            <!-- Toast Feedback Notifications -->
            <div id="toast-container" class="no-print fixed top-5 right-5 z-50 flex flex-col space-y-3 max-w-sm w-full pointer-events-none">
                @if(session('success'))
                    <div class="pointer-events-auto p-4 rounded-xl border border-emerald-500/30 bg-slate-900/95 shadow-xl text-slate-100 flex items-start space-x-3 transition-all duration-300"
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4500)" x-transition>
                        <i class="fa-solid fa-circle-check text-emerald-400 text-lg flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wider text-emerald-400">Operación Exitosa</p>
                            <p class="text-xs text-slate-200 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="text-slate-500 hover:text-slate-300"><i class="fa-solid fa-xmark text-xs"></i></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="pointer-events-auto p-4 rounded-xl border border-rose-500/30 bg-slate-900/95 shadow-xl text-slate-100 flex items-start space-x-3 transition-all duration-300"
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5500)" x-transition>
                        <i class="fa-solid fa-circle-exclamation text-rose-400 text-lg flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wider text-rose-400">Error de Operación</p>
                            <p class="text-xs text-slate-200 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-slate-500 hover:text-slate-300"><i class="fa-solid fa-xmark text-xs"></i></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="pointer-events-auto p-4 rounded-xl border border-rose-500/30 bg-slate-900/95 shadow-xl text-slate-100 flex items-start space-x-3 transition-all duration-300"
                         x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5500)" x-transition>
                        <i class="fa-solid fa-circle-exclamation text-rose-400 text-lg flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wider text-rose-400">Atención / Error</p>
                            <p class="text-xs text-slate-200 mt-0.5 leading-relaxed">{{ $errors->first() }}</p>
                        </div>
                        <button @click="show = false" class="text-slate-500 hover:text-slate-300"><i class="fa-solid fa-xmark text-xs"></i></button>
                    </div>
                @endif
            </div>

            @yield('content')

        </main>
    </div>

    <!-- === MOBILE SLIDE-OVER DRAWER === -->
    <div x-show="mobileMenuOpen" 
         x-cloak
         class="no-print fixed inset-0 z-50 md:hidden" 
         style="background: rgba(0,0,0,0.7); backdrop-filter: blur(4px);">
        
        <div class="fixed inset-y-0 left-0 w-72 p-4 flex flex-col shadow-2xl overflow-y-auto"
             style="background-color: var(--bg-surface-1);"
             @click.away="mobileMenuOpen = false"
             x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full">
            
            <div class="flex items-center justify-between pb-4 border-b border-white/5">
                <div class="flex items-center space-x-2.5">
                    <img src="{{ asset('images/logo_koyosita.png') }}" alt="KOYOSITA S.R.L." class="w-8 h-8 rounded-lg object-cover border border-amber-500/40">
                    <span class="font-bold text-sm text-slate-100">KOYOSITA S.R.L.</span>
                </div>
                <button @click="mobileMenuOpen = false" class="text-slate-400 hover:text-white p-1">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <nav class="flex-1 py-4 space-y-1 text-sm">
                <a href="{{ route('dashboard') }}" class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active-item' : '' }}">
                    <i class="fa-solid fa-chart-pie w-5 mr-3"></i> Tablero Principal
                </a>
                <div class="pt-3 pb-1 text-[10px] font-mono font-bold uppercase text-slate-500">Personal y Bocaminas</div>
                <a href="{{ route('bocaminas.index') }}" class="sidebar-nav-item {{ request()->routeIs('bocaminas.*') ? 'active-item' : '' }}">
                    <i class="fa-solid fa-mountain w-5 mr-3"></i> Bocaminas
                </a>
                <a href="{{ route('trabajadores.index') }}" class="sidebar-nav-item {{ request()->routeIs('trabajadores.*') ? 'active-item' : '' }}">
                    <i class="fa-solid fa-id-card-clip w-5 mr-3"></i> Personal y Contratos
                </a>
                <a href="{{ route('tipos-trabajo.index') }}" class="sidebar-nav-item {{ request()->routeIs('tipos-trabajo.*') ? 'active-item' : '' }}">
                    <i class="fa-solid fa-hammer w-5 mr-3"></i> Tipos de Trabajo
                </a>

                <div class="pt-3 pb-1 text-[10px] font-mono font-bold uppercase text-slate-500">Caja y Pagos</div>
                <a href="{{ route('fondos-caja.index') }}" class="sidebar-nav-item {{ request()->routeIs('fondos-caja.*') ? 'active-item' : '' }}">
                    <i class="fa-solid fa-cash-register w-5 mr-3"></i> Caja del Personal
                </a>
                <a href="{{ route('anticipos.index') }}" class="sidebar-nav-item {{ request()->routeIs('anticipos.*') ? 'active-item' : '' }}">
                    <i class="fa-solid fa-hand-holding-dollar w-5 mr-3"></i> Anticipos
                </a>
                <a href="{{ route('pagos.index') }}" class="sidebar-nav-item {{ request()->routeIs('pagos.*') ? 'active-item' : '' }}">
                    <i class="fa-solid fa-file-invoice-dollar w-5 mr-3"></i> Pagos y Planillas
                </a>
                <a href="{{ route('servicios-externos.index') }}" class="sidebar-nav-item {{ request()->routeIs('servicios-externos.*') ? 'active-item' : '' }}">
                    <i class="fa-solid fa-truck-ramp-box w-5 mr-3"></i> Servicios Externos
                </a>
                <a href="{{ route('reportes.index') }}" class="sidebar-nav-item {{ request()->routeIs('reportes.index') ? 'active-item' : '' }}">
                    <i class="fa-solid fa-chart-column w-5 mr-3"></i> Reportes
                </a>

                <div class="pt-3 pb-1 text-[10px] font-mono font-bold uppercase text-slate-500">Comercialización Mineral</div>
                <a href="{{ route('transacciones-minerales.index', ['tab' => 'caja']) }}" class="sidebar-nav-item {{ (request()->routeIs('transacciones-minerales.*') && request('tab') === 'caja') ? 'active-item' : '' }}">
                    <i class="fa-solid fa-scale-balanced w-5 mr-3"></i> Caja del Módulo
                </a>
                <a href="{{ route('transacciones-minerales.index', ['tab' => 'compras']) }}" class="sidebar-nav-item {{ (request()->routeIs('transacciones-minerales.*') && request('tab', 'compras') === 'compras') ? 'active-item' : '' }}">
                    <i class="fa-solid fa-arrow-down-to-bracket w-5 mr-3"></i> Compras de Lotes
                </a>
                <a href="{{ route('transacciones-minerales.index', ['tab' => 'ventas']) }}" class="sidebar-nav-item {{ (request()->routeIs('transacciones-minerales.*') && request('tab') === 'ventas') ? 'active-item' : '' }}">
                    <i class="fa-solid fa-arrow-up-from-bracket w-5 mr-3"></i> Ventas de Mineral
                </a>
                <a href="{{ route('transacciones-minerales.index', ['tab' => 'stock']) }}" class="sidebar-nav-item {{ (request()->routeIs('transacciones-minerales.*') && request('tab') === 'stock') ? 'active-item' : '' }}">
                    <i class="fa-solid fa-boxes-stacked w-5 mr-3"></i> Stock de Mineral
                </a>
                <a href="{{ route('reportes-comercializacion.index') }}" class="sidebar-nav-item {{ request()->routeIs('reportes-comercializacion.*') ? 'active-item' : '' }}">
                    <i class="fa-solid fa-chart-line w-5 mr-3"></i> Reportes Comercializ.
                </a>

                <div class="pt-3 pb-1 text-[10px] font-mono font-bold uppercase text-slate-500">Sistema</div>
                <a href="{{ route('backups.index') }}" class="sidebar-nav-item {{ request()->routeIs('backups.*') ? 'active-item' : '' }}">
                    <i class="fa-solid fa-database w-5 mr-3"></i> Respaldos
                </a>
                <a href="{{ route('profile.edit') }}" class="sidebar-nav-item {{ request()->routeIs('profile.*') ? 'active-item' : '' }}">
                    <i class="fa-solid fa-user-shield w-5 mr-3"></i> Perfil
                </a>
            </nav>

            <form action="{{ route('logout') }}" method="POST" class="pt-3 border-t border-white/5">
                @csrf
                <button type="submit" class="w-full py-2 px-3 rounded-lg text-rose-400 bg-rose-500/10 text-xs font-bold text-center block">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>

    <!-- === GLOBAL CORE SCRIPTS === -->
    <script>
        // 1. Reloj en tiempo real
        function updateClock() {
            const clockEl = document.getElementById('realtime-clock');
            if (!clockEl) return;
            const now = new Date();
            const timeStr = now.toLocaleTimeString('es-BO', { hour12: false });
            clockEl.textContent = timeStr;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // 2. Modal de confirmación nativo del sistema (evita alertas del navegador "127.0.0.1:8000 dice")
        function createCustomConfirmModal(message, reportUrl, onConfirm) {
            const existing = document.getElementById('scpm-custom-confirm-modal');
            if (existing) existing.remove();

            const modalContainer = document.createElement('div');
            modalContainer.id = 'scpm-custom-confirm-modal';
            modalContainer.style.cssText = 'position: fixed !important; top: 0 !important; left: 0 !important; right: 0 !important; bottom: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 2147483647 !important; display: flex !important; align-items: center !important; justify-content: center !important; padding: 16px !important; background-color: rgba(2, 6, 23, 0.85) !important; backdrop-filter: blur(10px) !important; -webkit-backdrop-filter: blur(10px) !important; opacity: 0; transition: opacity 0.18s ease;';

            const reportBtnHtml = reportUrl ? `
                <a href="${reportUrl}" style="padding: 10px 16px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; border-radius: 12px; background: rgba(245, 158, 11, 0.16); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.35); display: inline-flex; align-items: center; gap: 6px; text-decoration: none;">
                    <i class="fa-solid fa-file-lines"></i> Ver Reporte
                </a>
            ` : '';

            modalContainer.innerHTML = `
                <div class="scpm-modal-card" style="position: relative; z-index: 2147483647; width: 100%; max-width: 420px; border-radius: 18px; background-color: #0f172a; border: 1px solid rgba(148, 163, 184, 0.28); box-shadow: 0 25px 60px -10px rgba(0, 0, 0, 0.95), 0 0 0 1px rgba(255, 255, 255, 0.08); overflow: hidden; transform: scale(0.95); transition: transform 0.18s ease;">
                    <div style="height: 4px; width: 100%; background: linear-gradient(90deg, #f59e0b 0%, #f43f5e 50%, #dc2626 100%);"></div>
                    <div style="padding: 28px 24px 22px; text-align: center;">
                        <div style="margin: 0 auto 16px; width: 56px; height: 56px; border-radius: 16px; background: rgba(244, 63, 94, 0.12); border: 1px solid rgba(244, 63, 94, 0.32); display: flex; align-items: center; justify-content: center; color: #fb7185; box-shadow: 0 8px 20px rgba(244, 63, 94, 0.15);">
                            <i class="fa-solid fa-triangle-exclamation" style="font-size: 24px;"></i>
                        </div>
                        <h3 style="margin: 0 0 8px; font-size: 18px; font-weight: 800; color: #ffffff; letter-spacing: -0.01em;">Confirmar Acción</h3>
                        <p style="margin: 0; font-size: 14px; color: #e2e8f0; font-weight: 500; line-height: 1.55; padding: 0 8px;">${message}</p>
                    </div>
                    <div style="padding: 16px 24px; border-top: 1px solid rgba(51, 65, 85, 0.7); background-color: #090d16; display: flex; flex-wrap: wrap; justify-content: center; gap: 12px;">
                        <button type="button" id="scpm-confirm-cancel" style="padding: 10px 18px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; border-radius: 12px; background-color: #1e293b; color: #e2e8f0; border: 1px solid #334155; cursor: pointer; transition: all 0.15s ease;">
                            Cancelar
                        </button>
                        ${reportBtnHtml}
                        <button type="button" id="scpm-confirm-ok" style="padding: 10px 22px; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; border-radius: 12px; background: linear-gradient(135deg, #e11d48 0%, #dc2626 100%); color: #ffffff; border: 1px solid rgba(251, 113, 133, 0.3); box-shadow: 0 6px 18px rgba(225, 29, 72, 0.35); cursor: pointer; transition: all 0.15s ease;">
                            Confirmar
                        </button>
                    </div>
                </div>
            `;

            document.body.appendChild(modalContainer);
            const card = modalContainer.querySelector('.scpm-modal-card');
            requestAnimationFrame(() => {
                modalContainer.style.opacity = '1';
                if (card) card.style.transform = 'scale(1)';
            });

            const closeModal = () => {
                modalContainer.style.opacity = '0';
                if (card) card.style.transform = 'scale(0.95)';
                setTimeout(() => modalContainer.remove(), 180);
            };

            modalContainer.querySelector('#scpm-confirm-cancel').addEventListener('click', closeModal);
            modalContainer.querySelector('#scpm-confirm-ok').addEventListener('click', () => {
                closeModal();
                if (onConfirm) onConfirm();
            });
            modalContainer.addEventListener('click', (ev) => {
                if (ev.target === modalContainer) closeModal();
            });
        }

        // Interceptar todos los formularios con onsubmit="return confirm(...)" antes del navegador
        document.addEventListener('click', function (e) {
            const button = e.target.closest('button[type="submit"], input[type="submit"]');
            if (!button) return;
            const form = button.closest('form');
            if (!form) return;

            const onsubmitAttr = form.getAttribute('onsubmit');
            if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                e.preventDefault();
                e.stopImmediatePropagation();

                const match = onsubmitAttr.match(/confirm\(\s*['"]([\s\S]*?)['"]\s*\)/);
                const message = match ? match[1] : '¿Está seguro de realizar esta operación?';

                let reportUrl = null;
                if (form.action) {
                    if (form.action.includes('/trabajadores/')) {
                        const idMatch = form.action.match(/\/trabajadores\/(\d+)/);
                        if (idMatch) reportUrl = `/reportes?tab=trabajador&trabajador_id=${idMatch[1]}`;
                    } else if (form.action.includes('/bocaminas/')) {
                        const idMatch = form.action.match(/\/bocaminas\/(\d+)/);
                        if (idMatch) reportUrl = `/reportes?tab=bocamina&bocamina_id=${idMatch[1]}`;
                    } else if (form.action.includes('/anticipos/')) {
                        reportUrl = '/reportes?tab=anticipos';
                    }
                }

                createCustomConfirmModal(message, reportUrl, () => {
                    form.removeAttribute('onsubmit');
                    form.submit();
                });
            }
        }, true);

        // Reemplazar window.alert para nunca mostrar "127.0.0.1:8000 dice"
        window.alert = function (msg) {
            createCustomConfirmModal(String(msg), null, null);
            const cancelBtn = document.getElementById('scpm-confirm-cancel');
            if (cancelBtn) cancelBtn.style.display = 'none';
            const okBtn = document.getElementById('scpm-confirm-ok');
            if (okBtn) okBtn.textContent = 'Entendido';
        };

        // 3. Alternador de tema día / noche
        function toggleTheme() {
            const html = document.documentElement;
            const icon = document.getElementById('theme-toggle-icon');
            if (html.classList.contains('light-theme')) {
                html.classList.remove('light-theme');
                localStorage.setItem('theme', 'dark');
                if (icon) {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                }
            } else {
                html.classList.add('light-theme');
                localStorage.setItem('theme', 'light');
                if (icon) {
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                }
            }
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: localStorage.getItem('theme') } }));
        }

        document.addEventListener('DOMContentLoaded', () => {
            const icon = document.getElementById('theme-toggle-icon');
            if (icon && document.documentElement.classList.contains('light-theme')) {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });

        // 4. Protección frente a doble envío en formularios
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form && form instanceof HTMLFormElement) {
                if (form.getAttribute('data-submitting') === 'true') {
                    e.preventDefault();
                    return;
                }
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    form.setAttribute('data-submitting', 'true');
                    const originalHtml = submitBtn.innerHTML;
                    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                    submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin mr-1.5"></i> Procesando...';
                    setTimeout(() => {
                        form.removeAttribute('data-submitting');
                        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        submitBtn.innerHTML = originalHtml;
                    }, 4000);
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
