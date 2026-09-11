<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-950 text-slate-100">
<head>
    <script>
        // Force dark theme on login page
        document.documentElement.classList.remove('light-theme');
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - SCPM</title>
    
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

        /* Full-screen Ken Burns background cover */
        .bg-mining-full {
            position: fixed;
            inset: 0;
            background-image: url('/images/mining_bg.png');
            background-size: cover;
            background-position: center;
            z-index: -2;
        }
        .animate-kenburns {
            animation: kenburns 45s ease-in-out infinite;
        }
        @keyframes kenburns {
            0%, 100% {
                transform: scale(1.02) translate(0, 0);
            }
            50% {
                transform: scale(1.10) translate(-0.8%, -0.4%);
            }
        }
        
        /* Dark overlay for contrast */
        .bg-overlay {
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at center, rgba(15, 23, 42, 0.35) 0%, rgba(2, 6, 23, 0.88) 100%);
            z-index: -1;
        }

        /* Glassmorphism card (frosted glass) */
        .glass-card {
            background: rgba(10, 12, 18, 0.45);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border-radius: 28px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            transform-style: preserve-3d;
            transition: transform 0.1s ease-out, box-shadow 0.3s;
            position: relative;
            z-index: 10;
        }

        /* Veta de Oro (Golden Glowing Seam) Border Animation */
        @keyframes goldVein {
            0%, 100% {
                border-color: rgba(245, 158, 11, 0.22);
                box-shadow: 0 0 20px rgba(245, 158, 11, 0.1), inset 0 1px 0 rgba(255, 255, 255, 0.08);
            }
            50% {
                border-color: rgba(234, 88, 12, 0.55);
                box-shadow: 0 0 35px rgba(234, 88, 12, 0.22), inset 0 1px 0 rgba(255, 255, 255, 0.18);
            }
        }
        .veta-oro-border {
            border: 1.5px solid rgba(245, 158, 11, 0.22);
            animation: goldVein 7s ease-in-out infinite;
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
    
    <!-- Canvas for Floating Glowing Gold/Fire Sparks -->
    <canvas id="particle-canvas" class="fixed inset-0 pointer-events-none z-0 opacity-75"></canvas>

    <div class="grid grid-cols-1 lg:grid-cols-12 min-h-screen relative z-10 w-full">
        
        <!-- Left Column: Login Form Container -->
        <div class="col-span-12 lg:col-span-5 flex flex-col justify-between p-6 sm:p-12 lg:p-16 relative z-10">
            
            <!-- Brand text on mobile -->
            <div class="flex items-center space-x-3 lg:invisible">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 shadow-md">
                    <i class="fa-solid fa-gem text-slate-950 text-base"></i>
                </div>
                <span class="text-sm font-bold uppercase tracking-wider text-amber-500">SCPM</span>
            </div>

            <!-- Centralized Form Container -->
            <div class="my-auto w-full max-w-md mx-auto py-8">
                
                <!-- Frosted Glass Card with Gold Border & Parallax -->
                <div class="glass-card veta-oro-border p-8 sm:p-10 animate-fade-in-up">
                    
                    <!-- Card Sheen Layer -->
                    <div class="card-sheen"></div>
                    


                    <!-- Centered Header -->
                    <div class="text-center mb-8 relative z-10 animate-fade-in-up delay-100">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 mb-3 shadow-lg shadow-orange-500/20">
                            <i class="fa-solid fa-gem text-slate-950 text-2xl"></i>
                        </div>
                        <h2 class="text-3xl font-extrabold tracking-wider uppercase text-slate-100">SCPM</h2>
                        <span class="text-[9px] text-amber-500 font-mono tracking-widest block mt-0.5">SISTEMA CONTROL DE PAGOS</span>
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
                <span>&copy; {{ date('Y') }} TORMAN Minería. Todos los derechos reservados.</span>
            </div>

        </div>

        <!-- Right Column: Graphic Panel (Desktop Only) -->
        <div class="hidden lg:flex lg:col-span-7 relative flex-col justify-between p-16 relative z-10">
            
            <!-- Upper Header (Logo) -->
            <div class="flex items-center space-x-3 animate-fade-in-up">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg">
                    <i class="fa-solid fa-gem text-slate-950 text-lg"></i>
                </div>
                <div>
                    <span class="text-base font-extrabold uppercase tracking-widest text-slate-100 leading-none block">TORMAN</span>
                    <span class="text-[9px] text-amber-500 font-mono tracking-widest block mt-0.5">MINERÍA & OPERACIONES</span>
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

        // Particle Canvas - Embers physics
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
            const maxParticles = 75; // Full screen spark density
            
            class Spark {
                constructor() {
                    this.reset(true);
                }
                
                reset(initial = false) {
                    this.x = Math.random() * width;
                    this.y = initial ? Math.random() * height : height + 15;
                    this.size = Math.random() * 2.8 + 1.0;
                    this.speedY = Math.random() * 1.6 + 0.7;
                    this.speedX = (Math.random() - 0.5) * 0.7;
                    this.life = Math.random() * 0.75 + 0.25; // life value 0 to 1
                    this.decay = Math.random() * 0.003 + 0.0018;
                    this.opacity = Math.random() * 0.85 + 0.15;
                    this.wiggleFreq = Math.random() * 0.015 + 0.004;
                    this.wiggleAmp = Math.random() * 1.8 + 0.3;
                }
                
                update() {
                    this.y -= this.speedY;
                    this.life -= this.decay;
                    
                    // Fire-like wiggle
                    this.x += Math.sin(this.y * this.wiggleFreq) * 0.2 * this.wiggleAmp;
                    
                    // Wind repulsion from mouse
                    if (mouse.active) {
                        const dx = this.x - mouse.x;
                        const dy = this.y - mouse.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        if (dist < 140) {
                            const force = (140 - dist) / 140;
                            this.x += (dx / dist) * force * 3.5;
                            this.y += (dy / dist) * force * 1.5;
                        }
                    }
                    
                    if (this.life <= 0 || this.x < 0 || this.x > width || this.y < -15) {
                        this.reset();
                    }
                }
                
                draw() {
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    
                    // Temperature color states
                    let r, g, b;
                    if (this.life > 0.7) {
                        // Glowing white-yellow seam
                        r = 254; g = 240; b = 138;
                    } else if (this.life > 0.4) {
                        // Molten copper orange
                        r = 249; g = 115; b = 22;
                    } else if (this.life > 0.18) {
                        // Fire red
                        r = 239; g = 68; b = 68;
                    } else {
                        // Ash gray
                        r = 100; g = 116; b = 139;
                    }
                    
                    ctx.fillStyle = `rgba(${r}, ${g}, ${b}, ${this.life * this.opacity})`;
                    ctx.shadowBlur = this.life > 0.4 ? this.size * 3.5 : 0;
                    ctx.shadowColor = 'rgb(249, 115, 22)';
                    ctx.fill();
                    ctx.shadowBlur = 0; // reset shadow for performance
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

        // 3D Parallax Tilt effect on Card
        const card = document.querySelector('.glass-card');
        if (card) {
            document.addEventListener('mousemove', (e) => {
                const cx = window.innerWidth / 2;
                const cy = window.innerHeight / 2;
                const dx = e.clientX - cx;
                const dy = e.clientY - cy;
                
                // Max 6 degrees rotation
                const rx = -(dy / cy) * 6.5;
                const ry = (dx / cx) * 6.5;
                
                card.style.transform = `rotateX(${rx}deg) rotateY(${ry}deg)`;
                
                // Shift spotlight reflection sheen
                const sheen = card.querySelector('.card-sheen');
                if (sheen) {
                    const xPercent = (e.clientX / window.innerWidth) * 100;
                    const yPercent = (e.clientY / window.innerHeight) * 100;
                    sheen.style.background = `radial-gradient(circle at ${xPercent}% ${yPercent}%, rgba(255, 255, 255, 0.08) 0%, transparent 65%)`;
                }
            });
            
            // Soft return transition on mouse exit
            document.addEventListener('mouseleave', () => {
                card.style.transform = 'rotateX(0deg) rotateY(0deg)';
                card.style.transition = 'transform 0.5s cubic-bezier(0.25, 0.8, 0.25, 1)';
            });
            
            document.addEventListener('mouseenter', () => {
                card.style.transition = 'none';
            });
        }

        // Micro-Sparks Button animation
        const btn = document.getElementById('submit-btn');
        if (btn) {
            function spawnSparks(e) {
                const rect = btn.getBoundingClientRect();
                // Spawn 6 spark particles
                for (let i = 0; i < 6; i++) {
                    const spark = document.createElement('span');
                    spark.className = 'button-spark';
                    const size = Math.random() * 3.5 + 2.0;
                    spark.style.width = `${size}px`;
                    spark.style.height = `${size}px`;
                    
                    // Inside button coordinates
                    const x = Math.random() * rect.width;
                    const y = rect.height - Math.random() * 3;
                    spark.style.left = `${x}px`;
                    spark.style.top = `${y}px`;
                    
                    // Trajectory velocity
                    const vx = (Math.random() - 0.5) * 55;
                    const vy = -(Math.random() * 55 + 35);
                    
                    btn.appendChild(spark);
                    
                    spark.animate([
                        { transform: 'translate(0, 0) scale(1)', opacity: 1 },
                        { transform: `translate(${vx}px, ${vy}px) scale(0)`, opacity: 0 }
                    ], {
                        duration: Math.random() * 500 + 400,
                        easing: 'cubic-bezier(0.1, 0.8, 0.3, 1)'
                    }).onfinish = () => spark.remove();
                }
            }
            
            // Spawn sparks continuously on mouse hover
            let sparkInterval;
            btn.addEventListener('mouseenter', (e) => {
                spawnSparks(e);
                sparkInterval = setInterval(() => spawnSparks(e), 250);
            });
            btn.addEventListener('mouseleave', () => {
                clearInterval(sparkInterval);
            });
            
            // Burst sparks on click
            btn.addEventListener('click', (e) => {
                for(let k = 0; k < 3; k++) {
                    setTimeout(() => spawnSparks(e), k * 80);
                }
            });
        }
    </script>
</body>
</html>
