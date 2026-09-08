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
    <title>@yield('title', 'Sistema de Pagos Mineros') - SCPM</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- SheetJS (XLSX) & html2pdf -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        :root {
            --bg-main: #020617;
            --bg-card: rgba(15, 23, 42, 0.45);
            --border-card: rgba(255, 255, 255, 0.06);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --bg-sidebar: rgba(15, 23, 42, 0.8);
            --border-sidebar: rgba(255, 255, 255, 0.06);
            --bg-header: rgba(15, 23, 42, 0.7);
            --text-nav: #94a3b8;
            --text-nav-hover: #f8fafc;
            --bg-input: rgba(15, 23, 42, 0.45);
            --border-input: rgba(255, 255, 255, 0.08);
            --text-input: #f8fafc;
            --canvas-opacity: 0.4;
        }

        [x-cloak] {
            display: none !important;
        }

        /* Light theme autofill overrides */
        .light-theme input:-webkit-autofill,
        .light-theme input:-webkit-autofill:hover, 
        .light-theme input:-webkit-autofill:focus,
        .light-theme textarea:-webkit-autofill,
        .light-theme textarea:-webkit-autofill:hover,
        .light-theme textarea:-webkit-autofill:focus,
        .light-theme select:-webkit-autofill,
        .light-theme select:-webkit-autofill:hover,
        .light-theme select:-webkit-autofill:focus {
            -webkit-text-fill-color: #0f172a !important;
            -webkit-box-shadow: 0 0 0px 1000px #ffffff inset !important;
            transition: background-color 5000s ease-in-out 0s !important;
        }

        .light-theme {
            --bg-main: #f8fafc;
            --bg-card: rgba(255, 255, 255, 0.85);
            --border-card: rgba(15, 23, 42, 0.08);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --bg-sidebar: rgba(241, 245, 249, 0.95);
            --border-sidebar: rgba(15, 23, 42, 0.08);
            --bg-header: rgba(241, 245, 249, 0.8);
            --text-nav: #475569;
            --text-nav-hover: #0f172a;
            --bg-input: rgba(255, 255, 255, 0.95);
            --border-input: rgba(15, 23, 42, 0.15);
            --text-input: #0f172a;
            --canvas-opacity: 0.12;
        }

        html, body {
            background-color: var(--bg-main) !important;
            color: var(--text-main) !important;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        body {
            font-family: 'Outfit', sans-serif;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        .light-theme ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #f59e0b;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #d97706;
        }
        /* Glassmorphism card utilities */
        .glass-card {
            background: var(--bg-card) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-card) !important;
            position: relative;
            overflow: hidden;
            transition: background 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .light-theme .glass-card {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02) !important;
        }
        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #f59e0b, #ea580c, transparent);
            opacity: 0.85;
            z-index: 10;
        }
        .gold-glow {
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.15);
        }
        .gold-border-glow:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 10px rgba(245, 158, 11, 0.3);
        }

        /* Custom sidebar menu items hover and active colors */
        .nav-item {
            color: #94a3b8 !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border: 1.2px solid transparent !important;
            background: transparent !important;
            border-radius: 12px !important;
        }
        .light-theme .nav-item {
            color: #475569 !important;
        }

        /* AMBER */
        .nav-item[data-theme-color="amber"]:hover, .nav-item[data-theme-color="amber"].active-nav-item {
            color: #fbbf24 !important;
            background: linear-gradient(95deg, rgba(245, 158, 11, 0.15) 0%, rgba(245, 158, 11, 0.03) 100%) !important;
            border: 1.2px solid rgba(245, 158, 11, 0.45) !important;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.12) !important;
        }
        .nav-item[data-theme-color="amber"]:hover i, .nav-item[data-theme-color="amber"].active-nav-item i { color: #fbbf24 !important; }
        .light-theme .nav-item[data-theme-color="amber"]:hover, .light-theme .nav-item[data-theme-color="amber"].active-nav-item {
            color: #b45309 !important;
            background: linear-gradient(95deg, rgba(245, 158, 11, 0.16) 0%, rgba(245, 158, 11, 0.05) 100%) !important;
            border: 1.2px solid rgba(245, 158, 11, 0.4) !important;
            box-shadow: 0 3px 8px rgba(245, 158, 11, 0.05) !important;
        }
        .light-theme .nav-item[data-theme-color="amber"]:hover i, .light-theme .nav-item[data-theme-color="amber"].active-nav-item i { color: #b45309 !important; }

        /* EMERALD */
        .nav-item[data-theme-color="emerald"]:hover, .nav-item[data-theme-color="emerald"].active-nav-item {
            color: #34d399 !important;
            background: linear-gradient(95deg, rgba(16, 185, 129, 0.15) 0%, rgba(16, 185, 129, 0.03) 100%) !important;
            border: 1.2px solid rgba(16, 185, 129, 0.45) !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.12) !important;
        }
        .nav-item[data-theme-color="emerald"]:hover i, .nav-item[data-theme-color="emerald"].active-nav-item i { color: #34d399 !important; }
        .light-theme .nav-item[data-theme-color="emerald"]:hover, .light-theme .nav-item[data-theme-color="emerald"].active-nav-item {
            color: #047857 !important;
            background: linear-gradient(95deg, rgba(16, 185, 129, 0.16) 0%, rgba(16, 185, 129, 0.05) 100%) !important;
            border: 1.2px solid rgba(16, 185, 129, 0.4) !important;
            box-shadow: 0 3px 8px rgba(16, 185, 129, 0.05) !important;
        }
        .light-theme .nav-item[data-theme-color="emerald"]:hover i, .light-theme .nav-item[data-theme-color="emerald"].active-nav-item i { color: #047857 !important; }

        /* SKY */
        .nav-item[data-theme-color="sky"]:hover, .nav-item[data-theme-color="sky"].active-nav-item {
            color: #38bdf8 !important;
            background: linear-gradient(95deg, rgba(14, 165, 233, 0.15) 0%, rgba(14, 165, 233, 0.03) 100%) !important;
            border: 1.2px solid rgba(14, 165, 233, 0.45) !important;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.12) !important;
        }
        .nav-item[data-theme-color="sky"]:hover i, .nav-item[data-theme-color="sky"].active-nav-item i { color: #38bdf8 !important; }
        .light-theme .nav-item[data-theme-color="sky"]:hover, .light-theme .nav-item[data-theme-color="sky"].active-nav-item {
            color: #0369a1 !important;
            background: linear-gradient(95deg, rgba(14, 165, 233, 0.16) 0%, rgba(14, 165, 233, 0.05) 100%) !important;
            border: 1.2px solid rgba(14, 165, 233, 0.4) !important;
            box-shadow: 0 3px 8px rgba(14, 165, 233, 0.05) !important;
        }
        .light-theme .nav-item[data-theme-color="sky"]:hover i, .light-theme .nav-item[data-theme-color="sky"].active-nav-item i { color: #0369a1 !important; }

        /* INDIGO */
        .nav-item[data-theme-color="indigo"]:hover, .nav-item[data-theme-color="indigo"].active-nav-item {
            color: #818cf8 !important;
            background: linear-gradient(95deg, rgba(129, 140, 248, 0.15) 0%, rgba(129, 140, 248, 0.03) 100%) !important;
            border: 1.2px solid rgba(129, 140, 248, 0.45) !important;
            box-shadow: 0 4px 12px rgba(129, 140, 248, 0.12) !important;
        }
        .nav-item[data-theme-color="indigo"]:hover i, .nav-item[data-theme-color="indigo"].active-nav-item i { color: #818cf8 !important; }
        .light-theme .nav-item[data-theme-color="indigo"]:hover, .light-theme .nav-item[data-theme-color="indigo"].active-nav-item {
            color: #4338ca !important;
            background: linear-gradient(95deg, rgba(129, 140, 248, 0.16) 0%, rgba(129, 140, 248, 0.05) 100%) !important;
            border: 1.2px solid rgba(129, 140, 248, 0.4) !important;
            box-shadow: 0 3px 8px rgba(129, 140, 248, 0.05) !important;
        }
        .light-theme .nav-item[data-theme-color="indigo"]:hover i, .light-theme .nav-item[data-theme-color="indigo"].active-nav-item i { color: #4338ca !important; }

        /* ROSE */
        .nav-item[data-theme-color="rose"]:hover, .nav-item[data-theme-color="rose"].active-nav-item {
            color: #fda4af !important;
            background: linear-gradient(95deg, rgba(251, 113, 133, 0.15) 0%, rgba(251, 113, 133, 0.03) 100%) !important;
            border: 1.2px solid rgba(251, 113, 133, 0.45) !important;
            box-shadow: 0 4px 12px rgba(251, 113, 133, 0.12) !important;
        }
        .nav-item[data-theme-color="rose"]:hover i, .nav-item[data-theme-color="rose"].active-nav-item i { color: #fda4af !important; }
        .light-theme .nav-item[data-theme-color="rose"]:hover, .light-theme .nav-item[data-theme-color="rose"].active-nav-item {
            color: #be123c !important;
            background: linear-gradient(95deg, rgba(251, 113, 133, 0.16) 0%, rgba(251, 113, 133, 0.05) 100%) !important;
            border: 1.2px solid rgba(251, 113, 133, 0.4) !important;
            box-shadow: 0 3px 8px rgba(251, 113, 133, 0.05) !important;
        }
        .light-theme .nav-item[data-theme-color="rose"]:hover i, .light-theme .nav-item[data-theme-color="rose"].active-nav-item i { color: #be123c !important; }

        /* TEAL */
        .nav-item[data-theme-color="teal"]:hover, .nav-item[data-theme-color="teal"].active-nav-item {
            color: #2dd4bf !important;
            background: linear-gradient(95deg, rgba(45, 212, 191, 0.15) 0%, rgba(45, 212, 191, 0.03) 100%) !important;
            border: 1.2px solid rgba(45, 212, 191, 0.45) !important;
            box-shadow: 0 4px 12px rgba(45, 212, 191, 0.12) !important;
        }
        .nav-item[data-theme-color="teal"]:hover i, .nav-item[data-theme-color="teal"].active-nav-item i { color: #2dd4bf !important; }
        .light-theme .nav-item[data-theme-color="teal"]:hover, .light-theme .nav-item[data-theme-color="teal"].active-nav-item {
            color: #0f766e !important;
            background: linear-gradient(95deg, rgba(45, 212, 191, 0.16) 0%, rgba(45, 212, 191, 0.05) 100%) !important;
            border: 1.2px solid rgba(45, 212, 191, 0.4) !important;
            box-shadow: 0 3px 8px rgba(45, 212, 191, 0.05) !important;
        }
        .light-theme .nav-item[data-theme-color="teal"]:hover i, .light-theme .nav-item[data-theme-color="teal"].active-nav-item i { color: #0f766e !important; }

        /* ORANGE */
        .nav-item[data-theme-color="orange"]:hover, .nav-item[data-theme-color="orange"].active-nav-item {
            color: #ff9d43 !important;
            background: linear-gradient(95deg, rgba(249, 115, 22, 0.15) 0%, rgba(249, 115, 22, 0.03) 100%) !important;
            border: 1.2px solid rgba(249, 115, 22, 0.45) !important;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.12) !important;
        }
        .nav-item[data-theme-color="orange"]:hover i, .nav-item[data-theme-color="orange"].active-nav-item i { color: #ff9d43 !important; }
        .light-theme .nav-item[data-theme-color="orange"]:hover, .light-theme .nav-item[data-theme-color="orange"].active-nav-item {
            color: #c2410c !important;
            background: linear-gradient(95deg, rgba(249, 115, 22, 0.16) 0%, rgba(249, 115, 22, 0.05) 100%) !important;
            border: 1.2px solid rgba(249, 115, 22, 0.4) !important;
            box-shadow: 0 3px 8px rgba(249, 115, 22, 0.05) !important;
        }
        .light-theme .nav-item[data-theme-color="orange"]:hover i, .light-theme .nav-item[data-theme-color="orange"].active-nav-item i { color: #c2410c !important; }

        /* VIOLET */
        .nav-item[data-theme-color="violet"]:hover, .nav-item[data-theme-color="violet"].active-nav-item {
            color: #c084fc !important;
            background: linear-gradient(95deg, rgba(167, 139, 250, 0.15) 0%, rgba(167, 139, 250, 0.03) 100%) !important;
            border: 1.2px solid rgba(167, 139, 250, 0.45) !important;
            box-shadow: 0 4px 12px rgba(167, 139, 250, 0.12) !important;
        }
        .nav-item[data-theme-color="violet"]:hover i, .nav-item[data-theme-color="violet"].active-nav-item i { color: #c084fc !important; }
        .light-theme .nav-item[data-theme-color="violet"]:hover, .light-theme .nav-item[data-theme-color="violet"].active-nav-item {
            color: #6d28d9 !important;
            background: linear-gradient(95deg, rgba(167, 139, 250, 0.16) 0%, rgba(167, 139, 250, 0.05) 100%) !important;
            border: 1.2px solid rgba(167, 139, 250, 0.4) !important;
            box-shadow: 0 3px 8px rgba(167, 139, 250, 0.05) !important;
        }
        .light-theme .nav-item[data-theme-color="violet"]:hover i, .light-theme .nav-item[data-theme-color="violet"].active-nav-item i { color: #6d28d9 !important; }

        /* CYAN */
        .nav-item[data-theme-color="cyan"]:hover, .nav-item[data-theme-color="cyan"].active-nav-item {
            color: #22d3ee !important;
            background: linear-gradient(95deg, rgba(6, 182, 212, 0.15) 0%, rgba(6, 182, 212, 0.03) 100%) !important;
            border: 1.2px solid rgba(6, 182, 212, 0.45) !important;
            box-shadow: 0 4px 12px rgba(6, 182, 212, 0.12) !important;
        }
        .nav-item[data-theme-color="cyan"]:hover i, .nav-item[data-theme-color="cyan"].active-nav-item i { color: #22d3ee !important; }
        .light-theme .nav-item[data-theme-color="cyan"]:hover, .light-theme .nav-item[data-theme-color="cyan"].active-nav-item {
            color: #0e7490 !important;
            background: linear-gradient(95deg, rgba(6, 182, 212, 0.16) 0%, rgba(6, 182, 212, 0.05) 100%) !important;
            border: 1.2px solid rgba(6, 182, 212, 0.4) !important;
            box-shadow: 0 3px 8px rgba(6, 182, 212, 0.05) !important;
        }
        .light-theme .nav-item[data-theme-color="cyan"]:hover i, .light-theme .nav-item[data-theme-color="cyan"].active-nav-item i { color: #0e7490 !important; }
        
        /* Global button hover spark styles */
        .global-button-spark {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
        }
        
        /* Premium button hover glows */
        button, .btn, [type="submit"], [type="button"] {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
        }
        
        /* Submit button hover glow */
        button[type="submit"], [type="submit"] {
            box-shadow: 0 0 10px rgba(245, 158, 11, 0.1);
        }
        button[type="submit"]:hover, [type="submit"]:hover {
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.35);
            transform: translateY(-1px);
        }
        
        /* Delete/Danger button hover glow */
        .bg-red-600:hover, .bg-red-500:hover, [class*="bg-red-"]:hover {
            box-shadow: 0 0 18px rgba(239, 68, 68, 0.45) !important;
            transform: translateY(-1px);
        }
        
        /* Success/emerald button hover glow */
        .bg-emerald-600:hover, .bg-emerald-500:hover, [class*="bg-emerald-"]:hover {
            box-shadow: 0 0 18px rgba(16, 185, 129, 0.45) !important;
            transform: translateY(-1px);
        }

        /* Vibrant animated buttons with moving gradient backgrounds */
        .btn-vibrant-amber {
            background: linear-gradient(135deg, #f59e0b 0%, #f97316 50%, #f59e0b 100%) !important;
            background-size: 200% auto !important;
            color: #020617 !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25) !important;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        .btn-vibrant-success {
            background: linear-gradient(135deg, #10b981 0%, #14b8a6 50%, #10b981 100%) !important;
            background-size: 200% auto !important;
            color: #020617 !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25) !important;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        .btn-vibrant-danger {
            background: linear-gradient(135deg, #ef4444 0%, #f43f5e 50%, #ef4444 100%) !important;
            background-size: 200% auto !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25) !important;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        .btn-vibrant-indigo {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 50%, #4f46e5 100%) !important;
            background-size: 200% auto !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25) !important;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        .btn-vibrant-warm {
            background: linear-gradient(135deg, #ea580c 0%, #d97706 50%, #ea580c 100%) !important;
            background-size: 200% auto !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.25) !important;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        
        .btn-vibrant-amber:hover, .btn-vibrant-success:hover, .btn-vibrant-danger:hover, .btn-vibrant-indigo:hover, .btn-vibrant-warm:hover {
            background-position: right center !important;
            transform: translateY(-2px) !important;
        }
        .btn-vibrant-amber:hover {
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.48) !important;
        }
        .btn-vibrant-success:hover {
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.48) !important;
        }
        .btn-vibrant-danger:hover {
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.48) !important;
        }
        .btn-vibrant-indigo:hover {
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.48) !important;
        }
        .btn-vibrant-warm:hover {
            box-shadow: 0 8px 25px rgba(234, 88, 12, 0.48) !important;
        }
        .btn-vibrant-amber:active, .btn-vibrant-success:active, .btn-vibrant-danger:active, .btn-vibrant-indigo:active, .btn-vibrant-warm:active {
            transform: translateY(0) !important;
        }

        /* Float Screen Toast System */
        .toast-item {
            background: rgba(15, 23, 42, 0.82) !important;
            backdrop-filter: blur(15px) !important;
            -webkit-backdrop-filter: blur(15px) !important;
            border-radius: 16px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.55) !important;
            position: relative;
            overflow: hidden;
            border: 1.5px solid transparent;
            animation: toastSlideIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transition: all 0.35s ease;
        }
        .toast-success {
            border-color: rgba(16, 185, 129, 0.35) !important;
            box-shadow: 0 0 25px rgba(16, 185, 129, 0.18) !important;
        }
        .toast-danger {
            border-color: rgba(244, 63, 94, 0.35) !important;
            box-shadow: 0 0 25px rgba(244, 63, 94, 0.18) !important;
        }
        @keyframes toastSlideIn {
            from {
                opacity: 0;
                transform: translateX(120%) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }
        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 100%;
            animation: toastTimer linear forwards;
        }
        .toast-success .toast-progress {
            animation-duration: 4.5s;
        }
        .toast-danger .toast-progress {
            animation-duration: 5.5s;
        }
        @keyframes toastTimer {
            from { width: 100%; }
            to { width: 0%; }
        }
        
        /* Print Styles */
        @media print {
            .no-print {
                display: none !important;
            }
            .print-only {
                display: block !important;
            }
            body {
                background: white !important;
                color: black !important;
            }
            .print-container {
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                border: none !important;
                background: white !important;
                box-shadow: none !important;
            }
        }

        /* Custom Confirmation Modal styles */
        #custom-confirm-modal {
            transition: opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }
        #custom-confirm-modal .glass-card {
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        #custom-confirm-modal.modal-hide {
            opacity: 0 !important;
        }
        #custom-confirm-modal.modal-hide .glass-card {
            transform: scale(0.9) !important;
        }

        /* Premium Global Inputs */
        input[type="text"], 
        input[type="email"], 
        input[type="password"], 
        input[type="number"], 
        input[type="date"], 
        select, 
        textarea {
            background: rgba(15, 23, 42, 0.45) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 10px !important;
            color: #f8fafc !important;
            font-family: 'Outfit', sans-serif !important;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        
        input[type="text"]:focus, 
        input[type="email"]:focus, 
        input[type="password"]:focus, 
        input[type="number"]:focus, 
        input[type="date"]:focus, 
        select:focus, 
        textarea:focus {
            outline: none !important;
            border-color: rgba(245, 158, 11, 0.6) !important;
            box-shadow: 0 0 14px rgba(245, 158, 11, 0.25) !important;
            background: rgba(15, 23, 42, 0.65) !important;
            transform: translateY(-0.5px);
        }

        /* Autofill overrides */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus,
        textarea:-webkit-autofill,
        textarea:-webkit-autofill:hover,
        textarea:-webkit-autofill:focus,
        select:-webkit-autofill,
        select:-webkit-autofill:hover,
        select:-webkit-autofill:focus {
            -webkit-text-fill-color: #f8fafc !important;
            -webkit-box-shadow: 0 0 0px 1000px rgba(15, 23, 42, 0.65) inset !important;
            transition: background-color 5000s ease-in-out 0s !important;
        }

        /* Sidebar Navigation Hover & Active Structural Styles */
        .nav-item {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border-left: none !important;
            padding-left: 10px !important;
            font-size: 13px !important;
            margin: 2px 0;
            border-radius: 6px !important;
            display: flex;
            align-items: center;
        }
        .nav-item:hover {
            transform: translateX(3px) !important;
        }
        .nav-item i {
            transition: transform 0.2s ease, color 0.2s ease !important;
        }
        .nav-item:hover i {
            transform: scale(1.08) !important;
        }
        
        /* Clean minimalist category containers */
        .sidebar-category-card {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin-top: 24px !important;
            margin-bottom: 8px !important;
        }
        
        /* Category Header Styles */
        .category-header-btn {
            background: transparent !important;
            border-radius: 0;
            padding: 4px 8px !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            letter-spacing: 0.1em !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            transition: color 0.2s ease;
        }
        .category-header-btn:hover {
            background: transparent !important;
            color: #94a3b8 !important;
        }
        .light-theme .category-header-btn {
            color: #64748b !important;
        }
        
        .nav-item.active-nav-item i {
            filter: none !important;
        }

        /* Clean standardized icons - NO square boxes */
        .nav-icon-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: auto !important;
            height: auto !important;
            background: transparent !important;
            border: none !important;
            margin-right: 10px !important;
            color: inherit !important;
        }

        /* --- LIGHT THEME (DAY MODE) OVERRIDES --- */
        .light-theme .text-slate-100 { color: #0f172a !important; }
        .light-theme .text-slate-200 { color: #1e293b !important; }
        .light-theme .text-slate-300 { color: #334155 !important; }
        .light-theme .text-slate-400 { color: #475569 !important; }
        .light-theme .text-slate-450 { color: #64748b !important; }
        .light-theme .text-slate-500 { color: #64748b !important; }
        
        .light-theme .sidebar-bg {
            background-color: var(--bg-sidebar) !important;
            border-right: 1px solid var(--border-sidebar) !important;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        
        .light-theme .header-bg {
            background-color: var(--bg-header) !important;
            border-bottom: 1px solid var(--border-sidebar) !important;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        
        .light-theme .md\:hidden.fixed.top-0 {
            background-color: var(--bg-sidebar) !important;
            border-bottom: 1px solid var(--border-sidebar) !important;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }
        .light-theme .md\:hidden.fixed.top-0 h1 {
            color: #ea580c !important;
        }
        .light-theme .md\:hidden.fixed.top-0 button {
            color: #475569 !important;
        }
        .light-theme .md\:hidden.fixed.top-0 button:hover {
            color: #0f172a !important;
        }
        
        .light-theme .md\:hidden.fixed.top-0 div[x-show="open"] {
            background-color: var(--bg-sidebar) !important;
            border-bottom: 1px solid var(--border-sidebar) !important;
        }
        .light-theme .md\:hidden.fixed.top-0 div[x-show="open"] a {
            color: #475569 !important;
        }
        .light-theme .md\:hidden.fixed.top-0 div[x-show="open"] a:hover {
            background-color: rgba(15, 23, 42, 0.05) !important;
            color: #0f172a !important;
        }
        
        .light-theme #particle-canvas {
            opacity: var(--canvas-opacity) !important;
            transition: opacity 0.3s ease;
        }
        
        .light-theme .nav-item {
            color: #475569 !important;
        }
        .light-theme .nav-item:hover {
            color: #1e293b !important;
        }
        .light-theme .nav-item.active-nav-item {
            font-weight: 700 !important;
        }
        
        .light-theme .border-t.border-slate-800\/80 {
            border-top-color: rgba(15, 23, 42, 0.08) !important;
        }
        .light-theme .border-t.border-slate-800\/80 p {
            color: #0f172a !important;
        }
        .light-theme .border-t.border-slate-800\/80 button {
            color: #4f46e5 !important;
        }
        
        .light-theme #realtime-clock {
            color: #475569 !important;
        }
        
        .light-theme .bg-slate-900\/40 {
            background-color: rgba(15, 23, 42, 0.04) !important;
        }
        .light-theme th {
            color: #475569 !important;
        }
        .light-theme td {
            color: #334155 !important;
        }
        .light-theme td.text-slate-100 {
            color: #0f172a !important;
        }
        .light-theme td.text-slate-200 {
            color: #1e293b !important;
        }
        .light-theme tr.hover\:bg-slate-900\/10:hover {
            background-color: rgba(15, 23, 42, 0.03) !important;
        }
        
        .light-theme input[type="text"], 
        .light-theme input[type="email"], 
        .light-theme input[type="password"], 
        .light-theme input[type="number"], 
        .light-theme input[type="date"], 
        .light-theme select, 
        .light-theme textarea {
            background: rgba(255, 255, 255, 0.95) !important;
            border: 1px solid rgba(15, 23, 42, 0.15) !important;
            color: #0f172a !important;
        }
        
        .light-theme input[type="text"]:focus, 
        .light-theme input[type="email"]:focus, 
        .light-theme input[type="password"]:focus, 
        .light-theme input[type="number"]:focus, 
        .light-theme input[type="date"]:focus, 
        .light-theme select:focus, 
        .light-theme textarea:focus {
            background: #ffffff !important;
            border-color: #f59e0b !important;
            box-shadow: 0 0 10px rgba(245, 158, 11, 0.15) !important;
        }
        
        .light-theme .bg-slate-800 {
            background-color: #e2e8f0 !important;
            color: #334155 !important;
        }
        .light-theme .bg-slate-800:hover {
            background-color: #cbd5e1 !important;
        }
        
        .light-theme .border-slate-700,
        .light-theme .border-slate-800,
        .light-theme .border-slate-700\/80,
        .light-theme .border-slate-800\/60 {
            border-color: rgba(15, 23, 42, 0.08) !important;
        }
        
        .light-theme .toast-item {
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
            border-color: rgba(15, 23, 42, 0.06) !important;
        }
        .light-theme .toast-item .text-slate-100 {
            color: #0f172a !important;
        }
        
        .light-theme #custom-confirm-modal {
            background: rgba(15, 23, 42, 0.45) !important;
        }
        .light-theme #custom-confirm-modal .glass-card {
            background: #ffffff !important;
            border-color: rgba(239, 68, 68, 0.3) !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        }
        .light-theme #custom-confirm-modal h3 {
            color: #ef4444 !important;
        }
        .light-theme #custom-confirm-modal p {
            color: #334155 !important;
        }

        /* --- PREVENT CONFLICTS AND SET PLUS JAKARTA SANS ON SIDEBAR --- */
        .sidebar-bg,
        .sidebar-bg button,
        .sidebar-bg a,
        .sidebar-bg span,
        .sidebar-bg p,
        .sidebar-bg div {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        .sidebar-bg i, 
        .sidebar-bg .fa, 
        .sidebar-bg .fa-solid, 
        .sidebar-bg .fa-regular, 
        .sidebar-bg .fa-brands {
            font-family: 'Font Awesome 6 Free', 'Font Awesome 6 Brands', sans-serif !important;
        }

        /* --- PREMIUM MODERN ERP SIDEBAR BACKGROUNDS & ACTIVE INDICATORS --- */
        .sidebar-bg {
            background-color: #0c101c !important; /* Extremely elegant rich dark blue-slate */
            background-image: 
                linear-gradient(135deg, rgba(255, 255, 255, 0.007) 25%, transparent 25%), 
                linear-gradient(225deg, rgba(255, 255, 255, 0.007) 25%, transparent 25%), 
                linear-gradient(45deg, rgba(255, 255, 255, 0.007) 25%, transparent 25%), 
                linear-gradient(315deg, rgba(255, 255, 255, 0.007) 25%, #0c101c 25%) !important;
            background-position: 20px 0, 20px 0, 0 0, 0 0 !important;
            background-size: 40px 40px !important;
            background-repeat: repeat !important;
            border-right: 1px solid rgba(255, 255, 255, 0.03) !important;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.15) !important;
            padding: 0 !important;
        }
        .light-theme .sidebar-bg {
            background-color: #f6f8fb !important; /* Clean day mode light blue-grey */
            background-image: 
                linear-gradient(135deg, rgba(15, 23, 42, 0.006) 25%, transparent 25%), 
                linear-gradient(225deg, rgba(15, 23, 42, 0.006) 25%, transparent 25%), 
                linear-gradient(45deg, rgba(15, 23, 42, 0.006) 25%, transparent 25%), 
                linear-gradient(315deg, rgba(15, 23, 42, 0.006) 25%, #f6f8fb 25%) !important;
            background-position: 20px 0, 20px 0, 0 0, 0 0 !important;
            background-size: 40px 40px !important;
            background-repeat: repeat !important;
            border-right: 1px solid rgba(0, 0, 0, 0.05) !important;
            box-shadow: 2px 0 12px rgba(15, 23, 42, 0.02) !important;
        }



        /* Main Navigation Scrollbar Styling */
        #main-nav::-webkit-scrollbar {
            width: 4px;
        }
        #main-nav::-webkit-scrollbar-track {
            background: transparent;
        }
        #main-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 999px;
        }
        #main-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        .light-theme #main-nav::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.08);
        }
        .light-theme #main-nav::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.15);
        }

        /* --- CATEGORIES & HEADERS HIERARCHY --- */
        .sidebar-category-card {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin-top: 24px !important;
            margin-bottom: 8px !important;
        }
        .category-header-btn {
            background: transparent !important;
            border: none !important;
            padding: 6px 8px !important;
            font-size: 11px !important;
            font-weight: 800 !important;
            letter-spacing: 0.1em !important;
            color: #4b5563 !important; /* Elegant slate gray */
            text-transform: uppercase !important;
            display: flex;
            align-items: center;
            gap: 6px;
            width: 100%;
            border-bottom: 1px solid rgba(255, 255, 255, 0.03) !important;
            margin-bottom: 12px !important;
        }
        .light-theme .category-header-btn {
            color: #9ca3af !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.04) !important;
        }
        .sub-group-header {
            font-size: 11px !important;
            font-weight: 700 !important;
            color: #64748b !important;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            padding: 6px 12px !important;
            margin-top: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            background: transparent !important;
        }
        .light-theme .sub-group-header {
            color: #64748b !important;
        }

        /* --- MAIN NAVIGATION ROWS --- */
        .nav-item {
            font-size: 13px !important;
            font-weight: 500 !important;
            padding: 8px 12px !important;
            margin: 3px 0;
            border-radius: 8px !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            border-left: none !important;
            display: flex;
            align-items: center;
        }

        /* --- TREE SUB-MENU INDENTATION (Notion/Linear style) --- */
        .tree-container {
            margin-left: 18px;
            padding-left: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-top: 4px;
            margin-bottom: 8px;
        }
        .tree-sub-item {
            font-size: 12px !important;
            font-weight: 500 !important;
            padding: 6px 12px !important;
            border-radius: 6px !important;
            color: #64748b !important;
            border-left: none !important;
            background: transparent !important;
        }
        .light-theme .tree-sub-item {
            color: #475569 !important;
        }
        .tree-sub-item:hover {
            color: #f8fafc !important;
        }
        .light-theme .tree-sub-item:hover {
            color: #0f172a !important;
        }
        .tree-sub-item.active-nav-item {
            font-weight: 600 !important;
        }

        /* Prevent layout shift on hover/active */
        .nav-item {
            border: 1.2px solid transparent !important;
            border-radius: 12px !important;
        }

        /* --- SOPHISTICATED METALLIC/PASTEL NAVIGATION ICONS (NOT LOUD NEON) --- */
        .sidebar-bg .text-amber-500, .sidebar-bg .text-amber-600 { color: #d4af37 !important; } /* Matte gold */
        .sidebar-bg .text-emerald-500 { color: #608c76 !important; } /* Sage / Pine green */
        .sidebar-bg .text-sky-500 { color: #5d81a0 !important; } /* Muted slate blue */
        .sidebar-bg .text-cyan-500 { color: #5897a0 !important; } /* Muted cyan */
        .sidebar-bg .text-rose-500 { color: #bf6d7a !important; } /* Dusty rose */
        .sidebar-bg .text-teal-500 { color: #4b8a85 !important; } /* Soft pine teal */
        .sidebar-bg .text-violet-500 { color: #87749b !important; } /* Muted amethyst */
        .sidebar-bg .text-indigo-500 { color: #6c78b0 !important; } /* Slate indigo */
        .sidebar-bg .text-orange-400, .sidebar-bg .text-orange-500 { color: #cc745c !important; } /* Terracotta copper */
        .sidebar-bg .text-blue-400 { color: #5c7cb8 !important; } /* Soft steel blue */

        .light-theme .sidebar-bg .text-amber-500, .light-theme .sidebar-bg .text-amber-600 { color: #ad821a !important; }
        .light-theme .sidebar-bg .text-emerald-500 { color: #416b54 !important; }
        .light-theme .sidebar-bg .text-sky-500 { color: #436685 !important; }
        .light-theme .sidebar-bg .text-cyan-500 { color: #3b6d75 !important; }
        .light-theme .sidebar-bg .text-rose-500 { color: #9c4b58 !important; }
        .light-theme .sidebar-bg .text-teal-500 { color: #366c67 !important; }
        .light-theme .sidebar-bg .text-violet-500 { color: #625078 !important; }
        .light-theme .sidebar-bg .text-indigo-500 { color: #4e598f !important; }
        .light-theme .sidebar-bg .text-orange-400, .light-theme .sidebar-bg .text-orange-500 { color: #a6513a !important; }
        .light-theme .sidebar-bg .text-blue-400 { color: #3b5a94 !important; }

        /* --- 3D ELEGANT EMBOSSED TREE LINES ("RAJITAS") --- */
        .sidebar-bg .border-l {
            border-left: 1.5px solid rgba(255, 255, 255, 0.08) !important;
            box-shadow: -1.2px 0 0 rgba(0, 0, 0, 0.45) !important; /* Engraved indentation shadow */
            transition: border-color 0.3s ease;
        }
        .light-theme .sidebar-bg .border-l {
            border-left: 1.5px solid rgba(0, 0, 0, 0.05) !important;
            box-shadow: 1.2px 0 0 rgba(255, 255, 255, 0.95) !important; /* Embossed highlight reflection */
        }

        /* --- MAIN CONTENT AREA LIGHT/DARK THEME BACKGROUND FIX --- */
        main {
            background-color: rgba(9, 13, 24, 0.45) !important;
        }
        .light-theme main {
            background-color: #f1f5f9 !important; /* Eliminates muddy gray background in Light Theme! */
        }

        /* --- ELEGANT CUSTOM CHEVRON FOR SELECT DROPDOWNS & FILTER INPUTS --- */
        select.premium-filter-input {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
            background-position: right 12px center !important;
            background-repeat: no-repeat !important;
            background-size: 16px !important;
            padding-right: 36px !important;
            border-radius: 12px !important;
        }
        .light-theme select.premium-filter-input {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23475569' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e") !important;
            background-color: #ffffff !important;
            border: 1px solid rgba(15, 23, 42, 0.15) !important;
            color: #0f172a !important;
        }

        /* --- GLASS CARD HIGH CONTRAST IN LIGHT MODE --- */
        .light-theme .glass-card {
            background: #ffffff !important;
            border: 1px solid rgba(15, 23, 42, 0.08) !important;
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06), 0 2px 6px -1px rgba(15, 23, 42, 0.04) !important;
        }

        /* --- HIGH ELEGANCE TABLES IN LIGHT THEME --- */
        .light-theme table th {
            color: #475569 !important;
            background-color: #f8fafc !important;
            border-bottom: 1.5px solid #e2e8f0 !important;
        }
        .light-theme table td {
            color: #1e293b !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        .light-theme table tr:hover td {
            background-color: #f8fafc !important;
        }
        /* --- SIDEBAR USER PROFILE TYPOGRAPHY (DARK/LIGHT THEMES) --- */
        .sidebar-bg .user-name {
            color: #f8fafc !important; /* Crystal clear white text in Dark Mode */
        }
        .light-theme .sidebar-bg .user-name {
            color: #0f172a !important; /* Crisp slate dark text in Light Mode */
        }
        .sidebar-bg .user-role {
            color: #94a3b8 !important; /* Cool slate text in Dark Mode */
        }
        .light-theme .sidebar-bg .user-role {
            color: #64748b !important; /* Muted slate text in Light Mode */
        }
    </style>
</head>
<body class="h-full flex overflow-hidden bg-slate-950 relative">
    
    <!-- Canvas for Floating Glowing Gold/Fire Sparks -->
    <canvas id="particle-canvas" class="fixed inset-0 pointer-events-none z-0 opacity-40"></canvas>

    <!-- Sidebar (no-print) -->
    <div class="no-print hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 sidebar-bg z-20">
        <div class="flex flex-col h-full pt-5 pb-4">
            <!-- Logo area -->
            <div class="flex items-center flex-shrink-0 px-6 py-3 space-x-3 border-b border-slate-900/10 dark:border-slate-800/10 mb-4">
                <div class="flex items-center justify-center w-9 h-9 rounded-xl bg-slate-950/60 dark:bg-slate-900 border border-amber-500/30 dark:border-amber-500/20 shadow-[0_0_15px_rgba(245,158,11,0.15)] flex-shrink-0 relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                    <i class="fa-solid fa-gem text-amber-500 text-sm drop-shadow-[0_0_8px_rgba(245,158,11,0.6)]"></i>
                </div>
                <div>
                    <div class="flex items-center space-x-1">
                        <span class="text-xs font-black tracking-widest text-slate-800 dark:text-white uppercase">CONTROL</span>
                        <span class="text-xs font-black tracking-widest text-amber-500 uppercase">PAGOS</span>
                    </div>
                    <span class="text-[8px] font-extrabold text-slate-400 dark:text-slate-500 font-mono tracking-[0.2em] block -mt-0.5">MINERÍA</span>
                </div>
            </div>
            <!-- Navigation -->
            <nav class="flex-1 px-4 space-y-3 relative overflow-y-auto pr-1.5" 
                 id="main-nav"
                 x-data="{ 
                     openPagos: localStorage.getItem('sidebar_open_pagos') !== 'false',
                     openPersonal: localStorage.getItem('sidebar_open_personal') !== 'false',
                     openMovimientos: localStorage.getItem('sidebar_open_movimientos') !== 'false',
                     openAlmacen: localStorage.getItem('sidebar_open_almacen') !== 'false',
                     openSistema: localStorage.getItem('sidebar_open_sistema') !== 'false',
                     toggle(key) {
                         this[key] = !this[key];
                         localStorage.setItem('sidebar_' + key.replace('open', '').toLowerCase(), this[key]);
                     }
                 }"
                 x-init="
                     if ({{ request()->routeIs('bocaminas.*', 'trabajadores.*', 'fondos-caja.*', 'anticipos.*', 'pagos.*', 'servicios-externos.*', 'reportes.*') ? 'true' : 'false' }}) { openPagos = true; }
                     if ({{ request()->routeIs('bocaminas.*', 'trabajadores.*', 'tipos-trabajo.*') ? 'true' : 'false' }}) { openPersonal = true; }
                     if ({{ request()->routeIs('fondos-caja.*', 'anticipos.*', 'pagos.*', 'servicios-externos.*', 'reportes.*') ? 'true' : 'false' }}) { openMovimientos = true; }
                     if ({{ request()->routeIs('transacciones-minerales.*') ? 'true' : 'false' }}) { openAlmacen = true; }
                     if ({{ request()->routeIs('backups.*') ? 'true' : 'false' }}) { openSistema = true; }
                 ">
                <!-- 1. Tablero Principal -->
                <a href="{{ route('dashboard') }}" data-theme-color="amber" class="nav-item flex items-center px-3 py-2 text-xs font-bold rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'active-nav-item' : 'text-slate-450 hover:text-slate-200' }}">
                    <i class="fa-solid fa-chart-simple w-5 text-center mr-3 text-sm"></i>
                    Tablero Principal
                </a>

                <!-- 2. PAGOS Y ANTICIPOS (desplegable principal) -->
                <div class="space-y-1.5">
                    <button @click="toggle('openPagos')" 
                            class="w-full flex items-center justify-between px-3 py-2 text-xs font-black uppercase tracking-widest text-slate-450 dark:text-slate-450 hover:text-slate-200 focus:outline-none transition-colors duration-150">
                        <span class="flex items-center">
                            <i class="fa-solid fa-sack-dollar w-5 text-center mr-3 text-sm text-amber-500"></i>
                            PAGOS Y ANTICIPOS
                        </span>
                        <i class="fa-solid fa-chevron-down text-[8px] text-slate-500 transition-transform duration-200" :class="openPagos ? '' : '-rotate-90'"></i>
                    </button>

                    <div x-show="openPagos" class="pl-2 space-y-1.5 border-l border-slate-900/10 dark:border-slate-800/60 ml-5" x-collapse>
                        
                        <!-- 2.1 PERSONAL (sub-desplegable) -->
                        <div class="space-y-1">
                            <button @click="toggle('openPersonal')" 
                                    class="w-full flex items-center justify-between px-2 py-1.5 text-[11px] font-bold text-slate-350 dark:text-slate-400 hover:text-slate-200 focus:outline-none transition-colors duration-150">
                                <span class="flex items-center">
                                    <i class="fa-solid fa-users w-5 text-center mr-3 text-xs text-orange-400"></i>
                                    PERSONAL
                                </span>
                                <i class="fa-solid fa-chevron-down text-[8px] text-slate-500 transition-transform duration-200" :class="openPersonal ? '' : '-rotate-90'"></i>
                            </button>

                            <div x-show="openPersonal" class="pl-2 space-y-1 border-l border-slate-900/5 dark:border-slate-800/30 ml-4" x-collapse>
                                <a href="{{ route('bocaminas.index') }}" data-theme-color="emerald" class="nav-item flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('bocaminas.*') ? 'active-nav-item' : 'text-slate-450 hover:text-slate-200' }}">
                                    <i class="fa-solid fa-mountain-sun w-5 text-center mr-3 text-xs text-emerald-500"></i>
                                    Bocaminas
                                </a>
                                 <a href="{{ route('trabajadores.index') }}" data-theme-color="sky" class="nav-item flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('trabajadores.*') ? 'active-nav-item' : 'text-slate-450 hover:text-slate-200' }}">
                                    <i class="fa-solid fa-user-gear w-5 text-center mr-3 text-xs text-sky-500"></i>
                                    Personal y Contratos
                                </a>
                                <a href="{{ route('tipos-trabajo.index') }}" data-theme-color="amber" class="nav-item flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('tipos-trabajo.*') ? 'active-nav-item' : 'text-slate-450 hover:text-slate-200' }}">
                                    <i class="fa-solid fa-hammer w-5 text-center mr-3 text-xs text-amber-500"></i>
                                    Tipos de Trabajo
                                </a>
                            </div>
                        </div>

                        <!-- 2.2 MOVIMIENTOS (sub-desplegable) -->
                        <div class="space-y-1">
                            <button @click="toggle('openMovimientos')" 
                                    class="w-full flex items-center justify-between px-2 py-1.5 text-[11px] font-bold text-slate-350 dark:text-slate-400 hover:text-slate-200 focus:outline-none transition-colors duration-150">
                                <span class="flex items-center">
                                    <i class="fa-solid fa-money-bill-transfer w-5 text-center mr-3 text-xs text-blue-400"></i>
                                    MOVIMIENTOS
                                </span>
                                <i class="fa-solid fa-chevron-down text-[8px] text-slate-500 transition-transform duration-200" :class="openMovimientos ? '' : '-rotate-90'"></i>
                            </button>

                            <div x-show="openMovimientos" class="pl-2 space-y-1 border-l border-slate-900/5 dark:border-slate-800/30 ml-4" x-collapse>
                                <a href="{{ route('fondos-caja.index') }}" data-theme-color="cyan" class="nav-item flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('fondos-caja.*') ? 'active-nav-item' : 'text-slate-450 hover:text-slate-200' }}">
                                    <i class="fa-solid fa-cash-register w-5 text-center mr-3 text-xs text-cyan-500"></i>
                                    Caja del Personal
                                </a>
                                <a href="{{ route('anticipos.index') }}" data-theme-color="rose" class="nav-item flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('anticipos.*') ? 'active-nav-item' : 'text-slate-450 hover:text-slate-200' }}">
                                    <i class="fa-solid fa-hand-holding-dollar w-5 text-center mr-3 text-xs text-rose-500"></i>
                                    Anticipos
                                </a>
                                 <a href="{{ route('pagos.index') }}" data-theme-color="teal" class="nav-item flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('pagos.*') ? 'active-nav-item' : 'text-slate-450 hover:text-slate-200' }}">
                                    <i class="fa-solid fa-credit-card w-5 text-center mr-3 text-xs text-teal-500"></i>
                                    Pagos
                                </a>
                                <a href="{{ route('servicios-externos.index') }}" data-theme-color="sky" class="nav-item flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('servicios-externos.*') ? 'active-nav-item' : 'text-slate-450 hover:text-slate-200' }}">
                                    <i class="fa-solid fa-truck-front w-5 text-center mr-3 text-xs text-sky-500"></i>
                                    Servicios Externos
                                </a>
                                <a href="{{ route('reportes.index') }}" data-theme-color="violet" class="nav-item flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('reportes.*') ? 'active-nav-item' : 'text-slate-450 hover:text-slate-200' }}">
                                    <i class="fa-solid fa-chart-simple w-5 text-center mr-3 text-xs text-violet-500"></i>
                                    Reportes
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 3. ALMACÉN Y VENTAS (desplegable principal) -->
                <div class="space-y-1.5">
                    <button @click="toggle('openAlmacen')" 
                            class="w-full flex items-center justify-between px-3 py-2 text-xs font-black uppercase tracking-widest text-slate-450 dark:text-slate-450 hover:text-slate-200 focus:outline-none transition-colors duration-150">
                        <span class="flex items-center">
                            <i class="fa-solid fa-boxes-stacked w-5 text-center mr-3 text-sm text-amber-600 dark:text-amber-500"></i>
                            ALMACÉN Y VENTAS
                        </span>
                        <i class="fa-solid fa-chevron-down text-[8px] text-slate-500 transition-transform duration-200" :class="openAlmacen ? '' : '-rotate-90'"></i>
                    </button>

                    <div x-show="openAlmacen" class="pl-2 space-y-1.5 border-l border-slate-900/10 dark:border-slate-800/60 ml-5" x-collapse>
                        <a href="{{ route('transacciones-minerales.index', ['tab' => 'caja']) }}" data-theme-color="amber" class="nav-item flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg relative z-10 transition-colors duration-200 {{ (request()->routeIs('transacciones-minerales.*') && request('tab') === 'caja') ? 'active-nav-item' : 'text-slate-450 hover:text-slate-200' }}">
                            <i class="fa-solid fa-vault w-5 text-center mr-3 text-xs text-amber-400"></i>
                            Caja del Módulo
                        </a>
                        <a href="{{ route('transacciones-minerales.index', ['tab' => 'compras']) }}" data-theme-color="amber" class="nav-item flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg relative z-10 transition-colors duration-200 {{ (request()->routeIs('transacciones-minerales.*') && request('tab', 'compras') === 'compras') ? 'active-nav-item' : 'text-slate-450 hover:text-slate-200' }}">
                            <i class="fa-solid fa-circle-arrow-down w-5 text-center mr-3 text-xs text-amber-500"></i>
                            Compras
                        </a>
                        <a href="{{ route('transacciones-minerales.index', ['tab' => 'ventas']) }}" data-theme-color="emerald" class="nav-item flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg relative z-10 transition-colors duration-200 {{ (request()->routeIs('transacciones-minerales.*') && request('tab') === 'ventas') ? 'active-nav-item' : 'text-slate-450 hover:text-slate-200' }}">
                            <i class="fa-solid fa-circle-arrow-up w-5 text-center mr-3 text-xs text-emerald-500"></i>
                            Ventas
                        </a>
                        <a href="{{ route('transacciones-minerales.index', ['tab' => 'stock']) }}" data-theme-color="cyan" class="nav-item flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg relative z-10 transition-colors duration-200 {{ (request()->routeIs('transacciones-minerales.*') && request('tab') === 'stock') ? 'active-nav-item' : 'text-slate-450 hover:text-slate-200' }}">
                            <i class="fa-solid fa-warehouse w-5 text-center mr-3 text-xs text-cyan-500"></i>
                            Stock
                        </a>
                        <a href="{{ route('transacciones-minerales.index', ['tab' => 'reportes']) }}" data-theme-color="indigo" class="nav-item flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg relative z-10 transition-colors duration-200 {{ (request()->routeIs('transacciones-minerales.*') && request('tab') === 'reportes') ? 'active-nav-item' : 'text-slate-450 hover:text-slate-200' }}">
                            <i class="fa-solid fa-chart-line w-5 text-center mr-3 text-xs text-indigo-500"></i>
                            Reportes
                        </a>
                    </div>
                </div>

                <!-- 4. SISTEMA (desplegable principal) -->
                <div class="space-y-1.5">
                    <button @click="toggle('openSistema')" 
                            class="w-full flex items-center justify-between px-3 py-2 text-xs font-black uppercase tracking-widest text-slate-450 dark:text-slate-450 hover:text-slate-200 focus:outline-none transition-colors duration-150">
                        <span class="flex items-center">
                            <i class="fa-solid fa-sliders w-5 text-center mr-3 text-sm text-slate-500"></i>
                            SISTEMA
                        </span>
                        <i class="fa-solid fa-chevron-down text-[8px] text-slate-500 transition-transform duration-200" :class="openSistema ? '' : '-rotate-90'"></i>
                    </button>

                    <div x-show="openSistema" class="pl-2 space-y-1.5 border-l border-slate-900/10 dark:border-slate-800/60 ml-5" x-collapse>
                        <a href="{{ route('backups.index') }}" data-theme-color="orange" class="nav-item flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg relative z-10 transition-colors duration-200 {{ request()->routeIs('backups.*') ? 'active-nav-item' : 'text-slate-450 hover:text-slate-200' }}">
                            <i class="fa-solid fa-server w-5 text-center mr-3 text-xs text-orange-500"></i>
                            Respaldos
                        </a>
                    </div>
                </div>
            </nav>
            
            <!-- User Section / Footer Card -->
            <div class="mt-auto pt-4 px-4 border-t border-slate-900/60 dark:border-slate-800/40">
                <div class="p-3 rounded-2xl bg-slate-900/40 dark:bg-slate-900/60 border border-slate-800/60 shadow-inner flex flex-col space-y-3">
                    <a href="{{ route('profile.edit') }}" class="flex items-center space-x-3 hover:bg-slate-800/50 p-2 rounded-xl transition duration-150 group" title="Ver mi Perfil">
                        <div class="relative w-10 h-10 flex-shrink-0">
                            @if(Auth::user() && Auth::user()->avatar)
                                <div class="w-10 h-10 rounded-xl bg-slate-900 border border-amber-500/30 overflow-hidden shadow-[0_0_12px_rgba(245,158,11,0.15)] flex items-center justify-center">
                                    <img src="{{ asset(Auth::user()->avatar) }}" class="w-full h-full object-cover">
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500/20 to-orange-600/10 border border-amber-500/30 flex items-center justify-center shadow-[0_0_12px_rgba(245,158,11,0.15)]">
                                    <i class="fa-solid fa-user-tie text-amber-500 text-sm"></i>
                                </div>
                            @endif
                            <span class="block h-3.5 w-3.5 rounded-full bg-emerald-500 ring-2 ring-slate-900 shadow-sm" style="position: absolute; bottom: 0px; right: 0px; left: auto; top: auto; z-index: 20;"></span>
                        </div>
                        <div class="flex-1 min-w-0 pl-2">
                            <p class="user-name text-sm font-extrabold leading-tight truncate">
                                {{ Auth::user()->name ?? 'Administrador' }}
                            </p>
                            <p class="user-role text-[9px] font-black uppercase tracking-wider mt-0.5">
                                ADMINISTRADOR
                            </p>
                        </div>
                    </a>
                    
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center space-x-2 py-2.5 px-4 rounded-xl font-extrabold text-xs uppercase tracking-wider transition-all duration-200 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-300 border border-rose-500/25 hover:border-rose-500/50 shadow-[0_0_12px_rgba(244,63,94,0.1)] active:scale-95">
                            <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                            <span>Salir</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Header & Navigation (no-print) -->
    <div x-data="{ open: false }" class="no-print md:hidden fixed top-0 w-full bg-slate-900 border-b border-slate-800 z-30">
        <div class="flex items-center justify-between h-16 px-4">
            <div class="flex items-center space-x-3">
                <div class="flex items-center justify-center w-8 h-8 rounded bg-slate-950/60 border border-amber-500/30 shadow-[0_0_10px_rgba(245,158,11,0.15)]">
                    <i class="fa-solid fa-gem text-amber-500 text-xs"></i>
                </div>
                <h1 class="text-md font-bold tracking-wider text-slate-100 uppercase">SCP <span class="text-amber-500/90 font-extrabold">Minero</span></h1>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="toggleTheme()" class="p-2 rounded text-amber-500 hover:text-amber-400 focus:outline-none" title="Cambiar Modo (Día/Noche)">
                    <i id="theme-toggle-icon-mobile" class="fa-solid fa-sun text-lg"></i>
                </button>
                <button @click="open = !open" class="text-slate-400 hover:text-slate-200 focus:outline-none">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile menu list -->
        <div x-show="open" @click.away="open = false" class="px-2 pt-2 pb-4 space-y-1 bg-slate-900 border-b border-slate-800">
            <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-amber-500/10 text-amber-500' : 'text-slate-300 hover:bg-slate-800' }}">Tablero Principal</a>
            
            <div class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 font-mono">Pagos y Anticipos al Personal</div>
            <div class="pl-2 border-l border-slate-800 ml-2 space-y-1">
                <div class="px-3 py-1 text-xs font-semibold text-slate-400 uppercase tracking-wider font-mono">👷 Personal</div>
                <a href="{{ route('bocaminas.index') }}" class="block px-6 py-1.5 rounded-md text-sm font-medium {{ request()->routeIs('bocaminas.*') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-450 hover:bg-slate-800' }}">Bocaminas</a>
                <a href="{{ route('trabajadores.index') }}" class="block px-6 py-1.5 rounded-md text-sm font-medium {{ request()->routeIs('trabajadores.*') ? 'bg-sky-500/10 text-sky-400' : 'text-slate-450 hover:bg-slate-800' }}">Personal y Contratos</a>
                
                <div class="px-3 py-1 text-xs font-semibold text-slate-400 uppercase tracking-wider font-mono mt-2">💰 Movimientos</div>
                <a href="{{ route('fondos-caja.index') }}" class="block px-6 py-1.5 rounded-md text-sm font-medium {{ request()->routeIs('fondos-caja.*') ? 'bg-cyan-500/10 text-cyan-400' : 'text-slate-450 hover:bg-slate-800' }}">Caja del Personal</a>
                <a href="{{ route('anticipos.index') }}" class="block px-6 py-1.5 rounded-md text-sm font-medium {{ request()->routeIs('anticipos.*') ? 'bg-rose-500/10 text-rose-455' : 'text-slate-450 hover:bg-slate-800' }}">Anticipos</a>
                <a href="{{ route('pagos.index') }}" class="block px-6 py-1.5 rounded-md text-sm font-medium {{ request()->routeIs('pagos.*') ? 'bg-teal-500/10 text-teal-400' : 'text-slate-450 hover:bg-slate-800' }}">Pagos</a>
                
                <a href="{{ route('reportes.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium mt-2 {{ request()->routeIs('reportes.*') ? 'bg-violet-500/10 text-violet-400' : 'text-slate-350 hover:bg-slate-800' }}">📊 Reportes</a>
            </div>
            
            <div class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 font-mono">📦 Compra y Venta de Minerales</div>
            <a href="{{ route('transacciones-minerales.index', ['tab' => 'caja']) }}" class="block px-6 py-1.5 rounded-md text-sm font-medium {{ (request()->routeIs('transacciones-minerales.*') && request('tab') === 'caja') ? 'bg-amber-500/10 text-amber-400' : 'text-slate-350 hover:bg-slate-800' }}">Caja del Módulo</a>
            <a href="{{ route('transacciones-minerales.index', ['tab' => 'compras']) }}" class="block px-6 py-1.5 rounded-md text-sm font-medium {{ (request()->routeIs('transacciones-minerales.*') && request('tab', 'compras') === 'compras') ? 'bg-amber-500/10 text-amber-500' : 'text-slate-350 hover:bg-slate-800' }}">Compras</a>
            <a href="{{ route('transacciones-minerales.index', ['tab' => 'ventas']) }}" class="block px-6 py-1.5 rounded-md text-sm font-medium {{ (request()->routeIs('transacciones-minerales.*') && request('tab') === 'ventas') ? 'bg-emerald-500/10 text-emerald-400' : 'text-slate-350 hover:bg-slate-800' }}">Ventas</a>
            <a href="{{ route('transacciones-minerales.index', ['tab' => 'stock']) }}" class="block px-6 py-1.5 rounded-md text-sm font-medium {{ (request()->routeIs('transacciones-minerales.*') && request('tab') === 'stock') ? 'bg-cyan-500/10 text-cyan-400' : 'text-slate-350 hover:bg-slate-800' }}">Stock</a>
            <a href="{{ route('transacciones-minerales.index', ['tab' => 'reportes']) }}" class="block px-6 py-1.5 rounded-md text-sm font-medium {{ (request()->routeIs('transacciones-minerales.*') && request('tab') === 'reportes') ? 'bg-indigo-500/10 text-indigo-400' : 'text-slate-350 hover:bg-slate-800' }}">Reportes</a>
            
            <div class="px-3 pt-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-500 font-mono">⚙️ Sistema</div>
            <a href="{{ route('backups.index') }}" class="block px-6 py-1.5 rounded-md text-sm font-medium {{ request()->routeIs('backups.*') ? 'bg-orange-500/10 text-orange-400' : 'text-slate-350 hover:bg-slate-800' }}">Respaldos</a>

            <form action="{{ route('logout') }}" method="POST" class="block w-full">
                @csrf
                <button type="submit" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-400 hover:bg-slate-800">Cerrar Sesión</button>
            </form>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="flex flex-col flex-1 w-full md:pl-64 overflow-hidden">
        <!-- Top bar (only for desktop, no-print) -->
        <header class="no-print hidden md:flex items-center justify-between h-16 header-bg px-8 flex-shrink-0 relative z-10 border-b border-slate-900/10 dark:border-slate-800/40">
            <!-- Left Side: System Quick Status Badge -->
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-slate-900/40 dark:bg-slate-800/40 text-slate-400 border border-slate-800/50">
                    <i class="fa-solid fa-microchip text-amber-500 mr-2 text-xs"></i> SCPM &middot; v2.5
                </span>
            </div>

            <!-- Right Side: Theme Toggle, Connection Status & Real-time Clock -->
            <div class="flex items-center space-x-3">
                <!-- Theme Toggle Capsule Button -->
                <button onclick="toggleTheme()" 
                        class="w-9 h-9 rounded-xl bg-slate-900/60 dark:bg-slate-900/80 hover:bg-slate-800 border border-amber-500/25 hover:border-amber-500/50 text-amber-500 hover:text-amber-400 transition-all duration-200 flex items-center justify-center shadow-[0_0_12px_rgba(245,158,11,0.1)] active:scale-95 no-print" 
                        title="Cambiar Modo (Día/Noche)">
                    <i id="theme-toggle-icon" class="fa-solid fa-sun text-sm"></i>
                </button>

                <div class="h-4 w-px bg-slate-800/60 dark:bg-slate-800/80"></div>

                <!-- Server Status Badge -->
                <div class="inline-flex items-center px-3 py-1 rounded-xl bg-emerald-500/10 border border-emerald-500/25 text-emerald-400 text-xs font-semibold shadow-[0_0_10px_rgba(16,185,129,0.08)]">
                    <span class="relative flex h-2 w-2 mr-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span>Servidor Local Conectado</span>
                </div>

                <div class="h-4 w-px bg-slate-800/60 dark:bg-slate-800/80"></div>

                <!-- Realtime Date & Clock Widget -->
                <div class="inline-flex items-center px-3 py-1 rounded-xl bg-slate-900/40 dark:bg-slate-900/60 border border-slate-800/60 text-xs font-medium text-slate-300 space-x-2">
                    <i class="fa-regular fa-calendar-days text-amber-500 text-xs"></i>
                    <span id="realtime-clock" class="font-mono text-slate-200 flex items-center"></span>
                </div>
            </div>
        </header>

        <!-- Main Body -->
        <main class="flex-1 relative overflow-y-auto focus:outline-none p-4 md:p-8 pt-20 md:pt-8 bg-slate-950/40 z-30">
            
            <!-- Floating Toast Notifications (no-print) -->
            <div id="toast-container" class="no-print fixed top-6 right-6 z-50 flex flex-col space-y-4 max-w-sm w-full">
                @if(session('success'))
                    <div class="toast-item toast-success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false }, 4500)">
                        <div class="flex items-start p-4">
                            <div class="flex-shrink-0 text-emerald-400">
                                <i class="fa-solid fa-circle-check text-xl animate-bounce"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-450">Operación Exitosa</p>
                                <p class="text-sm text-slate-100 font-semibold mt-1">{{ session('success') }}</p>
                            </div>
                            <button @click="show = false" class="ml-4 text-slate-500 hover:text-slate-300 transition duration-150">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div class="toast-progress bg-emerald-500"></div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="toast-item toast-danger" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { show = false }, 5500)">
                        <div class="flex items-start p-4">
                            <div class="flex-shrink-0 text-rose-500">
                                <i class="fa-solid fa-circle-exclamation text-xl animate-pulse"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-[10px] font-bold uppercase tracking-wider text-slate-450">Error en Operación</p>
                                <p class="text-sm text-slate-100 font-semibold mt-1">{{ $errors->first() }}</p>
                            </div>
                            <button @click="show = false" class="ml-4 text-slate-500 hover:text-slate-300 transition duration-150">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        <div class="toast-progress bg-rose-500"></div>
                    </div>
                @endif
            </div>

            @yield('content')
            
        </main>
    </div>

    <!-- Real-time Spanish Clock -->
    <script>
        function updateClock() {
            const now = new Date();
            const days = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
            const months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
            
            const dayName = days[now.getDay()];
            const day = now.getDate();
            const monthName = months[now.getMonth()];
            const year = now.getFullYear();
            
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const dateStr = `${dayName.charAt(0).toUpperCase() + dayName.slice(1)}, ${day} de ${monthName} de ${year}`;
            const timeStr = `${hours}:${minutes}:${seconds}`;
            
            const clockEl = document.getElementById('realtime-clock');
            if (clockEl) {
                clockEl.innerHTML = `<span>${dateStr}</span><span class="text-amber-500 font-mono font-bold ml-2 bg-amber-500/10 px-2 py-0.5 rounded-lg border border-amber-500/20 text-xs shadow-[0_0_10px_rgba(245,158,11,0.15)]">${timeStr}</span>`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();

        // --- GLOBAL PARTICLE CANVAS FOR MINING SPARKS ---
        const canvas = document.getElementById('particle-canvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            let width = canvas.width = window.innerWidth;
            let height = canvas.height = window.innerHeight;
            
            let mouse = { x: -1000, y: -1000, active: false };
            
            window.addEventListener('resize', () => {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            });
            
            window.addEventListener('mousemove', (e) => {
                mouse.x = e.clientX;
                mouse.y = e.clientY;
                mouse.active = true;
            });
            
            window.addEventListener('mouseleave', () => {
                mouse.active = false;
            });
            
            const particles = [];
            const maxParticles = 55; // Balanced for dashboard viewability
            
            class Spark {
                constructor() {
                    this.reset(true);
                }
                
                reset(initial = false) {
                    this.x = Math.random() * width;
                    this.y = initial ? Math.random() * height : height + 15;
                    this.size = Math.random() * 2.2 + 0.8;
                    this.speedY = Math.random() * 1.3 + 0.5;
                    this.speedX = (Math.random() - 0.5) * 0.5;
                    this.life = Math.random() * 0.7 + 0.3;
                    this.decay = Math.random() * 0.0025 + 0.0015;
                    this.opacity = Math.random() * 0.75 + 0.15;
                    this.wiggleFreq = Math.random() * 0.01 + 0.003;
                    this.wiggleAmp = Math.random() * 1.5 + 0.2;
                }
                
                update() {
                    this.y -= this.speedY;
                    this.life -= this.decay;
                    this.x += Math.sin(this.y * this.wiggleFreq) * 0.15 * this.wiggleAmp;
                    
                    if (mouse.active) {
                        const dx = this.x - mouse.x;
                        const dy = this.y - mouse.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < 120) {
                            const force = (120 - dist) / 120;
                            this.x += (dx / dist) * force * 3;
                            this.y += (dy / dist) * force * 1;
                        }
                    }
                    
                    if (this.life <= 0 || this.x < 0 || this.x > width || this.y < -15) {
                        this.reset();
                    }
                }
                
                draw() {
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    
                    let r, g, b;
                    if (this.life > 0.7) {
                        r = 254; g = 240; b = 138; // yellow
                    } else if (this.life > 0.4) {
                        r = 249; g = 115; b = 22;  // orange
                    } else if (this.life > 0.18) {
                        r = 239; g = 68; b = 68;   // red
                    } else {
                        r = 100; g = 116; b = 139;  // gray ash
                    }
                    
                    ctx.fillStyle = `rgba(${r}, ${g}, ${b}, ${this.life * this.opacity})`;
                    ctx.shadowBlur = this.life > 0.55 ? this.size * 3.0 : 0;
                    ctx.shadowColor = 'rgb(249, 115, 22)';
                    ctx.fill();
                    ctx.shadowBlur = 0;
                }
            }
            
            for (let i = 0; i < maxParticles; i++) {
                particles.push(new Spark());
            }
            
            function animate() {
                ctx.clearRect(0, 0, width, height);
                for (let i = 0; i < particles.length; i++) {
                    particles[i].update();
                    particles[i].draw();
                }
                requestAnimationFrame(animate);
            }
            animate();
        }

        // --- GLOBAL BUTTON SPARK GENERATOR ON CLICK/HOVER ---
        document.addEventListener('DOMContentLoaded', () => {
            // 1. Dynamic styles injection for card animations
            const animStyle = document.createElement("style");
            animStyle.innerText = `
                @keyframes slideUpFade {
                    from { opacity: 0; transform: translateY(18px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            `;
            document.head.appendChild(animStyle);

            // Stagger page card loads
            const cards = document.querySelectorAll('.glass-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.animation = 'slideUpFade 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards';
                card.style.animationDelay = `${index * 65}ms`;
            });



            // 3. Global buttons interaction (including nav items for premium sparks hover)
            const buttons = document.querySelectorAll('button, .btn, [type="submit"], [type="button"], a.btn, a.inline-flex, .nav-item');
            buttons.forEach(btn => {
                if (btn.id && btn.id.includes('eye')) return;
                
                const style = window.getComputedStyle(btn);
                if (style.position === 'static') {
                    btn.style.position = 'relative';
                }
                if (btn.classList.contains('nav-item')) {
                    btn.style.overflow = 'visible';
                } else if (style.overflow !== 'hidden') {
                    btn.style.overflow = 'hidden';
                }
                
                let hoverInterval;
                btn.addEventListener('mouseenter', (e) => {
                    spawnGlobalSparks(btn, 3);
                    hoverInterval = setInterval(() => spawnGlobalSparks(btn, 1), 300);
                });
                btn.addEventListener('mouseleave', () => {
                    clearInterval(hoverInterval);
                });
                
                btn.addEventListener('click', () => {
                    spawnGlobalSparks(btn, 10);
                });
            });
        });

        // 4. Form submit glass processing overlay & Global confirm modal handler
        function showProcessingOverlay(form) {
            if (form.action && form.action.includes('logout')) return;
            if (form.checkValidity && !form.checkValidity()) return;
            
            const card = form.closest('.glass-card') || form.closest('main') || document.body;
            
            // Prevent duplicate overlays
            if (card.querySelector('.processing-overlay')) return;
            
            const overlay = document.createElement('div');
            overlay.className = 'processing-overlay';
            overlay.style.position = 'absolute';
            overlay.style.inset = '0';
            overlay.style.background = 'rgba(10, 12, 18, 0.84)';
            overlay.style.backdropFilter = 'blur(10px)';
            overlay.style.webkitBackdropFilter = 'blur(10px)';
            overlay.style.borderRadius = '16px';
            overlay.style.zIndex = '50';
            overlay.style.display = 'flex';
            overlay.style.flexDirection = 'column';
            overlay.style.alignItems = 'center';
            overlay.style.justifyContent = 'center';
            overlay.style.opacity = '0';
            overlay.style.transition = 'opacity 0.25s ease';
            
            overlay.innerHTML = `
                <div style="position: relative; display: flex; align-items: center; justify-content: center;">
                    <div class="w-14 h-14 rounded-full border-2 border-amber-500/20 border-t-amber-500 animate-spin"></div>
                    <i class="fa-solid fa-gem text-amber-500 absolute text-base animate-pulse"></i>
                </div>
                <span class="text-xs text-amber-500 font-mono tracking-widest uppercase mt-4 animate-pulse">Procesando...</span>
            `;
            
            card.appendChild(overlay);
            overlay.offsetHeight; // force reflow
            overlay.style.opacity = '1';

            // Safety cleanup if form submission is interrupted
            setTimeout(() => {
                if (overlay && overlay.parentNode) {
                    overlay.style.opacity = '0';
                    setTimeout(() => overlay.remove(), 250);
                }
            }, 6000);
        }

        // Global Custom Confirmation Modal
        function showCustomConfirmModal(message, reportUrl, onConfirm) {
            const existingModal = document.getElementById('custom-confirm-modal');
            if (existingModal) existingModal.remove();
            
            const modalContainer = document.createElement('div');
            modalContainer.id = 'custom-confirm-modal';
            modalContainer.className = 'fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md';
            modalContainer.style.opacity = '0';
            modalContainer.style.transition = 'opacity 0.25s cubic-bezier(0.16, 1, 0.3, 1)';
            
            let reportButtonHtml = '';
            if (reportUrl) {
                reportButtonHtml = `
                    <a href="${reportUrl}" target="_blank" id="confirm-report-btn" class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 text-slate-950 shadow-lg shadow-amber-500/10 hover:from-amber-600 hover:to-orange-600 transition-all duration-150 inline-flex items-center">
                        <i class="fa-solid fa-file-pdf mr-1.5 text-sm"></i> Generar Reporte
                    </a>
                `;
            }
            
            const modalContentHtml = `
                <div class="glass-card w-full max-w-md rounded-2xl overflow-hidden shadow-2xl border border-red-500/35 relative transform scale-90 transition-all duration-300 ease-out" style="background: rgba(15, 23, 42, 0.92);">
                    <div style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, #ef4444, #f59e0b, #ef4444); opacity: 0.9;"></div>
                    
                    <div class="p-6 space-y-5 text-center font-sans">
                        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-500/10 border border-red-500/30 text-red-500 animate-pulse mt-2">
                            <i class="fa-solid fa-trash-can text-2xl animate-bounce" style="animation-duration: 2s;"></i>
                        </div>
                        
                        <div class="space-y-3">
                            <h3 class="text-lg font-bold tracking-wider text-red-500 uppercase font-mono">Advertencia de Eliminación</h3>
                            <p class="text-sm text-slate-200 font-semibold px-2 leading-relaxed">${message}</p>
                            <p class="text-xs text-slate-400 px-3 leading-relaxed">Se recomienda generar o revisar un reporte de este historial antes de eliminar el registro de forma permanente.</p>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 border-t border-slate-800/80 bg-slate-900/40 flex flex-wrap justify-center gap-3">
                        <button type="button" id="confirm-cancel-btn" class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700/60 hover:border-slate-650 transition-all duration-200">
                            Cancelar
                        </button>
                        
                        ${reportButtonHtml}
                        
                        <button type="button" id="confirm-ok-btn" class="px-4 py-2.5 text-xs font-bold uppercase tracking-wider rounded-lg bg-gradient-to-r from-red-650 to-rose-500 text-white shadow-lg shadow-red-500/20 hover:from-red-550 hover:to-rose-450 transition-all duration-300">
                            Eliminar de todas formas
                        </button>
                    </div>
                </div>
            `;
            
            modalContainer.innerHTML = modalContentHtml;
            document.body.appendChild(modalContainer);
            
            const card = modalContainer.querySelector('.glass-card');
            
            setTimeout(() => {
                modalContainer.style.opacity = '1';
                card.style.transform = 'scale(1)';
            }, 10);
            
            const closeModal = () => {
                modalContainer.classList.add('modal-hide');
                setTimeout(() => {
                    modalContainer.remove();
                }, 250);
            };
            
            const cancelBtn = modalContainer.querySelector('#confirm-cancel-btn');
            const okBtn = modalContainer.querySelector('#confirm-ok-btn');
            
            if (typeof spawnGlobalSparks === 'function') {
                cancelBtn.addEventListener('mouseenter', () => spawnGlobalSparks(cancelBtn, 2));
                cancelBtn.addEventListener('click', () => spawnGlobalSparks(cancelBtn, 6));
                okBtn.addEventListener('mouseenter', () => spawnGlobalSparks(okBtn, 3));
                okBtn.addEventListener('click', () => spawnGlobalSparks(okBtn, 12));
            }
            
            cancelBtn.addEventListener('click', closeModal);
            okBtn.addEventListener('click', () => {
                closeModal();
                if (onConfirm) onConfirm();
            });
            
            modalContainer.addEventListener('click', (e) => {
                if (e.target === modalContainer) {
                    closeModal();
                }
            });
        }

        // Intercept click events in the capturing phase to prevent inline confirm() browser alerts
        document.addEventListener('click', (e) => {
            const button = e.target.closest('button[type="submit"]');
            if (!button) return;
            
            const form = button.closest('form');
            if (!form) return;
            
            const onsubmitAttr = form.getAttribute('onsubmit');
            if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                // Prevent the submission before the browser executes onsubmit!
                e.preventDefault();
                e.stopImmediatePropagation();
                
                // Extract message
                const match = onsubmitAttr.match(/confirm\(\s*['"](.*?)['"]\s*\)/);
                const message = match ? match[1] : '¿Estás seguro de continuar?';
                
                // Extract report URL from form action route dynamically
                let reportUrl = '/reportes';
                if (form.action) {
                    if (form.action.includes('/trabajadores/')) {
                        const idMatch = form.action.match(/\/trabajadores\/(\d+)/);
                        if (idMatch) reportUrl = `/reportes?tab=trabajador&trabajador_id=${idMatch[1]}`;
                    } else if (form.action.includes('/bocaminas/')) {
                        const idMatch = form.action.match(/\/bocaminas\/(\d+)/);
                        if (idMatch) reportUrl = `/reportes?tab=bocamina&bocamina_id=${idMatch[1]}`;
                    } else if (form.action.includes('/contratos/')) {
                        reportUrl = '/reportes?tab=general';
                    } else if (form.action.includes('/anticipos/')) {
                        reportUrl = '/reportes?tab=anticipos';
                    } else if (form.action.includes('/trabajos/')) {
                        reportUrl = '/reportes?tab=general';
                    }
                }
                
                showCustomConfirmModal(message, reportUrl, () => {
                    showProcessingOverlay(form);
                    form.removeAttribute('onsubmit');
                    form.submit();
                });
            }
        }, true);

        // Intercept all submit events in the capturing phase
        document.addEventListener('submit', (e) => {
            const form = e.target;
            
            // Check if this form uses confirm() inline
            const onsubmitAttr = form.getAttribute('onsubmit');
            if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                // Prevent form execution immediately
                e.preventDefault();
                e.stopImmediatePropagation();
                
                // Extract message
                const match = onsubmitAttr.match(/confirm\(\s*['"](.*?)['"]\s*\)/);
                const message = match ? match[1] : '¿Estás seguro de continuar?';
                
                let reportUrl = '/reportes';
                if (form.action) {
                    if (form.action.includes('/trabajadores/')) {
                        const idMatch = form.action.match(/\/trabajadores\/(\d+)/);
                        if (idMatch) reportUrl = `/reportes?tab=trabajador&trabajador_id=${idMatch[1]}`;
                    } else if (form.action.includes('/bocaminas/')) {
                        const idMatch = form.action.match(/\/bocaminas\/(\d+)/);
                        if (idMatch) reportUrl = `/reportes?tab=bocamina&bocamina_id=${idMatch[1]}`;
                    } else if (form.action.includes('/contratos/')) {
                        reportUrl = '/reportes?tab=general';
                    } else if (form.action.includes('/anticipos/')) {
                        reportUrl = '/reportes?tab=anticipos';
                    } else if (form.action.includes('/trabajos/')) {
                        reportUrl = '/reportes?tab=general';
                    }
                }

                showCustomConfirmModal(message, reportUrl, () => {
                    showProcessingOverlay(form);
                    form.removeAttribute('onsubmit');
                    form.submit();
                });
            } else {
                // Standard submission, just show overlay
                showProcessingOverlay(form);
            }
        }, true);

        function spawnGlobalSparks(element, count) {
            const rect = element.getBoundingClientRect();
            
            let colorGrad = 'radial-gradient(circle, #fcd34d 0%, #f97316 60%, #ef4444 100%)';
            let shadow = '#f59e0b';
            
            if (element.classList.contains('bg-red-600') || 
                element.classList.contains('bg-red-500') || 
                element.className.includes('bg-red-') || 
                element.textContent.toLowerCase().includes('eliminar') || 
                element.textContent.toLowerCase().includes('borrar')) {
                colorGrad = 'radial-gradient(circle, #fecaca 0%, #ef4444 60%, #b91c1c 100%)';
                shadow = '#ef4444';
            } 
            else if (element.classList.contains('bg-emerald-600') || 
                     element.classList.contains('bg-emerald-500') || 
                     element.className.includes('bg-emerald-') || 
                     element.textContent.toLowerCase().includes('guardar') || 
                     element.textContent.toLowerCase().includes('crear') || 
                     element.textContent.toLowerCase().includes('procesar') ||
                     element.textContent.toLowerCase().includes('éxito')) {
                colorGrad = 'radial-gradient(circle, #a7f3d0 0%, #10b981 60%, #047857 100%)';
                shadow = '#10b981';
            }
            
            for (let i = 0; i < count; i++) {
                const spark = document.createElement('span');
                spark.className = 'global-button-spark';
                const size = Math.random() * 3.0 + 1.6;
                spark.style.width = `${size}px`;
                spark.style.height = `${size}px`;
                spark.style.background = colorGrad;
                spark.style.boxShadow = `0 0 5px ${shadow}`;
                
                const x = Math.random() * rect.width;
                const y = rect.height - Math.random() * 3;
                spark.style.left = `${x}px`;
                spark.style.top = `${y}px`;
                
                const vx = (Math.random() - 0.5) * 45;
                const vy = -(Math.random() * 45 + 25);
                
                element.appendChild(spark);
                
                spark.animate([
                    { transform: 'translate(0, 0) scale(1)', opacity: 1 },
                    { transform: `translate(${vx}px, ${vy}px) scale(0)`, opacity: 0 }
                ], {
                    duration: Math.random() * 500 + 400,
                    easing: 'cubic-bezier(0.1, 0.8, 0.3, 1)'
                }).onfinish = () => spark.remove();
            }
        }

        // --- DYNAMIC DAY/NIGHT MODE TOGGLER ---
        function toggleTheme() {
            const html = document.documentElement;
            const icons = [document.getElementById('theme-toggle-icon'), document.getElementById('theme-toggle-icon-mobile')];
            if (html.classList.contains('light-theme')) {
                html.classList.remove('light-theme');
                localStorage.setItem('theme', 'dark');
                icons.forEach(icon => {
                    if (icon) {
                        icon.classList.remove('fa-moon');
                        icon.classList.add('fa-sun');
                    }
                });
            } else {
                html.classList.add('light-theme');
                localStorage.setItem('theme', 'light');
                icons.forEach(icon => {
                    if (icon) {
                        icon.classList.remove('fa-sun');
                        icon.classList.add('fa-moon');
                    }
                });
            }
            
            // Dispatch a global event so views (like dashboard charts) can adapt dynamically
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: localStorage.getItem('theme') } }));
        }

        document.addEventListener('DOMContentLoaded', () => {
            const html = document.documentElement;
            const icons = [document.getElementById('theme-toggle-icon'), document.getElementById('theme-toggle-icon-mobile')];
            icons.forEach(icon => {
                if (icon) {
                    if (html.classList.contains('light-theme')) {
                        icon.classList.remove('fa-sun');
                        icon.classList.add('fa-moon');
                    } else {
                        icon.classList.remove('fa-moon');
                        icon.classList.add('fa-sun');
                    }
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
