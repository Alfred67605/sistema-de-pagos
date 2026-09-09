@extends('layouts.app')

@section('title', 'Vale de Anticipo #' . str_pad($anticipo->id, 5, '0', STR_PAD_LEFT))

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
                return $decenas[$d] . ($u > 0 ? ' Y ' . $numALetras($u) : '');
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
            <a href="{{ route('anticipos.index') }}" class="text-xs text-slate-400 hover:text-indigo-400 flex items-center font-medium transition duration-150">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Volver a Anticipos
            </a>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100 mt-1">Comprobante de Anticipo</h1>
        </div>
        <div class="flex flex-wrap gap-3">
            <form action="{{ route('anticipos.destroy', $anticipo->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Está seguro de eliminar este vale de anticipo #{{ $anticipo->id }}? Al confirmar, el saldo prestado se restaurará automáticamente en la Caja Personal.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-3d-receipt inline-flex items-center justify-center px-4 py-2.5 text-xs bg-rose-500/20 text-rose-300 border border-rose-500/30 hover:bg-rose-500/30 font-bold rounded-xl shadow-md transition cursor-pointer" title="Eliminar este Anticipo">
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
                                    <i class="fa-solid fa-hand-holding-dollar mr-1"></i> VALE DE ANTICIPO EN EFECTIVO
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Document Title & Serial Badge -->
                    <div class="text-center md:text-right">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-lg shadow-sm" style="background-color: #ffffff !important; color: #0369a1 !important; border: 1.5px solid #7dd3fc !important;">
                            <span class="text-xs font-black uppercase tracking-widest" style="color: #0369a1 !important;">VALE DE ANTICIPO Nº</span>
                            <span class="text-lg font-black font-mono" style="color: #0284c7 !important;">{{ str_pad($anticipo->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <p class="text-[11px] font-mono mt-1.5 font-bold" style="color: #e0f2fe !important;">
                            Fecha: <strong style="color: #ffffff !important;">{{ $anticipo->fecha->format('d/m/Y') }}</strong> • Hora: <strong style="color: #ffffff !important;">{{ $anticipo->created_at->format('H:i:s') }}</strong>
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
                                    <span class="font-black uppercase text-xs font-sans leading-tight block" style="color: #0f172a !important;">{{ $anticipo->trabajador->bocamina->nombre ?? 'N/A' }}</span>
                                </div>
                            </div>

                            <!-- Beneficiario Box -->
                            <div class="rounded-xl p-3 flex items-center space-x-3" style="background-color: #f0f9ff !important; border: 1.5px solid #7dd3fc !important;">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-base flex-shrink-0" style="background-color: #0369a1 !important; color: #ffffff !important;">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div>
                                    <span class="text-[9.5px] font-black uppercase tracking-wider block" style="color: #0369a1 !important;">TRABAJADOR BENEFICIARIO</span>
                                    <span class="font-black uppercase text-xs font-sans leading-tight block" style="color: #0f172a !important;">{{ $anticipo->trabajador->nombre }}</span>
                                    <span class="text-[10px] font-mono font-bold block mt-0.5" style="color: #0284c7 !important;">C.I.: {{ $anticipo->trabajador->ci }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Recibí de Banner -->
                        <div class="rounded-lg p-2.5 text-xs" style="background-color: #f8fafc !important; border: 1px solid #e2e8f0 !important;">
                            <div class="flex items-center justify-between text-[11px] font-mono">
                                <span style="color: #334155 !important;">Recibí de: <strong uppercase style="color: #0f172a !important; font-weight: 900;">ADMINISTRACIÓN CENTRAL / CAJA CHICA MINERA</strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Right (5 Cols): Financial Executive Card (Light Celeste Gradient) -->
                    <div style="flex: 1 1 40%; min-width: 240px;">
                        <div class="h-full rounded-xl p-4 shadow-sm flex flex-col justify-between" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important; border: 2px solid #7dd3fc !important; color: #ffffff !important;">
                            <div class="flex justify-between items-center pb-2" style="border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;">
                                <span class="text-[10.5px] font-black uppercase tracking-wider" style="color: #e0f2fe !important;">MONTO ANTICIPADO (Bs.)</span>
                                <span class="text-[9.5px] font-mono px-2 py-0.5 rounded font-bold uppercase" style="background-color: #ffffff !important; color: #0284c7 !important;">Efectivo</span>
                            </div>
                            <div class="text-right py-1">
                                <div class="text-2xl md:text-3xl font-black font-mono tracking-tight" style="color: #ffffff !important; font-weight: 900;">
                                    Bs. {{ number_format($anticipo->monto, 2, ',', '.') }}
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-[10.5px] font-mono pt-1.5" style="border-top: 1px solid rgba(255, 255, 255, 0.2) !important; color: #e0f2fe !important;">
                                <span>Estado: <strong style="color: #ffffff !important;" class="uppercase">{{ $anticipo->saldo == 0 ? 'DESCONTADO' : 'PENDIENTE' }}</strong></span>
                                <span>Saldo: <strong style="color: #ffffff !important;">Bs. {{ number_format($anticipo->saldo, 2, ',', '.') }}</strong></span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Amount in Words & Concept Card -->
                <div class="rounded-xl p-3.5 space-y-2 text-xs" style="background-color: #f0f9ff !important; border: 1.5px solid #7dd3fc !important;">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-1 sm:space-y-0 sm:space-x-3">
                        <span class="text-xs font-black uppercase tracking-wider w-28 flex-shrink-0" style="color: #0369a1 !important;">La cantidad de:</span>
                        <div class="flex-grow font-black font-mono px-3 py-1 rounded-lg uppercase text-xs" style="background-color: #ffffff !important; border: 1px solid #38bdf8 !important; color: #0f172a !important;">
                            {{ montoEnLetrasOficial($anticipo->monto) }}
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-start space-y-1 sm:space-y-0 sm:space-x-3">
                        <span class="text-xs font-black uppercase tracking-wider w-28 flex-shrink-0 pt-0.5" style="color: #0369a1 !important;">Por concepto de:</span>
                        <div class="flex-grow font-bold uppercase leading-snug text-xs" style="color: #0f172a !important;">
                            ANTICIPO DE DINERO A CUENTA DE PLANILLA DE TRABAJO
                            @if($anticipo->observacion)
                                <span class="font-medium normal-case font-mono block mt-1 p-1.5 rounded" style="background-color: #ffffff !important; border: 1px solid #cbd5e1 !important; color: #475569 !important;">
                                    <i class="fa-solid fa-pen-nib mr-1 text-slate-400"></i> {{ $anticipo->observacion }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Form of Payment Checkboxes (Light Celeste Strip) -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between py-2.5 px-4 rounded-xl text-white shadow-xs" style="background: linear-gradient(90deg, #0284c7 0%, #0369a1 100%) !important; border: 1.5px solid #7dd3fc !important; color: #ffffff !important;">
                    <div class="flex flex-wrap items-center gap-6">
                        <span class="text-xs font-black uppercase tracking-widest" style="color: #ffffff !important;">Forma de Entrega:</span>
                        <div class="flex items-center space-x-2">
                            <span class="w-5 h-5 inline-flex items-center justify-center rounded text-xs font-black" style="background-color: #ffffff !important; color: #0284c7 !important;">✓</span>
                            <span class="text-xs font-bold" style="color: #ffffff !important;">Efectivo (Caja Chica)</span>
                        </div>
                    </div>
                    <div class="text-[10.5px] font-mono" style="color: #e0f2fe !important;">
                        <span>Moneda: Bolivianos (Bs.)</span>
                    </div>
                </div>

                <!-- Grand Total Banner (Light Celeste Gradient) -->
                <div class="p-4 rounded-xl flex justify-between items-center shadow-xs" style="background: linear-gradient(90deg, #0284c7 0%, #0369a1 100%) !important; border: 1.5px solid #7dd3fc !important; color: #ffffff !important;">
                    <div>
                        <span class="text-[11px] font-black uppercase tracking-widest block" style="color: #ffffff !important;">TOTAL ANTICIPO ENTREGADO EN CAJA</span>
                        <span class="text-[10px] font-mono" style="color: #e0f2fe !important;">Adelanto sujeto a descuento en liquidación de planilla</span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl md:text-3xl font-black font-mono tracking-tight" style="color: #ffffff !important; font-weight: 900;">
                            Bs. {{ number_format($anticipo->monto, 2, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Signatures & Audit Seal Block -->
                <div class="pt-6" style="border-top: 2px dashed #cbd5e1 !important;">
                    <div class="grid grid-cols-2 gap-10 text-center text-xs mb-3">
                        <!-- Beneficiary Signature -->
                        <div class="flex flex-col items-center">
                            <div class="w-52 mb-1.5" style="border-bottom: 2px solid #0284c7 !important;"></div>
                            <span class="font-black uppercase text-xs leading-tight" style="color: #0f172a !important; font-weight: 900;">{{ $anticipo->trabajador->nombre }}</span>
                            <span class="text-[9.5px] uppercase tracking-widest font-mono font-bold mt-0.5" style="color: #475569 !important;">FIRMA RECIBÍ CONFORME (BENEFICIARIO)</span>
                            <span class="text-[9px] font-mono mt-0.5" style="color: #64748b !important;">C.I.: {{ $anticipo->trabajador->ci }}</span>
                        </div>

                        <!-- Cashier Signature -->
                        <div class="flex flex-col items-center">
                            <div class="w-52 mb-1.5" style="border-bottom: 2px solid #0284c7 !important;"></div>
                            <span class="font-black uppercase text-xs leading-tight" style="color: #0f172a !important; font-weight: 900;">{{ Auth::user()->name ?? 'ADMINISTRADOR MINERO' }}</span>
                            <span class="text-[9.5px] uppercase tracking-widest font-mono font-bold mt-0.5" style="color: #475569 !important;">FIRMA ENTREGUÉ CONFORME (CAJA CHICA)</span>
                        </div>
                    </div>

                    <!-- Official Verification Watermark Badge -->
                    <div class="text-center mt-3">
                        <span class="inline-flex items-center text-[9.5px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow-xs" style="background-color: #e0f2fe !important; border: 1.5px solid #0284c7 !important; color: #0369a1 !important;">
                            <i class="fa-solid fa-circle-check text-sky-600 mr-1.5 text-xs"></i> VALE OFICIAL REGISTRADO Y VERIFICADO — SCPM CONTROL MINERO
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

    <!-- ══════════ VALE IMPRESIÓN TÉRMICA 80MM / 100x148MM ══════════ -->
    <div id="thermal-ticket-80mm">
        <div style="text-align: center; margin-bottom: 6px;">
            <div style="font-weight: 900; font-size: 13px; text-transform: uppercase;">EMPRESA MINERA</div>
            <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; margin-top: 2px;">VALE DE ANTICIPO DE DINERO</div>
            <div style="font-size: 10px; font-weight: bold; margin-top: 2px;">VALE N.º {{ str_pad($anticipo->id, 5, '0', STR_PAD_LEFT) }}</div>
            <div style="font-size: 9.5px; margin-top: 1px;">Fecha: {{ $anticipo->fecha->format('d/m/Y') }}</div>
        </div>

        <div style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 4px 0; margin-bottom: 6px; font-size: 10px;">
            <div><strong>TRABAJADOR:</strong> {{ strtoupper($anticipo->trabajador->nombre) }}</div>
            <div><strong>C.I.:</strong> {{ $anticipo->trabajador->ci }}</div>
            <div><strong>BOCAMINA:</strong> {{ strtoupper($anticipo->trabajador->bocamina->nombre ?? 'N/A') }}</div>
        </div>

        <div style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 4px 0; margin-bottom: 6px; font-size: 11px; font-weight: 900; display: flex; justify-content: space-between; padding-right: 4px;">
            <span>MONTO ANTICIPO:</span>
            <span>Bs. {{ number_format($anticipo->monto, 2) }}</span>
        </div>

        <div style="font-size: 8.5px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px;">
            {{ montoEnLetrasOficial($anticipo->monto) }}
        </div>

        @if($anticipo->observacion)
            <div style="font-size: 9.5px; margin-bottom: 8px;">
                <strong>CONCEPTO / MOTIVO:</strong> {{ $anticipo->observacion }}
            </div>
        @endif

        <div style="margin-top: 20px; text-align: center;">
            <div style="border-top: 1px solid #000; width: 75%; margin: 0 auto 2px auto;"></div>
            <div style="font-size: 9.5px; font-weight: bold;">{{ strtoupper($anticipo->trabajador->nombre) }}</div>
            <div style="font-size: 8.5px;">RECIBÍ CONFORME (TRABAJADOR)</div>
        </div>

        <div style="margin-top: 20px; text-align: center;">
            <div style="border-top: 1px solid #000; width: 75%; margin: 0 auto 2px auto;"></div>
            <div style="font-size: 9.5px; font-weight: bold;">{{ strtoupper(Auth::user()->name ?? 'Administración') }}</div>
            <div style="font-size: 8.5px;">ENTREGUÉ CONFORME (CAJA)</div>
        </div>

        <div style="text-align: center; margin-top: 12px; font-size: 8.5px; border-top: 1px dashed #000; padding-top: 4px;">
            *** Impreso desde Sistema de Pagos ***
        </div>
    </div>

</div>
@endsection

@push('scripts')
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
        const sourceEl = document.getElementById('receipt-card');
        if (!sourceEl) {
            printStandardA4(btn);
            return;
        }

        const oldIframe = document.getElementById('scpm-anticipo-iframe');
        if (oldIframe) oldIframe.remove();

        const iframe = document.createElement('iframe');
        iframe.id = 'scpm-anticipo-iframe';
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = 'none';
        iframe.style.visibility = 'hidden';
        document.body.appendChild(iframe);

        const docHtml = `<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Vale_Anticipo_Nro_{{ str_pad($anticipo->id, 5, '0', STR_PAD_LEFT) }}</title>
    <script src="https://cdn.tailwindcss.com"><\/script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page { size: letter portrait; margin: 4mm 6mm; }
        * { box-sizing: border-box; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }
        body { font-family: 'Outfit', sans-serif; background: #ffffff !important; color: #0f172a !important; margin: 0; padding: 2px; }
        .no-print { display: none !important; }
        #receipt-card { width: 100% !important; max-width: 100% !important; margin: 0 !important; box-shadow: none !important; border: 1.5px solid #10b981 !important; border-radius: 10px !important; overflow: hidden !important; }
        .print-container { width: 100% !important; margin: 0 !important; padding: 0 !important; }
    </style>
</head>
<body class="bg-white">
    ${sourceEl.outerHTML}
</body>
</html>`;

        iframe.contentDocument.open();
        iframe.contentDocument.write(docHtml);
        iframe.contentDocument.close();

        setTimeout(() => {
            try {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            } catch (err) {
                console.warn('Iframe print error, fallback to direct print', err);
                printStandardA4(btn);
            }
            setTimeout(() => {
                if (iframe.parentNode) iframe.remove();
                cleanupPrintModes();
            }, 2500);
        }, 350);
    }

    function downloadExcel(btn) {
        const htmlContent = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta charset="utf-8">
                <style>
                    body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 11px; color: #1e293b; }
                    .header-banner { background-color: #0f172a; color: #fbbf24; font-size: 15px; font-weight: bold; text-align: center; padding: 12px; }
                    .info-header { background-color: #1e293b; color: #ffffff; font-size: 11px; font-weight: bold; padding: 8px 12px; }
                    .td-cell { border: 1px solid #cbd5e1; padding: 7px 10px; }
                    .td-num { border: 1px solid #cbd5e1; padding: 7px 10px; text-align: right; font-family: Consolas, monospace; }
                    .total-cell { background-color: #fef2f2; font-size: 12px; font-weight: bold; color: #991b1b; border: 1px solid #ef4444; }
                </style>
            </head>
            <body>
                <table style="width:100%; border-collapse:collapse;">
                    <tr><td colspan="4" class="header-banner">EMPRESA MINERA — VALE DE ANTICIPO DE DINERO</td></tr>
                    <tr>
                        <td class="info-header" colspan="2">VALE N.º: {{ str_pad($anticipo->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="info-header" colspan="2" style="text-align:right;">FECHA: {{ $anticipo->fecha->format('d/m/Y') }}</td>
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Trabajador / Beneficiario:</td><td class="td-cell" colspan="3"><strong>{{ $anticipo->trabajador->nombre }}</strong></td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Cédula de Identidad (C.I.):</td><td class="td-cell" colspan="3">{{ $anticipo->trabajador->ci }}</td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Bocamina:</td><td class="td-cell" colspan="3">{{ $anticipo->trabajador->bocamina->nombre ?? 'N/A' }}</td></tr>
                    @if($anticipo->observacion)
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Concepto / Motivo:</td><td class="td-cell" colspan="3">{{ $anticipo->observacion }}</td></tr>
                    @endif
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr><td colspan="4" class="info-header">LIQUIDACIÓN DE ANTICIPO</td></tr>
                    <tr><td class="td-cell total-cell" colspan="3">MONTO ANTICIPADO ENTREGADO (Bs.):</td><td class="td-num total-cell">Bs. {{ number_format($anticipo->monto, 2) }}</td></tr>
                    <tr><td class="td-cell" colspan="3" style="font-weight:bold;">Saldo Pendiente por Descontar (Bs.):</td><td class="td-num" style="font-weight:bold; color:#dc2626;">Bs. {{ number_format($anticipo->saldo, 2) }}</td></tr>
                    <tr><td class="td-cell" colspan="3">Estado del Saldo:</td><td class="td-cell" style="text-align:center; font-weight:bold;">{{ $anticipo->saldo == 0 ? 'TOTALMENTE DESCONTADO' : 'PENDIENTE DE DESCUENTO' }}</td></tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Monto en Letras:</td><td class="td-cell" colspan="3"><strong>{{ montoEnLetrasOficial($anticipo->monto) }}</strong></td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Entregado Por:</td><td class="td-cell" colspan="3">{{ Auth::user()->name ?? 'Administración General' }}</td></tr>
                </table>
            </body>
            </html>
        `;

        const blob = new Blob(['\ufeff' + htmlContent], { type: 'application/vnd.ms-excel;charset=utf-8' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'Vale_Anticipo_Nro_' + '{{ str_pad($anticipo->id, 5, "0", STR_PAD_LEFT) }}' + '.xls';
        document.body.appendChild(link);
        link.click();
        setTimeout(() => {
            if (link.parentNode) link.parentNode.removeChild(link);
            cleanupPrintModes();
        }, 200);
    }
</script>
@endpush
