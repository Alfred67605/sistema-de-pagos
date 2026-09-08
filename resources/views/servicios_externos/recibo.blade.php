@extends('layouts.app')

@section('title', 'Comprobante de Servicio Externo #' . str_pad($servicio->id, 5, '0', STR_PAD_LEFT))

@section('content')
@php
if (!function_exists('montoEnLetrasServicio')) {
    function montoEnLetrasServicio($monto) {
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

<!-- Custom Styles for Receipt -->
<style>
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
        background: linear-gradient(90deg, #0284c7, #06b6d4, #10b981) !important;
        z-index: 10 !important;
    }
    #thermal-ticket-80mm { display: none; }
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
    .btn-3d-receipt {
        border-radius: 12px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        transition: all 0.15s ease !important;
        cursor: pointer !important;
    }
</style>

<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 no-print">
        <div>
            <a href="{{ route('servicios-externos.index') }}" class="text-xs text-slate-400 hover:text-sky-400 flex items-center font-medium transition duration-150">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Volver a Historial
            </a>
            <h1 class="text-3xl font-bold tracking-tight text-slate-100 mt-1">Comprobante de Servicio Externo</h1>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('servicios-externos.edit', $servicio->id) }}" class="btn-3d-receipt inline-flex items-center justify-center px-4 py-2.5 text-xs bg-slate-800 text-amber-400 border border-slate-700 hover:bg-slate-700 font-bold rounded-xl shadow-md transition">
                <i class="fa-solid fa-pen-to-square mr-2 text-sm"></i> Editar
            </a>
            <button onclick="downloadPDF()" class="btn-3d-receipt inline-flex items-center justify-center px-4 py-2.5 text-xs bg-rose-600 hover:bg-rose-500 text-white font-bold rounded-xl shadow-md transition">
                <i class="fa-solid fa-file-pdf mr-2 text-sm"></i> PDF
            </button>
            <button onclick="downloadExcel()" class="btn-3d-receipt inline-flex items-center justify-center px-4 py-2.5 text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl shadow-md transition">
                <i class="fa-solid fa-file-excel mr-2 text-sm"></i> Excel
            </button>
            <button onclick="printThermal80mm()" class="btn-3d-receipt inline-flex items-center justify-center px-4 py-2.5 text-xs bg-teal-600 hover:bg-teal-500 text-white font-bold rounded-xl shadow-md transition">
                <i class="fa-solid fa-receipt mr-2 text-sm"></i> Ticket (80mm)
            </button>
            <button onclick="printStandardA4()" class="btn-3d-receipt inline-flex items-center justify-center px-4 py-2.5 text-xs bg-sky-600 text-white hover:bg-sky-500 font-bold rounded-xl shadow-md transition">
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
                                    <i class="fa-solid fa-truck-ramp-box mr-1"></i> SERVICIOS EXTERNOS Y FLETES
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Document Title & Serial Badge -->
                    <div class="text-center md:text-right">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-lg shadow-sm" style="background-color: #ffffff !important; color: #0369a1 !important; border: 1.5px solid #7dd3fc !important;">
                            <span class="text-xs font-black uppercase tracking-widest" style="color: #0369a1 !important;">COMPROBANTE DE SERVICIO Nº</span>
                            <span class="text-lg font-black font-mono" style="color: #0284c7 !important;">{{ str_pad($servicio->id, 5, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <p class="text-[11px] font-mono mt-1.5 font-bold" style="color: #e0f2fe !important;">
                            Fecha: <strong style="color: #ffffff !important;">{{ $servicio->fecha->format('d/m/Y') }}</strong> • Hora: <strong style="color: #ffffff !important;">{{ $servicio->created_at->format('H:i:s') }}</strong>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Receipt Content Body (Clean & Soft Light Celeste Tonal Grid) -->
            <div class="p-5 md:p-6 space-y-4" style="background-color: #ffffff !important;">

                <!-- 2-Column Section: Metadata (Left) & Financial Card (Right) -->
                <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: stretch;">
                    
                    <!-- Left (7 Cols): Chofer & Placa Cards -->
                    <div style="flex: 1 1 55%; min-width: 280px;" class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Chofer Box -->
                            <div class="rounded-xl p-3 flex items-center space-x-3" style="background-color: #f0f9ff !important; border: 1.5px solid #7dd3fc !important;">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-base flex-shrink-0" style="background-color: #0284c7 !important; color: #ffffff !important;">
                                    <i class="fa-solid fa-user-tie"></i>
                                </div>
                                <div>
                                    <span class="text-[9.5px] font-black uppercase tracking-wider block" style="color: #0369a1 !important;">CHOFER / OPERADOR</span>
                                    <span class="font-black uppercase text-xs font-sans leading-tight block" style="color: #0f172a !important;">{{ $servicio->chofer_operador }}</span>
                                </div>
                            </div>

                            <!-- Placa Box -->
                            <div class="rounded-xl p-3 flex items-center space-x-3" style="background-color: #f0f9ff !important; border: 1.5px solid #7dd3fc !important;">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center font-bold text-base flex-shrink-0" style="background-color: #0369a1 !important; color: #ffffff !important;">
                                    <i class="fa-solid fa-truck-monster"></i>
                                </div>
                                <div>
                                    <span class="text-[9.5px] font-black uppercase tracking-wider block" style="color: #0369a1 !important;">PLACA / MAQUINARIA</span>
                                    <span class="font-black uppercase text-xs font-mono px-2 py-0.5 rounded inline-block mt-0.5" style="background-color: #ffffff !important; border: 1px solid #38bdf8 !important; color: #0f172a !important;">{{ $servicio->placa_maquinaria }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Recibí de Banner -->
                        <div class="rounded-lg p-2.5 text-xs" style="background-color: #f8fafc !important; border: 1px solid #e2e8f0 !important;">
                            <div class="flex items-center justify-between text-[11px] font-mono">
                                <span style="color: #334155 !important;">Recibí de: <strong uppercase style="color: #0f172a !important; font-weight: 900;">ADMINISTRACIÓN CENTRAL / CAJA CHICA MINERA</strong></span>
                                <span style="color: #0369a1 !important; font-weight: 700;">(por: {{ $servicio->entregado_por ?? 'Administración' }})</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right (5 Cols): Financial Executive Card (Light Celeste Gradient) -->
                    <div style="flex: 1 1 40%; min-width: 240px;">
                        <div class="h-full rounded-xl p-4 shadow-sm flex flex-col justify-between" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important; border: 2px solid #7dd3fc !important; color: #ffffff !important;">
                            <div class="flex justify-between items-center pb-2" style="border-bottom: 1px solid rgba(255, 255, 255, 0.2) !important;">
                                <span class="text-[10.5px] font-black uppercase tracking-wider" style="color: #e0f2fe !important;">TOTAL MONTO PAGADO (Bs.)</span>
                                <span class="text-[9.5px] font-mono px-2 py-0.5 rounded font-bold uppercase" style="background-color: #ffffff !important; color: #0284c7 !important;">Bolivianos</span>
                            </div>
                            <div class="text-right py-1">
                                <div class="text-2xl md:text-3xl font-black font-mono tracking-tight" style="color: #ffffff !important; font-weight: 900;">
                                    Bs. {{ number_format($servicio->monto_total, 2, ',', '.') }}
                                </div>
                            </div>
                            <div class="flex justify-between items-center text-[10.5px] font-mono pt-1.5" style="border-top: 1px solid rgba(255, 255, 255, 0.2) !important; color: #e0f2fe !important;">
                                <span>Equiv $us: <strong style="color: #ffffff !important;">$us {{ number_format($servicio->monto_total / 6.96, 2, ',', '.') }}</strong></span>
                                <span>T/C: <strong style="color: #ffffff !important;">Bs. 6,96</strong></span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Amount in Words & Concept Card -->
                <div class="rounded-xl p-3.5 space-y-2 text-xs" style="background-color: #f0f9ff !important; border: 1.5px solid #7dd3fc !important;">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-1 sm:space-y-0 sm:space-x-3">
                        <span class="text-xs font-black uppercase tracking-wider w-28 flex-shrink-0" style="color: #0369a1 !important;">La suma de:</span>
                        <div class="flex-grow font-black font-mono px-3 py-1 rounded-lg uppercase text-xs" style="background-color: #ffffff !important; border: 1px solid #38bdf8 !important; color: #0f172a !important;">
                            {{ montoEnLetrasServicio($servicio->monto_total) }}
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-start space-y-1 sm:space-y-0 sm:space-x-3">
                        <span class="text-xs font-black uppercase tracking-wider w-28 flex-shrink-0 pt-0.5" style="color: #0369a1 !important;">Por servicio de:</span>
                        <div class="flex-grow font-bold uppercase leading-snug text-xs" style="color: #0f172a !important;">
                            <span class="font-sans font-black text-sm" style="color: #0284c7 !important;">{{ $servicio->tipo_servicio }}</span> — <span class="font-mono font-black" style="color: #0369a1 !important;">{{ number_format($servicio->cantidad, 2) }} {{ $servicio->unidad_medida }}</span>
                            @if($servicio->precio_unitario > 0)
                                a tarifa de <span class="font-mono font-black" style="color: #0369a1 !important;">Bs. {{ number_format($servicio->precio_unitario, 2) }} c/u</span>
                            @endif
                            @if($servicio->origen_destino)
                                <span class="block text-[11px] font-mono mt-1 p-1.5 rounded" style="background-color: #ffffff !important; border: 1px solid #bae6fd !important; color: #0284c7 !important;">
                                    <i class="fa-solid fa-route mr-1"></i> TRAMO / RUTA: {{ $servicio->origen_destino }}
                                </span>
                            @endif
                            @if($servicio->observacion)
                                <span class="font-medium normal-case font-mono block mt-1 p-1.5 rounded" style="background-color: #ffffff !important; border: 1px solid #cbd5e1 !important; color: #475569 !important;">
                                    <i class="fa-solid fa-pen-nib mr-1 text-slate-400"></i> {{ $servicio->observacion }}
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
                            <span class="text-xs font-bold" style="color: {{ $servicio->metodo_pago === 'efectivo' ? '#ffffff' : '#e0f2fe' }} !important;">Efectivo</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-5 h-5 inline-flex items-center justify-center rounded text-xs font-black" style="background-color: #ffffff !important; color: #0284c7 !important;">✓</span>
                            <span class="text-xs font-bold" style="color: {{ $servicio->metodo_pago === 'cheque' ? '#ffffff' : '#e0f2fe' }} !important;">Cheque</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="w-5 h-5 inline-flex items-center justify-center rounded text-xs font-black" style="background-color: #ffffff !important; color: #0284c7 !important;">✓</span>
                            <span class="text-xs font-bold" style="color: {{ $servicio->metodo_pago === 'transferencia' ? '#ffffff' : '#e0f2fe' }} !important;">Transferencia Bancaria</span>
                        </div>
                    </div>
                    <div class="text-[10.5px] font-mono" style="color: #e0f2fe !important;">
                        <span>Moneda: Bolivianos (Bs.)</span>
                    </div>
                </div>

                <!-- Grand Total Banner (Light Celeste Gradient) -->
                <div class="p-4 rounded-xl flex justify-between items-center shadow-xs" style="background: linear-gradient(90deg, #0284c7 0%, #0369a1 100%) !important; border: 1.5px solid #7dd3fc !important; color: #ffffff !important;">
                    <div>
                        <span class="text-[11px] font-black uppercase tracking-widest block" style="color: #ffffff !important;">TOTAL MONTO CANCELADO POR SERVICIO</span>
                        <span class="text-[10px] font-mono" style="color: #e0f2fe !important;">Pago efectuado por concepto de flete / servicio externo</span>
                    </div>
                    <div class="text-right">
                        <span class="text-2xl md:text-3xl font-black font-mono tracking-tight" style="color: #ffffff !important; font-weight: 900;">
                            Bs. {{ number_format($servicio->monto_total, 2, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Signatures & Audit Seal Block -->
                <div class="pt-6" style="border-top: 2px dashed #cbd5e1 !important;">
                    <div class="grid grid-cols-2 gap-10 text-center text-xs mb-3">
                        <!-- Operator Signature -->
                        <div class="flex flex-col items-center">
                            <div class="w-52 mb-1.5" style="border-bottom: 2px solid #0284c7 !important;"></div>
                            <span class="font-black uppercase text-xs leading-tight" style="color: #0f172a !important; font-weight: 900;">{{ $servicio->chofer_operador }}</span>
                            <span class="text-[9.5px] uppercase tracking-widest font-mono font-bold mt-0.5" style="color: #475569 !important;">FIRMA CHOFER / OPERADOR (RECIBÍ CONFORME)</span>
                            <span class="text-[9px] font-mono mt-0.5" style="color: #64748b !important;">Placa: {{ $servicio->placa_maquinaria }}</span>
                        </div>

                        <!-- Cashier Signature -->
                        <div class="flex flex-col items-center">
                            <div class="w-52 mb-1.5" style="border-bottom: 2px solid #0284c7 !important;"></div>
                            <span class="font-black uppercase text-xs leading-tight" style="color: #0f172a !important; font-weight: 900;">{{ $servicio->entregado_por ?? 'ADMINISTRADOR MINERO' }}</span>
                            <span class="text-[9.5px] uppercase tracking-widest font-mono font-bold mt-0.5" style="color: #475569 !important;">FIRMA ENTREGUÉ CONFORME (CAJA CHICA)</span>
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
            <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; margin-top: 2px;">RECIBO DE SERVICIO EXTERNO</div>
            <div style="font-size: 10px; font-weight: bold; margin-top: 2px;">N.º {{ str_pad($servicio->id, 5, '0', STR_PAD_LEFT) }}</div>
            <div style="font-size: 9.5px; margin-top: 1px;">Fecha: {{ $servicio->fecha->format('d/m/Y') }}</div>
        </div>

        <div style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 4px 0; margin-bottom: 6px; font-size: 10px;">
            <div><strong>CHOFER/OPERADOR:</strong> {{ strtoupper($servicio->chofer_operador) }}</div>
            <div><strong>PLACA/MÁQUINA:</strong> {{ strtoupper($servicio->placa_maquinaria) }}</div>
            @if($servicio->bocamina)
                <div><strong>BOCAMINA:</strong> {{ strtoupper($servicio->bocamina->nombre) }}</div>
            @endif
        </div>

        <div style="font-size: 10px; margin-bottom: 6px; line-height: 1.35;">
            <div><strong>SERVICIO:</strong> {{ $servicio->tipo_servicio }}</div>
            <div><strong>CANTIDAD:</strong> {{ number_format($servicio->cantidad, 2) }} {{ $servicio->unidad_medida }}</div>
            @if($servicio->precio_unitario > 0)
                <div><strong>PRECIO UNIT.:</strong> Bs. {{ number_format($servicio->precio_unitario, 2) }}</div>
            @endif
            @if($servicio->origen_destino)
                <div><strong>TRAMO:</strong> {{ $servicio->origen_destino }}</div>
            @endif
        </div>

        <div style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 4px 0; margin-bottom: 6px; font-size: 11px; font-weight: 900; display: flex; justify-content: space-between; padding-right: 4px;">
            <span>TOTAL PAGADO:</span>
            <span>Bs. {{ number_format($servicio->monto_total, 2) }}</span>
        </div>

        <div style="font-size: 8.5px; font-weight: bold; text-transform: uppercase; margin-bottom: 10px;">
            {{ montoEnLetrasServicio($servicio->monto_total) }}
        </div>

        <div style="margin-top: 20px; text-align: center;">
            <div style="border-top: 1px solid #000; width: 75%; margin: 0 auto 2px auto;"></div>
            <div style="font-size: 9.5px; font-weight: bold;">{{ strtoupper($servicio->chofer_operador) }}</div>
            <div style="font-size: 8.5px;">RECIBÍ CONFORME (CHOFER / OPERADOR)</div>
        </div>

        <div style="margin-top: 20px; text-align: center;">
            <div style="border-top: 1px solid #000; width: 75%; margin: 0 auto 2px auto;"></div>
            <div style="font-size: 9.5px; font-weight: bold;">{{ strtoupper($servicio->entregado_por ?? 'Administración') }}</div>
            <div style="font-size: 8.5px;">ENTREGUÉ CONFORME (CAJA CHICA)</div>
        </div>

        <div style="text-align: center; margin-top: 12px; font-size: 8.5px; border-top: 1px dashed #000; padding-top: 4px;">
            *** Impreso desde Sistema de Pagos ***
        </div>
    </div>

</div>

</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
    function printThermal80mm() {
        document.body.classList.add('thermal-print-mode');
        window.print();
        setTimeout(function() {
            document.body.classList.remove('thermal-print-mode');
        }, 1000);
    }

    function printStandardA4() {
        document.body.classList.remove('thermal-print-mode');
        window.print();
    }

    function downloadPDF() {
        const element = document.getElementById('receipt-card');
        if (!element) return;
        
        const btn = event ? event.currentTarget : null;
        const originalText = btn ? btn.innerHTML : '';
        if (btn) btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Generando PDF...';

        const opt = {
            margin:       [0.2, 0.2, 0.2, 0.2],
            filename:     'Recibo_Servicio_Externo_Nro_' + '{{ str_pad($servicio->id, 5, "0", STR_PAD_LEFT) }}' + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 1.8, useCORS: true, letterRendering: true, backgroundColor: '#ffffff', scrollX: 0, scrollY: 0 },
            jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
        };

        html2pdf().set(opt).from(element).save().then(() => {
            if (btn) btn.innerHTML = originalText;
        }).catch(err => {
            console.error(err);
            if (btn) btn.innerHTML = originalText;
            window.print();
        });
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
                    .td-cell { border: 1px solid #cbd5e1; padding: 7px 10px; }
                    .td-num { border: 1px solid #cbd5e1; padding: 7px 10px; text-align: right; font-family: Consolas, monospace; }
                    .total-cell { background-color: #f0fdf4; font-size: 12px; font-weight: bold; color: #166534; border: 1px solid #22c55e; }
                </style>
            </head>
            <body>
                <table style="width:100%; border-collapse:collapse;">
                    <tr><td colspan="4" class="header-banner">EMPRESA MINERA — COMPROBANTE DE SERVICIO EXTERNO / FLETE</td></tr>
                    <tr>
                        <td class="info-header" colspan="2">N.º CORRELATIVO: {{ str_pad($servicio->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="info-header" colspan="2" style="text-align:right;">FECHA: {{ $servicio->fecha->format('d/m/Y') }}</td>
                    </tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Chofer / Operador / Empresa:</td><td class="td-cell" colspan="3"><strong>{{ $servicio->chofer_operador }}</strong></td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Placa / Maquinaria:</td><td class="td-cell" colspan="3">{{ $servicio->placa_maquinaria }}</td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Bocamina / Origen:</td><td class="td-cell" colspan="3">{{ $servicio->bocamina->nombre ?? 'N/A' }}</td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Categoría de Servicio:</td><td class="td-cell" colspan="3">{{ $servicio->tipo_servicio }}</td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Cantidad:</td><td class="td-num" colspan="3" style="text-align:left;">{{ number_format($servicio->cantidad, 2) }} {{ $servicio->unidad_medida }}</td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Precio Unitario (Bs.):</td><td class="td-num" colspan="3" style="text-align:left;">Bs. {{ number_format($servicio->precio_unitario, 2) }}</td></tr>
                    @if($servicio->origen_destino)
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Tramo / Origen y Destino:</td><td class="td-cell" colspan="3">{{ $servicio->origen_destino }}</td></tr>
                    @endif
                    @if($servicio->observacion)
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Observación / Concepto:</td><td class="td-cell" colspan="3">{{ $servicio->observacion }}</td></tr>
                    @endif
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr><td colspan="4" class="info-header">LIQUIDACIÓN DE PAGO DEL SERVICIO</td></tr>
                    <tr><td class="td-cell total-cell" colspan="3">MONTO TOTAL PAGADO (Bs.):</td><td class="td-num total-cell">Bs. {{ number_format($servicio->monto_total, 2) }}</td></tr>
                    <tr><td class="td-cell" colspan="3" style="font-weight:bold;">EQUIVALENTE EN DÓLARES ($us):</td><td class="td-num" style="font-weight:bold;">$us {{ number_format($servicio->monto_total / 6.96, 2) }}</td></tr>
                    <tr><td colspan="4">&nbsp;</td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Monto en Letras:</td><td class="td-cell" colspan="3"><strong>{{ montoEnLetrasServicio($servicio->monto_total) }}</strong></td></tr>
                    <tr><td class="td-cell" style="font-weight:bold; background:#f8fafc;">Entregado Por (Caja Chica):</td><td class="td-cell" colspan="3">{{ $servicio->entregado_por ?? 'Administración General' }}</td></tr>
                </table>
            </body>
            </html>
        `;

        const blob = new Blob(['\ufeff' + htmlContent], { type: 'application/vnd.ms-excel;charset=utf-8' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'Recibo_Servicio_Externo_Nro_' + '{{ str_pad($servicio->id, 5, "0", STR_PAD_LEFT) }}' + '.xls';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endpush
@endsection
