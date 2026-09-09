@extends('layouts.app')

@section('title', 'Comprobante de Egreso #' . str_pad($pago->id, 5, '0', STR_PAD_LEFT))

@section('content')
@php
if (!function_exists('montoEnLetrasOficial')) {
    function montoEnLetrasOficial($monto) {
        $monto = floatval($monto);
        $entero = floor($monto);
        $centavos = round(($monto - $entero) * 100);
        $centavosStr = str_pad($centavos, 2, '0', STR_PAD_LEFT) . '/100';

        $unidades = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE', 'DIEZ', 'ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE', 'VEINTE'];
        $decenas = ['', '', 'VEINTI', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
        $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];

        $numALetras = function($n) use (&$numALetras, $unidades, $decenas, $centenas) {
            if ($n == 0) return '';
            if ($n <= 20) return $unidades[$n];
            if ($n < 30) return 'VEINTI' . $unidades[$n - 20];
            if ($n < 100) {
                $d = floor($n / 10);
                $u = $n % 10;
                return $decenas[$d] . ($u > 0 ? ' Y ' . $unidades[$u] : '');
            }
            if ($n == 100) return 'CIEN';
            if ($n < 1000) {
                $c = floor($n / 100);
                $resto = $n % 100;
                return $centenas[$c] . ($resto > 0 ? ' ' . $numALetras($resto) : '');
            }
            if ($n < 1000000) {
                $miles = floor($n / 1000);
                $resto = $n % 1000;
                $milesStr = ($miles == 1) ? 'UN MIL' : $numALetras($miles) . ' MIL';
                return $milesStr . ($resto > 0 ? ' ' . $numALetras($resto) : '');
            }
            return number_format($n, 0, '', '');
        };

        $letras = ($entero == 0) ? 'CERO' : trim($numALetras($entero));
        return "SON: " . $letras . " " . $centavosStr . " BOLIVIANOS";
    }
}
@endphp

<!-- Custom Styles for Premium Receipt and Print layout -->
<style>
    /* Premium High-Contrast styling for printable receipt container */
    .receipt-card-wrapper {
        background: #ffffff !important;
        color: #0f172a !important;
        border-radius: 20px !important;
        border: 2px solid #0f172a !important;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08) !important;
        position: relative !important;
        overflow: hidden !important;
    }

    .receipt-card-wrapper::before {
        content: '' !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        height: 6px !important;
        background: linear-gradient(90deg, #10b981, #0ea5e9, #6366f1) !important;
        z-index: 10 !important;
    }

    /* Print styles to guarantee exact copy on Postcard 100x148mm, 80mm Roll, A4 or Letter */
    #thermal-ticket-80mm {
        display: none;
    }

    @media print {
        @page {
            margin: 0mm !important;
        }
        body {
            margin: 0 !important;
            padding: 0 !important;
        }
        html, body.thermal-print-mode {
            background: #ffffff !important;
            background-color: #ffffff !important;
            color: #000000 !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        body.thermal-print-mode * {
            background: transparent !important;
            background-color: transparent !important;
            color: #000000 !important;
            -webkit-print-color-adjust: economy !important;
            print-color-adjust: economy !important;
            color-adjust: economy !important;
        }
        body.thermal-print-mode .no-print,
        body.thermal-print-mode #receipt-card {
            display: none !important;
        }
        body.thermal-print-mode #thermal-ticket-80mm {
            display: block !important;
            width: 70mm !important;
            max-width: 70mm !important;
            margin: 0 auto !important;
            padding: 0 !important;
            background: #ffffff !important;
            background-color: #ffffff !important;
            font-family: 'Courier New', Courier, monospace !important;
            font-size: 10.5px !important;
            color: #000000 !important;
            box-sizing: border-box !important;
        }

        body:not(.thermal-print-mode) {
            background: #ffffff !important;
            color: #000000 !important;
        }
        body:not(.thermal-print-mode) .no-print {
            display: none !important;
        }
        body:not(.thermal-print-mode) .receipt-card-wrapper {
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            margin: 0 auto !important;
            width: 100% !important;
            max-width: 100% !important;
            background: #ffffff !important;
            box-sizing: border-box !important;
        }
        body:not(.thermal-print-mode) .receipt-card-wrapper::before {
            display: none !important;
        }
        body:not(.thermal-print-mode) .print-container {
            border: 2px solid #000000 !important;
            border-radius: 6px !important;
            padding: 0.6rem 0.8rem !important;
            margin: 0 auto !important;
            box-sizing: border-box !important;
            width: 100% !important;
            overflow: hidden !important;
        }
        body:not(.thermal-print-mode) #thermal-ticket-80mm {
            display: none !important;
        }
    }

    /* 3D button styling */
    .btn-3d-receipt {
        border-radius: 12px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        transition: all 0.15s ease !important;
        cursor: pointer !important;
    }

    .btn-3d-receipt-pdf {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        color: #ffffff !important;
        border: 1px solid #b91c1c !important;
        border-bottom: 4.5px solid #991b1b !important;
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.25) !important;
    }

    .btn-3d-receipt-pdf:hover {
        background: linear-gradient(135deg, #f87171 0%, #ef4444 100%) !important;
        transform: translateY(-1px);
    }

    .btn-3d-receipt-excel {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: #ffffff !important;
        border: 1px solid #047857 !important;
        border-bottom: 4.5px solid #065f46 !important;
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25) !important;
    }

    .btn-3d-receipt-excel:hover {
        background: linear-gradient(135deg, #34d399 0%, #10b981 100%) !important;
        transform: translateY(-1px);
    }

    .btn-3d-receipt-print {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        color: #0f172a !important;
        border: 1px solid #b45309 !important;
        border-bottom: 4.5px solid #78350f !important;
        box-shadow: 0 4px 10px rgba(245, 158, 11, 0.25) !important;
    }

    .btn-3d-receipt-print:hover {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%) !important;
        transform: translateY(-1px);
    }
