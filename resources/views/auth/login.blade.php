<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950 text-slate-100">
<head>
    <script>
        // Force dark theme on login page
        document.documentElement.classList.remove('light-theme');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - KOYOSITA S.R.L.</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo_koyosita.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="/app.ico">
    <link rel="manifest" href="/manifest.webmanifest">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS (Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            perspective: 1000px;
        }

        /* Full-screen Ken Burns background cover with local mining hero */
        .bg-mining-full {
            position: fixed;
            inset: 0;
            background-image: url('{{ asset('images/mining/login_hero.jpg') }}');
            background-size: cover;
            background-position: center 35%;
            z-index: -3;
        }
        .animate-kenburns {
            animation: kenburns 35s ease-in-out infinite alternate;
        }
        @keyframes kenburns {
            0% {
                transform: scale(1.0);
            }
            100% {
                transform: scale(1.06) translate(-1%, -0.5%);
            }
        }
        
        /* Dark overlay for contrast and depth */
        .bg-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(to right, rgba(9, 13, 22, 0.94) 0%, rgba(9, 13, 22, 0.85) 45%, rgba(2, 6, 23, 0.65) 100%),
                        radial-gradient(circle at center, transparent 20%, rgba(2, 6, 23, 0.88) 100%);
            z-index: -2;
        }

        /* Ambient Tunnel Lantern Glow */
        .ambient-tunnel-glow {
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 65% 45%, rgba(245, 158, 11, 0.16) 0%, rgba(217, 119, 6, 0.06) 35%, transparent 70%);
            pointer-events: none;
            z-index: -1;
            animation: tunnelBreathing 10s ease-in-out infinite alternate;
        }
        @keyframes tunnelBreathing {
            0% { opacity: 0.45; transform: scale(1); }
            100% { opacity: 0.85; transform: scale(1.05); }
        }

        @media (prefers-reduced-motion: reduce) {
            .animate-kenburns, .ambient-tunnel-glow, .veta-oro-border, .animate-fade-in-up {
                animation: none !important;
                transform: none !important;
            }
        }

        /* Glassmorphism card (frosted glass) */
        .glass-card {
            background: rgba(10, 15, 28, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            transform-style: preserve-3d;
            transition: transform 0.2s ease-out, box-shadow 0.3s;
            position: relative;
            z-index: 10;
        }

        /* Veta de Oro (Golden Glowing Seam) Border Animation */
        @keyframes goldVein {
            0%, 100% {
                border-color: rgba(245, 158, 11, 0.25);
                box-shadow: 0 0 15px rgba(245, 158, 11, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.06);
            }
            50% {
                border-color: rgba(245, 158, 11, 0.55);
                box-shadow: 0 0 25px rgba(245, 158, 11, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.12);
            }
        }
        .veta-oro-border {
            border: 1.5px solid rgba(245, 158, 11, 0.25);
            animation: goldVein 6s ease-in-out infinite;
        }

        /* Spotlight Sheen Layer inside card */
        .card-sheen {
            position: absolute;
            inset: 0;
            border-radius: 28px;
            pointer-events: none;
            z-index: 1;
            background: radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 60%);
            transition: background 0.15s ease-out;
        }

        /* Glass shine sweep on load/hover */
        .glass-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -150%;
            width: 40%;
            height: 100%;
            background: linear-gradient(
                to right,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.12) 30%,
                rgba(255, 255, 255, 0) 100%
            );
            transform: skewX(-25deg);
            pointer-events: none;
            z-index: 2;
            transition: none;
        }
        .glass-card:hover::before {
            left: 150%;
            transition: 1.4s cubic-bezier(0.25, 1, 0.5, 1);
        }

        /* Staggered entrance animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-up {
            opacity: 0;
            animation: fadeInUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }

        /* Pretty Transparent Glass Inputs */
        .glass-input-container {
            position: relative;
            margin-bottom: 22px;
        }
        .glass-input {
            background: rgba(15, 23, 42, 0.45) !important;
            border: 1.5px solid rgba(255, 255, 255, 0.12) !important;
            border-radius: 14px !important;
            padding: 14px 16px 14px 44px !important; /* pl-11 left icon space */
            width: 100% !important;
            color: #f8fafc !important;
            font-size: 14px !important;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        .glass-input::placeholder {
            color: rgba(148, 163, 184, 0.4) !important;
        }
        .glass-input:focus {
            outline: none !important;
            background: rgba(15, 23, 42, 0.6) !important;
            border-color: rgba(245, 158, 11, 0.65) !important;
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.25) !important;
        }

        /* Pulsing animated border glow on focus */
        @keyframes borderPulse {
            0%, 100% {
                border-color: rgba(245, 158, 11, 0.55) !important;
                box-shadow: 0 0 12px rgba(245, 158, 11, 0.18) !important;
            }
            50% {
                border-color: rgba(234, 88, 12, 0.85) !important;
                box-shadow: 0 0 24px rgba(234, 88, 12, 0.38) !important;
            }
        }
        .glass-input-focus-animate:focus {
            animation: borderPulse 2.5s infinite ease-in-out !important;
        }

        .glass-label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            margin-bottom: 8px;
            transition: color 0.3s;
        }
        .glass-input:focus ~ .glass-label {
            color: #f59e0b;
        }
        .left-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #475569;
            transition: color 0.3s, transform 0.3s;
            pointer-events: none;
            z-index: 10;
        }
        .glass-input:focus ~ .left-icon {
            color: #f59e0b;
            transform: translateY(-50%) scale(1.1);
        }

        /* Autofill overrides to enforce dark transparent backgrounds */
        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: #f8fafc !important;
            transition: background-color 5000s ease-in-out 0s;
            box-shadow: inset 0 0 20px 20px rgba(10, 12, 18, 0.8) !important;
        }

        /* Molten Gold Buttons with sweep reflection */
        .molten-gold-btn {
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: #020617;
            font-weight: 700;
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 0 15px rgba(234, 88, 12, 0.22);
            border-radius: 14px;
        }
        .molten-gold-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: -50%;
            width: 30%;
            height: 100%;
            background: linear-gradient(
                to right,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.45) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            transform: skewX(-25deg);
            animation: goldSweep 4.5s infinite linear;
        }
        @keyframes goldSweep {
            0% { left: -60%; }
            28% { left: 140%; }
            100% { left: 140%; }
        }
        .molten-gold-btn:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 0 32px rgba(249, 115, 22, 0.55);
        }
        .molten-gold-btn:active {
            transform: translateY(0) scale(1);
        }

        /* Sparks ejected from button */
        .button-spark {
            position: absolute;
            background: radial-gradient(circle, #fcd34d 0%, #f97316 60%, #ef4444 100%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 10;
            box-shadow: 0 0 5px #f59e0b;
        }

        /* Close Button decoration like in the screenshot */
        .card-close-decor {
            position: absolute;
            top: 18px;
            right: 18px;
            color: rgba(255, 255, 255, 0.35);
            font-size: 14px;
            transition: color 0.2s, transform 0.2s;
            cursor: pointer;
            z-index: 20;
        }
        .card-close-decor:hover {
            color: #f59e0b;
            transform: rotate(90deg);
        }


    </style>
</head>
<body class="h-full bg-slate-950 overflow-hidden relative min-h-screen">
    
    <!-- Animated Mine background -->
    <div class="bg-mining-full animate-kenburns"></div>
    <div class="bg-overlay"></div>
    
    <!-- Ambient Tunnel Glow & Depth -->
    <div class="ambient-tunnel-glow"></div>

    <div class="grid grid-cols-1 lg:grid-cols-12 min-h-screen relative z-10 w-full">
        
        <!-- Left Column: Login Form Container -->
        <div class="col-span-12 lg:col-span-5 flex flex-col justify-between p-6 sm:p-12 lg:p-16 relative z-10">
            
            <!-- Brand text on mobile -->
            <div class="flex items-center space-x-3 lg:invisible">
                <img src="{{ asset('images/logo_koyosita.png') }}" alt="KOYOSITA S.R.L." class="w-9 h-9 rounded-lg object-cover shadow-md border border-amber-500/40">
                <span class="text-sm font-bold uppercase tracking-wider text-amber-500">KOYOSITA S.R.L.</span>
            </div>

            <!-- Centralized Form Container -->
            <div class="my-auto w-full max-w-md mx-auto py-8">
                
                <!-- Frosted Glass Card with Gold Border & Parallax -->
                <div class="glass-card veta-oro-border p-8 sm:p-10 animate-fade-in-up">
                    
                    <!-- Card Sheen Layer -->
                    <div class="card-sheen"></div>
                    


                    <!-- Centered Header -->
                    <div class="text-center mb-8 relative z-10 animate-fade-in-up delay-100">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl overflow-hidden mb-3 shadow-lg shadow-orange-500/20 border border-amber-500/40">
                            <img src="{{ asset('images/logo_koyosita.png') }}" alt="KOYOSITA S.R.L." class="w-full h-full object-cover">
                        </div>
                        <h2 class="text-2xl font-extrabold tracking-wider uppercase text-slate-100">KOYOSITA S.R.L.</h2>
                        <span class="text-[9px] text-amber-500 font-mono tracking-widest block mt-0.5">EMPRESA MINERA — CONTROL DE PAGOS</span>
                        <p class="text-xs text-slate-400 mt-2">
                            Ingresa tus credenciales para acceder al sistema
                        </p>
                    </div>

                    <!-- Session Success Alert -->
                    @if(session('success'))
                        <div class="mb-5 p-3 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs flex items-center space-x-2.5 relative z-10 animate-fade-in-up">
                            <i class="fa-solid fa-circle-check text-sm"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    <!-- Validation Errors Alert -->
                    @if($errors->any())
                        <div class="mb-5 p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs flex items-center space-x-2.5 relative z-10 animate-fade-in-up">
                            <i class="fa-solid fa-circle-exclamation text-sm"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <!-- Login Form with Glass Inputs -->
                    <form action="{{ route('login') }}" method="POST" class="space-y-5 relative z-10 animate-fade-in-up delay-200">
                        @csrf
                        
                        <!-- Email Input -->
                        <div class="glass-input-container">
                            <label for="email" class="glass-label">Correo Electrónico</label>
                            <div class="relative">
                                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email', 'admin@mina.com') }}"
                                       class="glass-input glass-input-focus-animate" placeholder="admin@mina.com">
                                <div class="left-icon">
                                    <i class="fa-solid fa-envelope text-sm"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Password Input with Ojito -->
                        <div class="glass-input-container">
                            <label for="password" class="glass-label">Contraseña</label>
                            <div class="relative">
                                <input id="password" name="password" type="password" autocomplete="current-password" required
                                       class="glass-input glass-input-focus-animate pr-12" placeholder="••••••••">
                                <div class="left-icon">
                                    <i class="fa-solid fa-lock text-sm"></i>
                                </div>
                                <!-- Toggle eye button (Ojito) -->
                                <button type="button" onclick="togglePasswordVisibility('password', 'eye-icon')" 
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 hover:text-amber-500 transition duration-150 focus:outline-none z-20">
                                    <i id="eye-icon" class="fa-solid fa-eye text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Me check -->
                        <div class="flex items-center justify-between pt-1 animate-fade-in-up delay-300">
                            <div class="flex items-center">
                                <input id="remember" name="remember" type="checkbox"
                                       class="h-4 w-4 bg-slate-950/80 border-slate-800 text-orange-500 focus:ring-orange-500 rounded transition duration-150 cursor-pointer">
                                <label for="remember" class="ml-2.5 block text-xs text-slate-400 cursor-pointer hover:text-slate-200 transition">
                                    Mantener sesión iniciada
                                </label>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-3 animate-fade-in-up delay-300">
                            <button type="submit" id="submit-btn" class="w-full flex justify-center py-3.5 px-4 molten-gold-btn focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                <span>Iniciar Sesión</span>
                                <i class="fa-solid fa-right-to-bracket ml-2 self-center text-xs"></i>
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <a href="{{ route('register') }}" class="text-xs text-slate-400 hover:text-amber-400 transition font-mono">
                            ¿Registrar otro administrador? Click aquí
                        </a>
                    </div>

                </div>
            </div>

            <!-- Footer copyright area -->
            <div class="text-center lg:text-left text-[10px] text-slate-600 font-mono mt-8">
                <span>&copy; {{ date('Y') }} Empresa Minera KOYOSITA S.R.L. Todos los derechos reservados.</span>
            </div>

        </div>

        <!-- Right Column: Graphic Panel (Desktop Only) -->
        <div class="hidden lg:flex lg:col-span-7 relative flex-col justify-between p-16 relative z-10">
            
            <!-- Upper Header (Logo) -->
            <div class="flex items-center space-x-3 animate-fade-in-up">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl overflow-hidden shadow-lg border border-amber-500/40">
                    <img src="{{ asset('images/logo_koyosita.png') }}" alt="KOYOSITA S.R.L." class="w-full h-full object-cover">
                </div>
                <div>
                    <span class="text-base font-extrabold uppercase tracking-widest text-slate-100 leading-none block">KOYOSITA S.R.L.</span>
                    <span class="text-[9px] text-amber-500 font-mono tracking-widest block mt-0.5">EMPRESA MINERA & OPERACIONES</span>
                </div>
            </div>

            <!-- Lower Showcase Info -->
            <div class="max-w-xl my-auto animate-fade-in-up delay-200">
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold font-mono bg-amber-500/10 text-amber-500 border border-amber-500/20 uppercase tracking-widest">
                    Sistema de Control de Pagos Mineros (SCPM)
                </span>
                <h1 class="text-4xl font-extrabold text-white mt-4 tracking-tight leading-tight">
                    Eficiencia y precisión en la liquidación de frentes de trabajo
                </h1>
                <p class="mt-4 text-base text-slate-300 leading-relaxed">
                    Gestión integral de contratos, avances de producción por bocamina, control de anticipos semanales, y módulo avanzado de compra-venta de mineral por lotes con control de stock.
                </p>
                <div class="grid grid-cols-2 gap-y-4 gap-x-6 mt-8 border-t border-slate-800/80 pt-6">
                    <div class="flex items-center space-x-2.5 text-xs text-slate-400">
                        <i class="fa-solid fa-mountain text-amber-500 text-sm"></i>
                        <span>Bocaminas y Frentes</span>
                    </div>
                    <div class="flex items-center space-x-2.5 text-xs text-slate-400">
                        <i class="fa-solid fa-user-tie text-amber-500 text-sm"></i>
                        <span>Personal y Contratos</span>
                    </div>
                    <div class="flex items-center space-x-2.5 text-xs text-slate-400">
                        <i class="fa-solid fa-boxes-stacked text-amber-500 text-sm"></i>
                        <span>Almacén y Lotes de Mineral</span>
                    </div>
                    <div class="flex items-center space-x-2.5 text-xs text-slate-400">
                        <i class="fa-solid fa-wallet text-amber-500 text-sm"></i>
                        <span>Caja, Anticipos y Pagos</span>
                    </div>
                </div>
            </div>

            <div></div> <!-- empty spacing element for alignment -->

        </div>

    </div>

    <!-- Scripts for Canvas particles, Tilt Parallax, and Button Micro-sparks -->
    <script>
        // Password toggler
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input && icon) {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        }

    <!-- Lightweight interactive scripts -->
    <script>
        // Password toggler
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input && icon) {
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }
        }

        // Gentle Tilt Parallax on Desktop (disabled if prefers-reduced-motion)
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const card = document.querySelector('.glass-card');
        
        if (card && !prefersReducedMotion && window.innerWidth >= 1024) {
            let ticking = false;
            document.addEventListener('mousemove', (e) => {
                if (!ticking) {
                    window.requestAnimationFrame(() => {
                        const cx = window.innerWidth / 2;
                        const cy = window.innerHeight / 2;
                        const dx = e.clientX - cx;
                        const dy = e.clientY - cy;
                        
                        const rx = -(dy / cy) * 3.5;
                        const ry = (dx / cx) * 3.5;
                        
                        card.style.transform = `rotateX(${rx}deg) rotateY(${ry}deg)`;
                        
                        const sheen = card.querySelector('.card-sheen');
                        if (sheen) {
                            const xPercent = (e.clientX / window.innerWidth) * 100;
                            const yPercent = (e.clientY / window.innerHeight) * 100;
                            sheen.style.background = `radial-gradient(circle at ${xPercent}% ${yPercent}%, rgba(255, 255, 255, 0.06) 0%, transparent 60%)`;
                        }
                        ticking = false;
                    });
                    ticking = true;
                }
            }, { passive: true });
            
            document.addEventListener('mouseleave', () => {
                card.style.transform = 'rotateX(0deg) rotateY(0deg)';
                card.style.transition = 'transform 0.4s ease-out';
            });
            
            document.addEventListener('mouseenter', () => {
                card.style.transition = 'none';
            });
        }
    </script>
</body>
</html>
