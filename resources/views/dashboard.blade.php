@extends('layouts.app')

@section('title', 'Tablero Principal')

@section('content')
<div class="space-y-6">
    
    <!-- Panoramic Mining Hero Banner with subtle ambient movement -->
    <div class="relative overflow-hidden rounded-2xl border border-slate-800/80 shadow-2xl p-6 sm:p-8 mining-hero-container">
        <!-- Panoramic Mining Background Image with slow cinematic zoom -->
        <div class="absolute inset-0 bg-cover bg-center mining-hero-img" style="background-image: url('{{ asset('images/mining/dashboard_banner.jpg') }}');"></div>
        
        <!-- Multi-layer Gradients for Maximum Legibility & Interface Fade -->
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-950/85 to-slate-950/55 pointer-events-none"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/30 to-transparent pointer-events-none"></div>
        
        <!-- Ambient Tunnel Lantern Glow -->
        <div class="ambient-mining-lantern pointer-events-none"></div>

        <!-- Content -->
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="max-w-2xl">
                <div class="flex items-center space-x-2.5 mb-2.5">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase tracking-wider bg-amber-500/15 text-amber-400 border border-amber-500/30">
                        <i class="fa-solid fa-gem mr-1 text-[9px]"></i> Operaciones Mineras
                    </span>
                    <span class="text-xs text-slate-400 font-mono flex items-center">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                        Vetas y Bocaminas en Producción
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white flex items-center gap-3">
                    Tablero de Control Operacional
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-2 leading-relaxed">
                    Monitoreo integral de personal en galerías subterráneas, liquidaciones de mano de obra, anticipos y transacciones de mineral.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex items-center flex-wrap gap-2.5 self-start lg:self-center">
                <a href="{{ route('pagos.create') }}" class="btn-gold px-4 py-2.5 rounded-xl text-xs font-bold inline-flex items-center shadow-lg cursor-pointer">
                    <i class="fa-solid fa-plus mr-2 text-xs"></i> Nueva Liquidación
                </a>
                <a href="{{ route('anticipos.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-900/80 hover:bg-slate-800 text-slate-200 border border-slate-700/80 transition-all inline-flex items-center shadow cursor-pointer">
                    <i class="fa-solid fa-hand-holding-dollar mr-2 text-amber-400"></i> Anticipo
                </a>
                <a href="{{ route('transacciones-minerales.create') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold bg-slate-900/80 hover:bg-slate-800 text-slate-200 border border-slate-700/80 transition-all inline-flex items-center shadow cursor-pointer">
                    <i class="fa-solid fa-cart-flatbed mr-2 text-amber-400"></i> Registrar Mineral
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Grid: 5 Cohesive Premium Cards with Distinct Jewel Tones & Shimmer -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- Card 1: Trabajadores (Gold / Amber) -->
        <div class="mining-card interactive shimmer-card accent-border-gold glow-gold p-4">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Personal Registrado</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400 shadow-sm">
                    <i class="fa-solid fa-users text-xs"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-slate-100 tabular-nums">{{ $totalTrabajadores }}</span>
                <span class="text-[10px] font-medium text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/20">
                    {{ $totalContratosActivos ?? $totalTrabajadores }} Activos
                </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2 flex items-center">
                <i class="fa-solid fa-id-badge text-[9px] mr-1.5 text-amber-400"></i> Total en nómina
            </p>
        </div>

        <!-- Card 2: Bocaminas (Copper / Bronze) -->
        <div class="mining-card interactive shimmer-card accent-border-copper glow-copper p-4">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Bocaminas</span>
                <div class="w-8 h-8 rounded-lg bg-orange-500/15 border border-orange-500/30 flex items-center justify-center text-orange-400 shadow-sm">
                    <i class="fa-solid fa-mountain text-xs"></i>
                </div>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-slate-100 tabular-nums">{{ $totalBocaminas }}</span>
                <span class="text-[10px] font-medium text-orange-400 bg-orange-500/10 px-1.5 py-0.5 rounded border border-orange-500/20">
                    Frentes Activos
                </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2 flex items-center">
                <i class="fa-solid fa-layer-group text-[9px] mr-1.5 text-orange-400"></i> Vetas en explotación
            </p>
        </div>

        <!-- Card 3: Saldo Caja Chica (Emerald Vault / Ruby Alert) -->
        @php
            $isSaldoNegativo = $saldo_caja < 0;
        @endphp
        <div class="mining-card interactive shimmer-card {{ $isSaldoNegativo ? 'accent-border-rose glow-rose' : 'accent-border-emerald glow-emerald' }} p-4">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Caja del Personal</span>
                <div class="w-8 h-8 rounded-lg {{ $isSaldoNegativo ? 'bg-rose-500/20 text-rose-400 border border-rose-500/35' : 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' }} flex items-center justify-center shadow-sm">
                    <i class="fa-solid fa-vault text-xs"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-xl font-extrabold tabular-nums {{ $isSaldoNegativo ? 'text-rose-400' : 'text-slate-100' }}">
                    Bs. {{ number_format($saldo_caja, 2) }}
                </div>
            </div>
            <div class="mt-2 flex items-center justify-between text-[11px]">
                @if($isSaldoNegativo)
                    <span class="text-rose-400 font-semibold flex items-center">
                        <i class="fa-solid fa-triangle-exclamation mr-1 text-[10px]"></i> Saldo Deudor
                    </span>
                @else
                    <span class="text-emerald-400 font-medium flex items-center">
                        <i class="fa-solid fa-circle-check mr-1 text-[10px]"></i> Efectivo Disponible
                    </span>
                @endif
                <a href="{{ route('fondos-caja.index') }}" class="text-[10px] text-amber-400 hover:underline font-medium">Ver Caja</a>
            </div>
        </div>

        <!-- Card 4: Ventas Mineral (Sapphire Cyan) -->
        <div class="mining-card interactive shimmer-card accent-border-cyan glow-cyan p-4">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Ventas de Mineral</span>
                <div class="w-8 h-8 rounded-lg bg-cyan-500/15 border border-cyan-500/30 flex items-center justify-center text-cyan-400 shadow-sm">
                    <i class="fa-solid fa-arrow-trend-up text-xs"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-xl font-extrabold text-slate-100 tabular-nums">
                    Bs. {{ number_format($totalVentasMineral, 2) }}
                </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2 flex items-center justify-between">
                <span>Ingresos comercializ.</span>
                <span class="text-[10px] font-mono text-cyan-400 font-bold">Módulo 2</span>
            </p>
        </div>

        <!-- Card 5: Compras Mineral (Amethyst Violet) -->
        <div class="mining-card interactive shimmer-card accent-border-purple glow-purple p-4">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Compras de Mineral</span>
                <div class="w-8 h-8 rounded-lg bg-purple-500/15 border border-purple-500/30 flex items-center justify-center text-purple-400 shadow-sm">
                    <i class="fa-solid fa-cart-shopping text-xs"></i>
                </div>
            </div>
            <div class="mt-3">
                <span class="text-xl font-extrabold text-slate-100 tabular-nums">
                    Bs. {{ number_format($totalComprasMineral, 2) }}
                </span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2 flex items-center justify-between">
                <span>Egresos acopio lotes</span>
                <span class="text-[10px] font-mono text-purple-400 font-bold">Módulo 2</span>
            </p>
        </div>

    </div>

    <!-- Analytical Section: 3 Columns (Charts & Clear Top Ranking) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Column 1: Flujo de Pagos Mensual -->
        <div class="mining-card shimmer-card p-5 flex flex-col">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-white/5">
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-200 flex items-center">
                        <i class="fa-solid fa-chart-line mr-2 text-amber-400"></i> Flujo de Pagos
                    </h2>
                    <span class="text-[10px] text-slate-400">Neto desembolsado por mes en Bs.</span>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    Últimos 6 Meses
                </span>
            </div>
            <div class="relative h-60 w-full mt-auto">
                <canvas id="pagosChart"></canvas>
            </div>
        </div>

        <!-- Column 2: Producción por Bocamina -->
        <div class="mining-card shimmer-card p-5 flex flex-col">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-white/5">
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-200 flex items-center">
                        <i class="fa-solid fa-chart-pie mr-2 text-orange-400"></i> Producción por Bocamina
                    </h2>
                    <span class="text-[10px] text-slate-400">Distribución de valorización bruta</span>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-white/5 text-slate-300 border border-white/10">
                    Frentes
                </span>
            </div>
            <div class="relative h-60 w-full flex items-center justify-center mt-auto">
                <canvas id="bocaminasChart"></canvas>
            </div>
        </div>

        <!-- Column 3: Top Trabajadores (Producción Valorizada) -->
        <div class="mining-card shimmer-card p-5 flex flex-col">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-white/5">
                <div>
                    <h2 class="text-xs font-bold uppercase tracking-wider text-slate-200 flex items-center">
                        <i class="fa-solid fa-trophy mr-2 text-amber-400"></i> Top Trabajadores
                    </h2>
                    <span class="text-[10px] text-slate-400">Producción valorizada acumulada en Bs.</span>
                </div>
                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    Líderes
                </span>
            </div>

            @php
                $maxProduccion = max($topTrabajadores->max('trabajos_sum_subtotal') ?? 1, 1);
            @endphp
            <div class="space-y-2.5 mt-1 flex-1 overflow-y-auto max-h-64 pr-1">
                @forelse($topTrabajadores as $index => $trabajador)
                    @php
                        $subtotalVal = $trabajador->trabajos_sum_subtotal ?? 0;
                        $pctProduccion = round(($subtotalVal / $maxProduccion) * 100);
                        $medalBadge = match($index) {
                            0 => [
                                'bg' => 'bg-gradient-to-br from-amber-500/30 to-amber-600/10 text-amber-300 border-amber-500/50 shadow-[0_0_12px_rgba(245,158,11,0.3)]',
                                'label' => '1°',
                                'icon' => 'fa-crown text-amber-400',
                                'bar' => 'bg-gradient-to-r from-amber-500 to-yellow-300'
                            ],
                            1 => [
                                'bg' => 'bg-gradient-to-br from-slate-300/25 to-slate-400/10 text-slate-200 border-slate-300/40 shadow-sm',
                                'label' => '2°',
                                'icon' => 'fa-medal text-slate-300',
                                'bar' => 'bg-gradient-to-r from-slate-400 to-slate-200'
                            ],
                            2 => [
                                'bg' => 'bg-gradient-to-br from-orange-600/25 to-amber-700/10 text-orange-300 border-orange-600/40 shadow-sm',
                                'label' => '3°',
                                'icon' => 'fa-award text-orange-400',
                                'bar' => 'bg-gradient-to-r from-orange-600 to-amber-400'
                            ],
                            default => [
                                'bg' => 'bg-white/5 text-slate-400 border-white/10',
                                'label' => ($index + 1) . '°',
                                'icon' => 'fa-user text-slate-500',
                                'bar' => 'bg-gradient-to-r from-sky-500 to-indigo-400'
                            ]
                        };
                    @endphp
                    <div class="p-2.5 rounded-xl bg-white/[0.02] border border-white/5 hover:border-amber-500/30 hover:bg-white/[0.04] hover:translate-x-1 transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 min-w-0">
                                <span class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-mono font-bold border flex-shrink-0 {{ $medalBadge['bg'] }}">
                                    @if($index === 0)
                                        <i class="fa-solid fa-crown text-[10px]"></i>
                                    @else
                                        {{ $medalBadge['label'] }}
                                    @endif
                                </span>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-100 truncate flex items-center gap-1.5">
                                        <span>{{ $trabajador->nombre }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 uppercase tracking-wider font-mono">
                                        {{ $trabajador->rol ?: 'Sin cargo' }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0 pl-2">
                                <div class="text-xs font-extrabold text-amber-400 font-mono tabular-nums">
                                    Bs. {{ number_format($subtotalVal, 2) }}
                                </div>
                                <div class="text-[9px] text-slate-500 font-mono">
                                    {{ $trabajador->trabajos_count ?? 0 }} liquidaciones
                                </div>
                            </div>
                        </div>
                        <!-- Mini Animated Relative Performance Bar -->
                        <div class="mt-2 w-full bg-slate-800/80 rounded-full h-1.5 overflow-hidden">
                            <div class="h-full rounded-full {{ $medalBadge['bar'] }}" style="width: {{ max($pctProduccion, 6) }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-500 text-xs">
                        <i class="fa-solid fa-file-invoice text-2xl mb-2 opacity-40"></i>
                        <p>No se registran trabajos de producción en planillas.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Feeds: Personal y Pagos vs Comercialización de Minerales -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        
        <!-- Left: Movimientos al Personal (Pagos y Anticipos Recientes) -->
        <div class="mining-card shimmer-card p-5">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-white/5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-200 flex items-center">
                    <i class="fa-solid fa-money-check-dollar mr-2 text-amber-400"></i> Movimientos al Personal
                </h2>
                <div class="flex items-center space-x-2 text-[11px]">
                    <a href="{{ route('pagos.index') }}" class="text-amber-400 hover:underline">Ver Pagos</a>
                    <span class="text-slate-600">•</span>
                    <a href="{{ route('anticipos.index') }}" class="text-amber-400 hover:underline">Ver Anticipos</a>
                </div>
            </div>

            <!-- Últimos Pagos -->
            <div class="space-y-3">
                <div class="text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400">
                    Últimas Liquidaciones Procesadas
                </div>
                <div class="space-y-2">
                    @forelse($recientesPagos as $pago)
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/[0.02] border border-white/5 hover:border-emerald-500/30 hover:bg-white/[0.04] hover:translate-x-1 transition-all">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-receipt text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-100 truncate">{{ $pago->trabajador->nombre ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $pago->fecha ? $pago->fecha->format('d/m/Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 flex-shrink-0">
                                <span class="text-xs font-bold text-emerald-400 font-mono tabular-nums">
                                    Bs. {{ number_format($pago->neto, 2) }}
                                </span>
                                <a href="{{ route('pagos.show', $pago->id) }}" class="p-1.5 rounded-lg bg-white/5 hover:bg-white/10 text-slate-300 hover:text-white transition-colors" title="Ver Recibo de Pago">
                                    <i class="fa-solid fa-print text-xs"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-xs text-slate-500 font-mono">Sin pagos recientes</div>
                    @endforelse
                </div>

                <!-- Últimos Anticipos -->
                <div class="pt-3 text-[10px] font-mono font-bold uppercase tracking-wider text-slate-400 border-t border-white/5">
                    Últimos Anticipos Otorgados
                </div>
                <div class="space-y-2">
                    @forelse($recientesAnticipos as $anticipo)
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-white/[0.02] border border-white/5 hover:border-amber-500/30 hover:bg-white/[0.04] hover:translate-x-1 transition-all">
                            <div class="flex items-center space-x-3 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-hand-holding-dollar text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-100 truncate">{{ $anticipo->trabajador->nombre ?? 'N/A' }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $anticipo->fecha ? $anticipo->fecha->format('d/m/Y') : '-' }}</div>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-xs font-bold text-rose-400 font-mono tabular-nums">
                                    Bs. {{ number_format($anticipo->monto, 2) }}
                                </span>
                                <div class="text-[9px] text-slate-500 font-mono">
                                    Saldo: Bs. {{ number_format($anticipo->saldo, 2) }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-xs text-slate-500 font-mono">Sin anticipos recientes</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Movimientos de Minerales (Compras y Ventas de Lotes) -->
        <div class="mining-card shimmer-card p-5">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-white/5">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-200 flex items-center">
                    <i class="fa-solid fa-dolly mr-2 text-amber-400"></i> Movimientos de Minerales (Lotes)
                </h2>
                <a href="{{ route('transacciones-minerales.index') }}" class="text-[11px] text-amber-400 hover:underline">
                    Ver Todos los Lotes
                </a>
            </div>

            <div class="space-y-2.5">
                @forelse($recientesTransaccionesMineral as $tx)
                    @php
                        $isVenta = $tx->tipo === 'venta';
                    @endphp
                    <div class="flex items-center justify-between p-3 rounded-xl bg-white/[0.02] border border-white/5 hover:border-cyan-500/30 hover:bg-white/[0.04] hover:translate-x-1 transition-all">
                        <div class="flex items-center space-x-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg {{ $isVenta ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-purple-500/10 text-purple-400 border border-purple-500/20' }} flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid {{ $isVenta ? 'fa-arrow-trend-up' : 'fa-arrow-down' }} text-xs"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-slate-100 truncate">{{ $tx->cliente_proveedor }}</span>
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-mono font-bold uppercase {{ $isVenta ? 'bg-cyan-500/10 text-cyan-400' : 'bg-purple-500/10 text-purple-400' }}">
                                        {{ $isVenta ? 'Venta' : 'Compra' }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5 flex items-center space-x-2 font-mono">
                                    <span>{{ $tx->presentacion ?: 'Lote' }}</span>
                                    <span>•</span>
                                    <span>{{ number_format($tx->peso_neto_seco, 2) }} TN Seco</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0 pl-2">
                            <div class="text-xs font-extrabold text-slate-100 font-mono tabular-nums">
                                Bs. {{ number_format($tx->monto_total, 2) }}
                            </div>
                            <div class="text-[9px] text-slate-500 font-mono">
                                {{ $tx->fecha ? $tx->fecha->format('d/m/Y') : '-' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-xs text-slate-500 font-mono">
                        <i class="fa-solid fa-boxes-stacked text-2xl mb-2 opacity-40"></i>
                        <p>No se registran transacciones de mineral recientemente.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

<!-- Interactive Chart Scripts (Dynamic, Accessible, Palette Harmonized) -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const produccionBocaminas = @json($produccionBocaminas ?? []);
        const pagosMensuales = @json($pagosMensuales ?? []);

        function getChartPalette() {
            const isLight = document.documentElement.classList.contains('light-theme');
            return {
                grid: isLight ? 'rgba(15, 23, 42, 0.06)' : 'rgba(255, 255, 255, 0.05)',
                text: isLight ? '#475569' : '#94a3b8',
                cardBorder: isLight ? '#ffffff' : '#0f172a'
            };
        }

        const palette = getChartPalette();

        // 1. Chart Flujo de Pagos (Area / Line Chart)
        const pagosCanvas = document.getElementById('pagosChart');
        let pagosChartInstance = null;
        if (pagosCanvas) {
            const ctx = pagosCanvas.getContext('2d');
            
            // Gradient fill
            const gradient = ctx.createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, 'rgba(245, 158, 11, 0.25)');
            gradient.addColorStop(1, 'rgba(245, 158, 11, 0.0)');

            const labels = pagosMensuales.length > 0 ? pagosMensuales.map(p => p.etiqueta) : ['Sin datos'];
            const data = pagosMensuales.length > 0 ? pagosMensuales.map(p => p.total) : [0];

            pagosChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Neto Desembolsado (Bs.)',
                        data: data,
                        backgroundColor: gradient,
                        borderColor: '#f59e0b',
                        borderWidth: 2,
                        pointBackgroundColor: '#f59e0b',
                        pointBorderColor: palette.cardBorder,
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.95)',
                            titleColor: '#f8fafc',
                            bodyColor: '#f59e0b',
                            borderColor: 'rgba(245, 158, 11, 0.3)',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    return 'Bs. ' + Number(context.raw).toLocaleString('es-BO', { minimumFractionDigits: 2 });
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: palette.grid },
                            ticks: { color: palette.text, font: { family: 'Outfit', size: 10 } }
                        },
                        y: {
                            grid: { color: palette.grid },
                            ticks: {
                                color: palette.text,
                                font: { family: 'Outfit', size: 10 },
                                callback: function(val) {
                                    return 'Bs. ' + Number(val).toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // 2. Chart Bocaminas (Doughnut Harmonized)
        const bocaminasCanvas = document.getElementById('bocaminasChart');
        let bocaminasChartInstance = null;
        if (bocaminasCanvas) {
            const ctx = bocaminasCanvas.getContext('2d');
            
            const bLabels = produccionBocaminas.length > 0 ? produccionBocaminas.map(b => b.nombre) : ['Sin registros'];
            const bData = produccionBocaminas.length > 0 ? produccionBocaminas.map(b => b.total) : [1];

            // Cohesive mineral palette: warm amber, dark slate, deep copper, bronze, golden
            const colors = [
                '#f59e0b', // Amber Gold
                '#0ea5e9', // Sky Mineral
                '#10b981', // Emerald Veta
                '#d97706', // Copper
                '#64748b'  // Slate
            ];

            bocaminasChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: bLabels,
                    datasets: [{
                        data: bData,
                        backgroundColor: colors.slice(0, bLabels.length),
                        borderColor: palette.cardBorder,
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                padding: 12,
                                color: palette.text,
                                font: { family: 'Outfit', size: 10 }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.95)',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': Bs. ' + Number(context.raw).toLocaleString('es-BO', { minimumFractionDigits: 2 });
                                }
                            }
                        }
                    }
                }
            });
        }

        // Listen to theme change
        window.addEventListener('theme-changed', function () {
            const p = getChartPalette();
            if (pagosChartInstance) {
                pagosChartInstance.options.scales.x.grid.color = p.grid;
                pagosChartInstance.options.scales.x.ticks.color = p.text;
                pagosChartInstance.options.scales.y.grid.color = p.grid;
                pagosChartInstance.options.scales.y.ticks.color = p.text;
                pagosChartInstance.data.datasets[0].pointBorderColor = p.cardBorder;
                pagosChartInstance.update();
            }
            if (bocaminasChartInstance) {
                bocaminasChartInstance.options.plugins.legend.labels.color = p.text;
                bocaminasChartInstance.data.datasets[0].borderColor = p.cardBorder;
                bocaminasChartInstance.update();
            }
        });
    });
</script>
@endsection