</style>

<div class="space-y-6">
    <!-- Top Action Bar (no-print) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 no-print">
        <div>
            <a href="{{ route('pagos.index') }}" class="text-xs text-slate-400 hover:text-indigo-400 flex items-center font-medium transition duration-150">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Volver a Historial
            </a>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100 mt-1">Comprobante de Pago</h1>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('pagos.edit', $pago->id) }}" class="btn-3d-receipt inline-flex items-center justify-center px-4 py-2.5 text-xs bg-slate-800 text-amber-400 border border-slate-700 hover:bg-slate-700 font-bold rounded-xl shadow-md transition">
                <i class="fa-solid fa-pen-to-square mr-2 text-sm"></i> Editar Pago
            </a>
            <form action="{{ route('pagos.destroy', $pago->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar este comprobante de pago #{{ $pago->id }}? Al confirmar, los anticipos descontados se restaurarán automáticamente en la cuenta del trabajador.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-3d-receipt inline-flex items-center justify-center px-4 py-2.5 text-xs bg-rose-500/20 text-rose-300 border border-rose-500/30 hover:bg-rose-500/30 font-bold rounded-xl shadow-md transition cursor-pointer" title="Eliminar este Pago">
                    <i class="fa-solid fa-trash mr-2 text-sm"></i> Eliminar
                </button>
            </form>
            <button type="button" onclick="downloadPDF(this)" class="btn-3d-receipt btn-3d-receipt-pdf inline-flex items-center justify-center px-4 py-2.5 text-xs cursor-pointer">
                <i class="fa-solid fa-file-pdf mr-2 text-sm"></i> PDF
            </button>
            <button type="button" onclick="downloadExcel(this)" class="btn-3d-receipt btn-3d-receipt-excel inline-flex items-center justify-center px-4 py-2.5 text-xs cursor-pointer">
                <i class="fa-solid fa-file-excel mr-2 text-sm"></i> Excel
            </button>
            <button type="button" onclick="printThermal80mm(this)" class="btn-3d-receipt inline-flex items-center justify-center px-4 py-2.5 text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-md transition cursor-pointer">
                <i class="fa-solid fa-receipt mr-2 text-sm"></i> Ticket (80mm / 100x148)
            </button>
            <button type="button" onclick="printStandardA4(this)" class="btn-3d-receipt btn-3d-receipt-print inline-flex items-center justify-center px-4 py-2.5 text-xs cursor-pointer">
                <i class="fa-solid fa-print mr-2 text-sm"></i> Hoja (Carta/A4)
            </button>
        </div>
    </div>

    <!-- Mandatory CSS for Print-Proof Vibrant Colors & Zero Margins -->
    <style>
        @media print {
            @page { size: auto; margin: 0mm; }
            body { margin: 0 !important; padding: 0 !important; background-color: #ffffff !important; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }
            #receipt-card { width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; border: none !important; box-shadow: none !important; border-radius: 0 !important; }
            .print-container { width: 100% !important; margin: 0 !important; padding: 0 !important; }
            #receipt-card, #receipt-card *, .print-container, .print-container * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
        }
        #receipt-card * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
    </style>

    <!-- Printable Area (Pure White Container with Light Celeste Palette) -->
    <div id="receipt-card" class="mx-auto max-w-4xl receipt-card-wrapper font-sans text-sm relative bg-white shadow-xl rounded-2xl overflow-hidden" style="border: 2px solid #38bdf8 !important; background-color: #ffffff !important;">
        
        <!-- Inner Container -->
        <div class="print-container bg-white" style="background-color: #ffffff !important;">
            
            <!-- Light Celeste & Sky Blue Header Banner -->
            <div class="p-5 md:p-6 text-white relative" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important; color: #ffffff !important; border-bottom: 3px solid #7dd3fc !important;">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <!-- Mining Company Logo & Brand -->
                    <div class="flex items-center space-x-4">
                        <div class="w-13 h-13 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-md" style="background: #ffffff !important; color: #0284c7 !important; border: 2px solid #7dd3fc !important;">
                            <i class="fa-solid fa-gem text-2xl" style="color: #0284c7 !important;"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-black uppercase tracking-widest leading-none" style="color: #ffffff !important; font-weight: 900;">EMPRESA MINERA</h2>
                            <div class="flex items-center gap-2 mt-1.5">
                                <span class="text-[10.5px] font-mono font-bold tracking-wider uppercase px-3 py-0.5 rounded-full" style="background-color: rgba(255, 255, 255, 0.2) !important; color: #ffffff !important; border: 1px solid #7dd3fc !important;">
                                    <i class="fa-solid fa-shield-halved mr-1"></i> CONTROL OPERATIVO MINERO
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Document Title & Serial Badge -->
                    <div class="text-center md:text-right">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-lg shadow-sm" style="background-color: #ffffff !important; color: #0369a1 !important; border: 1.5px solid #7dd3fc !important;">
                            <span class="text-xs font-black uppercase tracking-widest" style="color: #0369a1 !important;">COMPROBANTE DE PAGO Nº</span>
                            <span class="text-lg font-black font-mono" style="color: #0284c7 !important;">{{ str_pad($pago->id, 5, '0', STR_PAD_LEFT) }}</span>
                            @if($pago->es_editado)
                                <span class="px-1.5 py-0.2 rounded text-[9px] font-black uppercase" style="background-color: #0284c7 !important; color: #ffffff !important;">EDITADO</span>
                            @endif
                        </div>
                        <p class="text-[11px] font-mono mt-1.5 font-bold" style="color: #e0f2fe !important;">
                            Fecha: <strong style="color: #ffffff !important;">{{ $pago->fecha->format('d/m/Y') }}</strong> • Hora: <strong style="color: #ffffff !important;">{{ $pago->created_at->format('H:i:s') }}</strong>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Receipt Content Body (Clean & Soft Light Celeste Tonal Grid) -->
            <div class="p-5 md:p-6 space-y-4" style="background-color: #ffffff !important;">

                <!-- 2-Column Section: Metadata (Left) & Financial Card (Right) -->
                <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: stretch;">
                    
                    <!-- Left (7 Cols): Bocamina & Beneficiario Cards -->
                    <div style="flex: 1 1 55%; min-width: 280px;" class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Bocamina Box -->
                            <div class="rounded-xl p-3 flex items-center space-x-3" style="background-color: #f0f9ff !important; border: 1.5px solid #7dd3fc !important;">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-base flex-shrink-0" style="background-color: #0284c7 !important; color: #ffffff !important;">
                                    <i class="fa-solid fa-mountain"></i>
                                </div>
                                <div>
                                    <span class="text-[9.5px] font-black uppercase tracking-wider block" style="color: #0369a1 !important;">BOCAMINA DE ORIGEN</span>
                                    <span class="font-black uppercase text-xs font-sans leading-tight block" style="color: #0f172a !important;">{{ $pago->trabajador->bocamina->nombre ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <!-- Beneficiario Box -->
                            <div class="rounded-xl p-3 flex items-center space-x-3" style="background-color: #f0f9ff !important; border: 1.5px solid #7dd3fc !important;">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-base flex-shrink-0" style="background-color: #0369a1 !important; color: #ffffff !important;">
                                    <i class="fa-solid fa-user-gear"></i>
                                </div>
                                <div>
                                    <span class="text-[9.5px] font-black uppercase tracking-wider block" style="color: #0369a1 !important;">CONTRATISTA / BENEFICIARIO</span>
                                    <span class="font-black uppercase text-xs font-sans leading-tight block" style="color: #0f172a !important;">{{ $pago->trabajador->nombre }}</span>
                                    <span class="text-[10px] font-mono font-bold block mt-0.5" style="color: #0284c7 !important;">C.I.: {{ $pago->trabajador->ci }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Recibí de Banner -->
                        <div class="rounded-lg p-2.5 text-xs" style="background-color: #f8fafc !important; border: 1px solid #e2e8f0 !important;">
                            <div class="flex items-center justify-between text-[11px] font-mono">
                                <span style="color: #334155 !important;">Recibí de: <strong uppercase style="color: #0f172a !important; font-weight: 900;">ADMINISTRACIÓN CENTRAL / CAJA MINERA</strong></span>
                                <span style="color: #0369a1 !important; font-weight: 700;">(por: {{ $pago->entregado_por ?? 'Administración' }})</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right (5 Cols): Financial Executive Card (Light Celeste Gradient) -->
                    <div style="flex: 1 1 40%; min-width: 240px;">
                        <div class="h-full rounded-xl p-4 shadow-sm flex flex-col justify-between" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important; border: 2px solid #7dd3fc !important; color: #ffffff !important;">
                            <div class="flex justify-between items-center pb-2" style="border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;">
                                <span class="text-[10.5px] font-black uppercase tracking-wider" style="color: #e0f2fe !important;">NETO A CANCELAR (Bs.)</span>
                                <span class="text-[9.5px] font-mono px-2 py-0.5 rounded font-bold uppercase" style="background-color: #ffffff !important; color: #0284c7 !important;">Bolivianos</span>
                            </div>
                            <div class="text-right py-1">
                                <div class="text-2xl md:text-3xl font-black font-mono tracking-tight" style="color: #ffffff !important; font-weight: 900;">
                                    Bs. {{ number_format($pago->neto, 2, ',', '.') }}
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-[10.5px] font-mono pt-1.5" style="border-top: 1px solid rgba(255, 255, 255, 0.2) !important; color: #e0f2fe !important;">
                                <span>Equiv $us: <strong style="color: #ffffff !important;">$us {{ number_format($pago->neto / ($pago->tipo_cambio > 0 ? $pago->tipo_cambio : 6.96), 2, ',', '.') }}</strong></span>
                                <span>T/C: <strong style="color: #ffffff !important;">Bs. {{ number_format($pago->tipo_cambio > 0 ? $pago->tipo_cambio : 6.96, 2, ',', '.') }}</strong></span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Amount in Words & Concept Card -->
                <div class="rounded-xl p-3.5 space-y-2 text-xs" style="background-color: #f0f9ff !important; border: 1.5px solid #7dd3fc !important;">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-1 sm:space-y-0 sm:space-x-3">
                        <span class="text-xs font-black uppercase tracking-wider w-28 flex-shrink-0" style="color: #0369a1 !important;">La suma de:</span>
                        <div class="flex-grow font-black font-mono px-3 py-1 rounded-lg uppercase text-xs" style="background-color: #ffffff !important; border: 1px solid #38bdf8 !important; color: #0f172a !important;">
                            {{ montoEnLetrasOficial($pago->neto) }}
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-start space-y-1 sm:space-y-0 sm:space-x-3">
                        <span class="text-xs font-black uppercase tracking-wider w-28 flex-shrink-0 pt-0.5" style="color: #0369a1 !important;">Por concepto de:</span>
                        <div class="flex-grow font-bold uppercase leading-snug text-xs" style="color: #0f172a !important;">
                            PLANILLA DE PAGO: <span class="font-mono font-black" style="color: #0284c7 !important;">{{ number_format($pago->cantidad_trabajada, 2) }}</span> UNIDADES DE <span class="font-mono font-black" style="color: #0284c7 !important;">{{ $pago->tipo_contrato_nombre }}</span> A TARIFA DE <span class="font-mono font-black" style="color: #0284c7 !important;">Bs. {{ number_format($pago->tarifa_pago, 2) }}</span>
                            @if($pago->observacion)
                                <span class="font-medium normal-case font-mono block mt-1 p-1.5 rounded" style="background-color: #ffffff !important; border: 1px solid #cbd5e1 !important; color: #475569 !important;">
                                    <i class="fa-solid fa-pen-nib mr-1 text-slate-400"></i> {{ $pago->observacion }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Form of Payment Checkboxes (Light Celeste Strip) -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2.5 px-4 rounded-xl text-white shadow-xs" style="background: linear-gradient(90deg, #0284c7 0%, #0369a1 100%) !important; border: 1.5px solid #7dd3fc !important; color: #ffffff !important;">
                    <div class="flex flex-wrap items-center gap-6">
                        <span class="text-xs font-black uppercase tracking-widest" style="color: #ffffff !important;">Forma de Pago:</span>
                        <div class="flex items-center space-x-2">
                            <span class="w-5 h-5 inline-flex items-center justify-center rounded text-xs font-black" style="background-color: #ffffff !important; color: #0284c7 !important;">✓</span>
                            <span class="text-xs font-bold" style="color: {{ $pago->metodo_pago === 'efectivo' ? '#ffffff' : '#e0f2fe' }} !important;">Efectivo</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-5 h-5 inline-flex items-center justify-center rounded text-xs font-black" style="background-color: #ffffff !important; color: #0284c7 !important;">✓</span>
                            <span class="text-xs font-bold" style="color: {{ $pago->metodo_pago === 'cheque' ? '#ffffff' : '#e0f2fe' }} !important;">Cheque</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-5 h-5 inline-flex items-center justify-center rounded text-xs font-black" style="background-color: #ffffff !important; color: #0284c7 !important;">✓</span>
                            <span class="text-xs font-bold" style="color: {{ $pago->metodo_pago === 'transferencia' ? '#ffffff' : '#e0f2fe' }} !important;">Transferencia Bancaria</span>
                        </div>
                    </div>
                    <div class="text-[10.5px] font-mono" style="color: #e0f2fe !important;">
                        <span>Moneda: Bolivianos (Bs.)</span>
                    </div>
                </div>

                <!-- Detailed Pay Breakdown Table (Clean Light Celeste Style) -->
                <div class="rounded-xl overflow-hidden" style="border: 1.5px solid #0284c7 !important;">
                    <div class="px-4 py-2 font-extrabold uppercase tracking-wider text-xs flex justify-between items-center text-white" style="background: linear-gradient(90deg, #0284c7 0%, #0369a1 100%) !important;">
                        <span class="flex items-center" style="color: #ffffff !important;"><i class="fa-solid fa-list-check mr-2" style="color: #ffffff !important;"></i> Desglose Detallado de Liquidación de Planilla</span>
                        <span class="text-[9.5px] font-mono px-2 py-0.5 rounded uppercase font-bold" style="background-color: #ffffff !important; color: #0284c7 !important;">Valores Oficiales</span>
                    </div>
                    <table class="w-full text-xs text-left">
                        <thead>
                            <tr class="font-black uppercase text-white" style="background-color: #0369a1 !important; color: #ffffff !important;">
                                <th class="px-4 py-2" style="color: #ffffff !important;">Concepto / Detalle de Trabajo</th>
                                <th class="px-4 py-2 text-right w-48" style="color: #ffffff !important;">Monto (Bs.)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 font-mono text-slate-900">
                            <tr style="background-color: #ffffff !important;">
                                <td class="px-4 py-2.5 font-sans">
                                    <strong class="text-xs" style="color: #0f172a !important;">{{ $pago->tipo_contrato_nombre }}</strong>
                                    <span class="block text-[11px] font-mono mt-0.5" style="color: #475569 !important;">
                                        {{ number_format($pago->cantidad_trabajada, 2, ',', '.') }} unidades × Bs. {{ number_format($pago->tarifa_pago, 2, ',', '.') }} c/u
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-right font-black text-sm" style="color: #0f172a !important;">Bs. {{ number_format($pago->subtotal, 2, ',', '.') }}</td>
                            </tr>
                            @if($pago->bonos > 0)
                            <tr style="background-color: #f0f9ff !important;">
                                <td class="px-4 py-2 font-sans font-bold flex items-center" style="color: #0369a1 !important;">
                                    <span class="px-2 py-0.5 rounded text-[9.5px] font-black mr-2" style="background-color: #e0f2fe !important; color: #0284c7 !important; border: 1px solid #7dd3fc !important;">(+) ADICIONAL</span>
                                    Bonos y Reconocimientos
                                </td>
                                <td class="px-4 py-2 text-right font-black" style="color: #0284c7 !important;">Bs. {{ number_format($pago->bonos, 2, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if($pago->descuentos > 0)
                            <tr style="background-color: #f8fafc !important;">
                                <td class="px-4 py-2 font-sans font-bold flex items-center" style="color: #334155 !important;">
                                    <span class="px-2 py-0.5 rounded text-[9.5px] font-black mr-2" style="background-color: #e2e8f0 !important; color: #475569 !important; border: 1px solid #cbd5e1 !important;">(-) DESCUENTO</span>
                                    Descuentos Generales
                                </td>
                                <td class="px-4 py-2 text-right font-black" style="color: #475569 !important;">-Bs. {{ number_format($pago->descuentos, 2, ',', '.') }}</td>
                            </tr>
                            @endif
                            @if($pago->anticipos_descontados > 0)
                            <tr style="background-color: #f0f9ff !important;">
                                <td class="px-4 py-2 font-sans font-bold flex items-center" style="color: #0369a1 !important;">
                                    <span class="px-2 py-0.5 rounded text-[9.5px] font-black mr-2" style="background-color: #bae6fd !important; color: #0369a1 !important; border: 1px solid #38bdf8 !important;">(-) ANTICIPO</span>
                                    Anticipos Previos Descontados
                                </td>
                                <td class="px-4 py-2 text-right font-black" style="color: #0369a1 !important;">-Bs. {{ number_format($pago->anticipos_descontados, 2, ',', '.') }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                    <!-- Grand Total Banner (Light Celeste Gradient) -->
                    <div class="p-3.5 flex justify-between items-center" style="background: linear-gradient(90deg, #0284c7 0%, #0369a1 100%) !important; color: #ffffff !important;">
                        <div>
                            <span class="text-[10.5px] font-black uppercase tracking-widest block" style="color: #ffffff !important;">TOTAL NETO LIQUIDADO Y ENTREGADO</span>
                            <span class="text-[9.5px] font-mono" style="color: #e0f2fe !important;">Monto final cancelado en caja al contratista</span>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl md:text-3xl font-black font-mono tracking-tight" style="color: #ffffff !important; font-weight: 900;">
                                Bs. {{ number_format($pago->neto, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Signatures & Audit Seal Block -->
                <div class="pt-6" style="border-top: 2px dashed #cbd5e1 !important;">
                    <div class="grid grid-cols-2 gap-10 text-center text-xs mb-3">
                        <!-- Beneficiary Signature -->
                        <div class="flex flex-col items-center">
                            <div class="w-52 mb-1.5" style="border-bottom: 2px solid #0284c7 !important;"></div>
                            <span class="font-black uppercase text-xs leading-tight" style="color: #0f172a !important; font-weight: 900;">{{ $pago->trabajador->nombre }}</span>
                            <span class="text-[9.5px] uppercase tracking-widest font-mono font-bold mt-0.5" style="color: #475569 !important;">FIRMA RECIBÍ CONFORME (CONTRATISTA)</span>
                            <span class="text-[9px] font-mono mt-0.5" style="color: #64748b !important;">C.I.: {{ $pago->trabajador->ci }}</span>
                        </div>

                        <!-- Cashier Signature -->
                        <div class="flex flex-col items-center">
                            <div class="w-52 mb-1.5" style="border-bottom: 2px solid #0284c7 !important;"></div>
                            <span class="font-black uppercase text-xs leading-tight" style="color: #0f172a !important; font-weight: 900;">{{ $pago->entregado_por ?? 'ADMINISTRADOR MINERO' }}</span>
                            <span class="text-[9.5px] uppercase tracking-widest font-mono font-bold mt-0.5" style="color: #475569 !important;">FIRMA ENTREGUÉ CONFORME (CAJA MINERA)</span>
                        </div>
                    </div>

                    <!-- Official Verification Watermark Badge -->
                    <div class="text-center mt-3">
                        <span class="inline-flex items-center text-[9.5px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-xs" style="background-color: #e0f2fe !important; border: 1.5px solid #0284c7 !important; color: #0369a1 !important;">
                            <i class="fa-solid fa-circle-check text-sky-600 mr-1.5 text-xs"></i> COMPROBANTE OFICIAL REGISTRADO Y VERIFICADO — SCPM CONTROL MINERO
                        </span>
                    </div>
                </div>

                <!-- Branding Footer -->
                <div class="flex justify-between items-center text-[9px] border-t pt-2 font-mono" style="border-top: 1px solid #e2e8f0 !important; color: #64748b !important;">
                    <div class="flex items-center font-bold" style="color: #334155 !important;">
                        <i class="fa-solid fa-building-columns mr-1.5 text-slate-600"></i> CONTROL OPERATIVO MINERO
                    </div>
                    <div class="text-right">
                        <span class="text-[8.5px] font-bold" style="color: #64748b !important;">SCPM v2.5 — Sistema de Control de Pagos Mineros</span>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- ══════════ TICKET IMPRESIÓN TÉRMICA 80MM / 100x148MM ══════════ -->
    <div id="thermal-ticket-80mm">
        <div style="text-align: center; margin-bottom: 6px;">
            <div style="font-weight: 900; font-size: 13px; text-transform: uppercase;">EMPRESA MINERA</div>
            <div style="font-size: 10px; font-weight: 700; text-transform: uppercase;">RECIBO DE PAGO DE PERSONAL</div>
            <div style="font-size: 10px; font-weight: bold; margin-top: 2px;">N.º {{ str_pad($pago->id, 5, '0', STR_PAD_LEFT) }}</div>
            <div style="font-size: 9.5px;">Fecha: {{ $pago->fecha->format('d/m/Y') }}</div>
        </div>

        <div style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 4px 0; margin-bottom: 6px; font-size: 10px;">
            <div><strong>TRABAJADOR:</strong> {{ strtoupper($pago->trabajador->nombre) }}</div>
            <div><strong>C.I.:</strong> {{ $pago->trabajador->ci }}</div>
            <div><strong>BOCAMINA:</strong> {{ strtoupper($pago->trabajador->bocamina->nombre ?? 'N/A') }}</div>
        </div>

        <div style="font-weight: bold; font-size: 9.5px; margin-bottom: 3px;">DETALLE DE TRABAJO:</div>
        <table style="width: 100%; font-size: 9.5px; border-collapse: collapse; margin-bottom: 6px;">
            <thead>
                <tr style="border-bottom: 1px solid #000; text-align: left;">
                    <th style="padding: 2px 0;">Concepto</th>
                    <th style="padding: 2px 0; text-align: center;">Cant</th>
                    <th style="padding: 2px 0; text-align: right;">P.U.</th>
                    <th style="padding: 2px 0; text-align: right; padding-right: 4px;">Subt.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pago->items as $item)
                <tr>
                    <td style="padding: 2px 0;">{{ $item->tipo_trabajo }}</td>
                    <td style="padding: 2px 0; text-align: center;">{{ number_format($item->cantidad, 2) }}</td>
                    <td style="padding: 2px 0; text-align: right;">{{ number_format($item->precio_unitario, 2) }}</td>
                    <td style="padding: 2px 0; text-align: right; font-weight: bold; padding-right: 4px;">Bs. {{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="border-top: 1px solid #000; padding-top: 4px; margin-bottom: 6px; font-size: 10px; line-height: 1.3; padding-right: 4px;">
            <div style="display: flex; justify-content: space-between;">
                <span>Subtotal Bruto:</span>
                <span>Bs. {{ number_format($pago->subtotal_items, 2) }}</span>
            </div>
            @if($pago->bonos > 0)
            <div style="display: flex; justify-content: space-between;">
                <span>Bonos / Adic.:</span>
                <span>Bs. {{ number_format($pago->bonos, 2) }}</span>
            </div>
            @endif
            @if($pago->descuentos > 0)
            <div style="display: flex; justify-content: space-between;">
                <span>Descuentos:</span>
                <span>-Bs. {{ number_format($pago->descuentos, 2) }}</span>
            </div>
            @endif
            @if($pago->anticipos_descontados > 0)
            <div style="display: flex; justify-content: space-between;">
                <span>Anticipos Desc.:</span>
                <span>-Bs. {{ number_format($pago->anticipos_descontados, 2) }}</span>
            </div>
            @endif
            <div style="display: flex; justify-content: space-between; font-weight: 900; font-size: 11.5px; margin-top: 3px; border-top: 1px solid #000; padding-top: 2px;">
                <span>NETO A PAGAR:</span>
                <span>Bs. {{ number_format($pago->neto, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 10.5px;">
                <span>DÓLARES ($us):</span>
                <span>$us {{ number_format($pago->neto / ($pago->tipo_cambio > 0 ? $pago->tipo_cambio : 6.96), 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 9.5px; color: #333;">
                <span>Tipo Cambio (T/C):</span>
                <span>Bs. {{ number_format($pago->tipo_cambio > 0 ? $pago->tipo_cambio : 6.96, 2) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 9.5px; font-weight: bold; margin-top: 2px;">
                <span>Efectivo Entregado:</span>
                <span>Bs. {{ number_format($pago->monto_pagado, 2) }}</span>
            </div>
        </div>

        <div style="font-size: 8.5px; font-weight: bold; text-transform: uppercase; border-top: 1px dashed #000; padding: 3px 0; margin-bottom: 12px;">
            {{ montoEnLetrasOficial($pago->neto) }}
        </div>

        <div style="margin-top: 20px; text-align: center;">
            <div style="border-top: 1px solid #000; width: 75%; margin: 0 auto 2px auto;"></div>
            <div style="font-size: 9.5px; font-weight: bold;">{{ strtoupper($pago->trabajador->nombre) }}</div>
            <div style="font-size: 8.5px;">RECIBÍ CONFORME (CONTRATISTA)</div>
        </div>

        <div style="margin-top: 20px; text-align: center;">
            <div style="border-top: 1px solid #000; width: 75%; margin: 0 auto 2px auto;"></div>
            <div style="font-size: 9.5px; font-weight: bold;">{{ strtoupper($pago->entregado_por ?? 'Administración') }}</div>
            <div style="font-size: 8.5px;">ENTREGUÉ CONFORME (CAJA)</div>
        </div>

        <div style="text-align: center; margin-top: 12px; font-size: 8.5px; border-top: 1px dashed #000; padding-top: 4px;">
            *** Impreso desde Sistema de Pagos ***
        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- Load html2pdf and SheetJS (XLSX) from CDNs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
    function cleanupPrintModes() {
        document.body.classList.remove('thermal-print-mode');
        if (typeof window.hideProcessingOverlay === 'function') {
            window.hideProcessingOverlay();
        }
    }

    window.addEventListener('afterprint', cleanupPrintModes);
    window.addEventListener('focus', cleanupPrintModes);

    function printThermal80mm(btn) {
        document.body.classList.add('thermal-print-mode');
        window.print();
        setTimeout(cleanupPrintModes, 1200);
    }

    function printStandardA4(btn) {
        document.body.classList.remove('thermal-print-mode');
        window.print();
        setTimeout(cleanupPrintModes, 500);
    }

    function downloadPDF(btn) {
        const element = document.getElementById('receipt-card');
        if (!element) return;
        
        const originalText = btn ? btn.innerHTML : '';
        if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Generando PDF...';
            btn.disabled = true;
        }

        const restoreBtn = () => {
            if (btn) {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
            cleanupPrintModes();
        };

        const opt = {
            margin:       [0.2, 0.2, 0.2, 0.2],
            filename:     'Recibo_Pago_Nro_' + '{{ str_pad($pago->id, 5, "0", STR_PAD_LEFT) }}' + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 1.8, useCORS: true, letterRendering: true, backgroundColor: '#ffffff', scrollX: 0, scrollY: 0 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };

        const safetyTimer = setTimeout(() => {
            restoreBtn();
        }, 5000);

        try {
            if (typeof html2pdf !== 'undefined') {
                html2pdf().set(opt).from(element).save().then(() => {
                    clearTimeout(safetyTimer);
                    restoreBtn();
                }).catch(err => {
                    console.error('html2pdf error:', err);
                    clearTimeout(safetyTimer);
                    restoreBtn();
                    printStandardA4();
                });
            } else {
                clearTimeout(safetyTimer);
                restoreBtn();
                printStandardA4();
            }
        } catch (e) {
            clearTimeout(safetyTimer);
            restoreBtn();
            printStandardA4();
        }
    }

    function downloadExcel() {
        const htmlContent = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta charset="utf-8">
                <style>
                    body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #1e293b; }
                    .header-banner { background-color: #0f172a; color: #fbbf24; font-size: 15px; font-weight: bold; text-align: center; padding: 12px; }
                    .info-header { background-color: #1e293b; color: #ffffff; font-size: 11px; font-weight: bold; padding: 8px 12px; }
                    .th-head { background-color: #059669; color: #ffffff; font-size: 11px; font-weight: bold; padding: 8px; text-align: center; border: 1px solid #047857; }
                    .td-cell { border: 1px solid #cbd5e1; padding: 7px 10px; }
                    .td-num { border: 1px solid #cbd5e1; padding: 7px 10px; text-align: right; font-family: Consolas, monospace; }
                    .total-cell { background-color: #ecfdf5; font-size: 12px; font-weight: bold; color: #065f46; border: 1px solid #059669; }
                </style>
            </head>
            <body>
                <table style="width:100%; border-collapse:collapse;">
                    <tr><td colspan="4" class="header-banner">EMPRESA MINERA — COMPROBANTE DE PAGO DE PERSONAL</td></tr>
                    <tr>
                        <td class="info-header" colspan="2">RECIBO N.º: {{ str_pad($pago->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="info-header" colspan="2" style="text-align:right;">FECHA: {{ $pago->fecha->format('d/m/Y') }}</td>
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Trabajador / Contratista:</td><td class="td-cell" colspan="3"><strong>{{ $pago->trabajador->nombre }}</strong></td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Cédula de Identidad (C.I.):</td><td class="td-cell" colspan="3">{{ $pago->trabajador->ci }}</td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Bocamina:</td><td class="td-cell" colspan="3">{{ $pago->trabajador->bocamina->nombre ?? 'N/A' }}</td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Método de Pago:</td><td class="td-cell" colspan="3">{{ strtoupper($pago->metodo_pago) }}</td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Entregado Por:</td><td class="td-cell" colspan="3">{{ $pago->entregado_por ?? 'Administración General' }}</td></tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr><td colspan="4" class="info-header">DETALLE DE TRABAJOS REALIZADOS</td></tr>
                    <tr>
                        <th class="th-head" style="width:40%;">Concepto / Tipo de Trabajo</th>
                        <th class="th-head" style="width:20%;">Cantidad</th>
                        <th class="th-head" style="width:20%;">Precio Unit. (Bs.)</th>
                        <th class="th-head" style="width:20%;">Subtotal (Bs.)</th>
                    </tr>
                    @foreach($pago->items as $item)
                    <tr>
                        <td class="td-cell">{{ $item->tipo_trabajo }}</td>
                        <td class="td-num">{{ number_format($item->cantidad, 2) }}</td>
                        <td class="td-num">Bs. {{ number_format($item->precio_unitario, 2) }}</td>
                        <td class="td-num" style="font-weight:bold;">Bs. {{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr><td colspan="4" class="info-header">RESUMEN Y LIQUIDACIÓN FINANCIERA</td></tr>
                    <tr><td class="td-cell" colspan="3">Subtotal Bruto de Ítems:</td><td class="td-num">Bs. {{ number_format($pago->subtotal_items, 2) }}</td></tr>
                    @if($pago->bonos > 0)
                    <tr><td class="td-cell" colspan="3" style="color:#059669;">Bonos / Adicionales (+):</td><td class="td-num" style="color:#059669;">Bs. {{ number_format($pago->bonos, 2) }}</td></tr>
                    @endif
                    @if($pago->descuentos > 0)
                    <tr><td class="td-cell" colspan="3" style="color:#dc2626;">Descuentos General (-):</td><td class="td-num" style="color:#dc2626;">-Bs. {{ number_format($pago->descuentos, 2) }}</td></tr>
                    @endif
                    @if($pago->anticipos_descontados > 0)
                    <tr><td class="td-cell" colspan="3" style="color:#dc2626;">Anticipos Descontados (-):</td><td class="td-num" style="color:#dc2626;">-Bs. {{ number_format($pago->anticipos_descontados, 2) }}</td></tr>
                    @endif
                    <tr><td class="td-cell total-cell" colspan="3">NETO LIQUIDADO A PAGAR (Bs.):</td><td class="td-num total-cell">Bs. {{ number_format($pago->neto, 2) }}</td></tr>
                    <tr><td class="td-cell" colspan="3" style="font-weight:bold;">EQUIVALENTE EN DÓLARES ($us):</td><td class="td-num" style="font-weight:bold;">$us {{ number_format($pago->neto / ($pago->tipo_cambio > 0 ? $pago->tipo_cambio : 6.96), 2) }}</td></tr>
                    <tr><td class="td-cell" colspan="3" style="font-size:10px; color:#64748b;">Tipo de Cambio Aplicado (T/C):</td><td class="td-num" style="font-size:10px;">Bs. {{ number_format($pago->tipo_cambio > 0 ? $pago->tipo_cambio : 6.96, 2) }}</td></tr>
                    <tr><td class="td-cell" colspan="3" style="font-weight:bold; background:#f8fafc;">Efectivo Real Entregado:</td><td class="td-num" style="font-weight:bold;">Bs. {{ number_format($pago->monto_pagado, 2) }}</td></tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Monto en Letras:</td><td class="td-cell" colspan="3"><strong>{{ montoEnLetrasOficial($pago->neto) }}</strong></td></tr>
                </table>
            </body>
            </html>
        `;

        const blob = new Blob(['\ufeff' + htmlContent], { type: 'application/vnd.ms-excel;charset=utf-8' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'Recibo_Pago_Nro_' + '{{ str_pad($pago->id, 5, "0", STR_PAD_LEFT) }}' + '.xls';
        document.body.appendChild(link);
        link.click();
        setTimeout(() => {
            if (link.parentNode) link.parentNode.removeChild(link);
            cleanupPrintModes();
        }, 200);
    }
</script>
@endpush
