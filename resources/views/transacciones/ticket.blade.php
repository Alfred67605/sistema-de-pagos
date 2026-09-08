<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket {{ $transaccion->tipo === 'compra' ? 'Compra' : 'Venta' }} #{{ str_pad($transaccion->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @page {
            size: auto;
            margin: 0mm !important;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            width: 70mm;
            max-width: 70mm;
            margin: 0 auto;
            padding: 1mm 1mm;
            font-family: 'Courier New', Courier, monospace;
            font-size: 10.5px;
            color: #000;
            background: #fff;
            line-height: 1.3;
        }
        .no-print {
            background: #0f172a;
            color: #fff;
            padding: 12px;
            text-align: center;
            margin-bottom: 15px;
            border-radius: 10px;
            font-family: sans-serif;
        }
        .btn-print {
            background: #10b981;
            color: #fff;
            border: none;
            padding: 10px 18px;
            font-weight: 800;
            border-radius: 8px;
            cursor: pointer;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }
        .btn-close {
            background: #475569;
            color: #fff;
            border: none;
            padding: 10px 14px;
            font-weight: bold;
            border-radius: 8px;
            margin-left: 8px;
            cursor: pointer;
            font-size: 12px;
        }
        @media print {
            .no-print { display: none !important; }
            body { width: 70mm !important; margin: 0 auto !important; padding: 0 !important; }
        }
        .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ IMPRIMIR TICKET (80mm / 100x148)</button>
        <button onclick="window.close()" class="btn-close">Cerrar</button>
    </div>

    <!-- ══════════ TICKET DE COMPRA / VENTA DE MINERAL ══════════ -->
    <div style="text-align: center; margin-bottom: 6px;">
        <div style="font-weight: 900; font-size: 13px; text-transform: uppercase;">EMPRESA MINERA</div>
        <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; margin-top: 2px;">
            RECIBO DE {{ $transaccion->tipo === 'compra' ? 'COMPRA DE MINERAL' : 'VENTA DE MINERAL' }}
        </div>
        <div style="font-size: 10px; font-weight: bold; margin-top: 2px;">
            {{ $transaccion->tipo === 'compra' ? 'LOTE N.º' : 'VENTA N.º' }} {{ str_pad($transaccion->id, 5, '0', STR_PAD_LEFT) }}
        </div>
        <div style="font-size: 9.5px; margin-top: 1px;">Fecha: {{ \Carbon\Carbon::parse($transaccion->fecha)->format('d/m/Y') }}</div>
    </div>

    <div style="border-top: 1px dashed #000; border-bottom: 1px dashed #000; padding: 4px 0; margin-bottom: 6px; font-size: 10px;">
        <div><strong>{{ $transaccion->tipo === 'compra' ? 'PROVEEDOR:' : 'CLIENTE:' }}</strong> {{ strtoupper($transaccion->cliente_proveedor) }}</div>
        @if($transaccion->bocamina)
            <div><strong>BOCAMINA:</strong> {{ strtoupper($transaccion->bocamina->nombre) }}</div>
        @endif
        @if($transaccion->presentacion)
            <div><strong>PRESENTACIÓN:</strong> {{ strtoupper($transaccion->presentacion === 'Otro' ? ($transaccion->presentacion_otro ?? 'OTRO') : $transaccion->presentacion) }}</div>
        @endif
        @if($transaccion->tipo === 'venta' && $transaccion->destino)
            <div><strong>DESTINO:</strong> {{ strtoupper($transaccion->destino) }}</div>
        @endif
    </div>

    @php
        $pesoBruto = (float)($transaccion->peso_bruto ?? $transaccion->peso_neto_seco);
        $pctHumedad = (float)($transaccion->humedad_porcentaje ?? 0);
        $subtotalBruto = $pesoBruto * (float)$transaccion->precio_unidad;
        $montoHumedad = $subtotalBruto * ($pctHumedad / 100);
    @endphp

    <div style="font-size: 10px; margin-bottom: 6px; line-height: 1.35; padding-right: 4px;">
        <div class="flex-between">
            <span>Cant. / Piezas:</span>
            <span>{{ number_format($transaccion->cantidad, 2) }}</span>
        </div>
        <div class="flex-between">
            <span>Peso Bruto:</span>
            <span>{{ number_format($pesoBruto, 2) }} Kg</span>
        </div>
        @if($pctHumedad > 0)
        <div class="flex-between">
            <span>Humedad ({{ number_format($pctHumedad, 2) }}%):</span>
            <span>-Bs. {{ number_format($montoHumedad, 2) }}</span>
        </div>
        @endif
        <div class="flex-between" style="font-weight: bold;">
            <span>Peso Neto Seco:</span>
            <span>{{ number_format($transaccion->peso_neto_seco, 2) }} Kg</span>
        </div>
        <div class="flex-between">
            <span>Precio Unitario:</span>
            <span>Bs. {{ number_format($transaccion->precio_unidad, 2) }}</span>
        </div>
    </div>

    @if($transaccion->analisis && count($transaccion->analisis) > 0)
    <div style="border-top: 1px dashed #000; padding: 4px 0; margin-bottom: 6px; font-size: 9.5px;">
        <div style="font-weight: bold; margin-bottom: 2px;">LEY(ES) DE LABORATORIO:</div>
        @foreach($transaccion->analisis as $an)
            <div class="flex-between">
                <span>• {{ $an->mineral }}:</span>
                <span style="font-weight: bold;">{{ number_format($an->ley, 2) }}%</span>
            </div>
        @endforeach
    </div>
    @endif

    <div style="border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 4px 0; margin-bottom: 6px; font-size: 11px; font-weight: 900; line-height: 1.35; padding-right: 4px;">
        <div class="flex-between">
            <span>SUBTOTAL:</span>
            <span>Bs. {{ number_format($subtotalBruto, 2) }}</span>
        </div>
        @if($montoHumedad > 0)
        <div class="flex-between" style="font-weight: normal; font-size: 10px;">
            <span>Desc. Humedad:</span>
            <span>-Bs. {{ number_format($montoHumedad, 2) }}</span>
        </div>
        @endif
        <div class="flex-between" style="font-size: 11.5px; border-top: 1px solid #000; padding-top: 2px; margin-top: 2px;">
            <span>NETO LIQUIDADO:</span>
            <span>Bs. {{ number_format($transaccion->monto_total, 2) }}</span>
        </div>
        <div class="flex-between" style="font-size: 10.5px;">
            <span>DÓLARES ($us):</span>
            <span>$us {{ number_format($transaccion->monto_total / 6.96, 2) }}</span>
        </div>
    </div>

    @php
        $montoEnLetras = '';
        if (!function_exists('montoEnLetrasTransaccion')) {
            function montoEnLetrasTransaccion($monto) {
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
                    if ($n < 100) { $d = floor($n / 10); $u = $n % 10; return $decenas[$d] . ($u > 0 ? ' Y ' . $unidades[$u] : ''); }
                    if ($n == 100) return 'CIEN';
                    if ($n < 1000) { $c = floor($n / 100); $resto = $n % 100; return $centenas[$c] . ($resto > 0 ? ' ' . $numALetras($resto) : ''); }
                    if ($n < 1000000) { $miles = floor($n / 1000); $resto = $n % 1000; $milesStr = ($miles == 1) ? 'UN MIL' : $numALetras($miles) . ' MIL'; return $milesStr . ($resto > 0 ? ' ' . $numALetras($resto) : ''); }
                    return number_format($n, 0, '', '');
                };
                $letras = ($entero == 0) ? 'CERO' : trim($numALetras($entero));
                return "SON: " . $letras . " " . $centavosStr . " BOLIVIANOS";
            }
        }
    @endphp

    <div style="font-size: 8.5px; font-weight: bold; text-transform: uppercase; border-top: 1px dashed #000; padding: 3px 0; margin-bottom: 12px;">
        {{ montoEnLetrasTransaccion($transaccion->monto_total) }}
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <div style="border-top: 1px solid #000; width: 75%; margin: 0 auto 2px auto;"></div>
        <div style="font-size: 9.5px; font-weight: bold;">{{ strtoupper($transaccion->cliente_proveedor) }}</div>
        <div style="font-size: 8.5px;">{{ $transaccion->tipo === 'compra' ? 'PROVEEDOR (RECIBÍ CONFORME)' : 'CLIENTE (RECIBÍ CONFORME)' }}</div>
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <div style="border-top: 1px solid #000; width: 75%; margin: 0 auto 2px auto;"></div>
        <div style="font-size: 9.5px; font-weight: bold;">ADMINISTRACIÓN DE CAJA</div>
        <div style="font-size: 8.5px;">ENTREGUÉ CONFORME (MÓDULO MINERALES)</div>
    </div>

    <div style="text-align: center; margin-top: 12px; font-size: 8.5px; border-top: 1px dashed #000; padding-top: 4px;">
        *** Impreso desde Sistema de Pagos ***
    </div>

</body>
</html>
