<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'trabajador_id',
        'fecha',
        'numero_nota',
        'tarifa_pago',
        'cantidad_trabajada',
        'tipo_contrato_nombre',
        'subtotal',
        'bonos',
        'descuentos',
        'anticipos_descontados',
        'neto',
        'monto_pagado',
        'saldo_pendiente',
        'saldo_liquidado',
        'tipo_cambio',
        'observacion',
        'metodo_pago',
        'entregado_por',
        'es_editado'
    ];

    protected $casts = [
        'fecha' => 'date',
        'saldo_liquidado' => 'boolean',
        'es_editado' => 'boolean',
    ];

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function items()
    {
        return $this->hasMany(PagoItem::class);
    }

    public function anticipos()
    {
        return $this->belongsToMany(Anticipo::class, 'pago_anticipo')
                    ->withPivot('monto_descontado')
                    ->withTimestamps();
    }

    public function getTipoAttribute()
    {
        return $this->tipo_contrato_nombre;
    }

    public function getCantidadAttribute()
    {
        return $this->cantidad_trabajada;
    }

    public function getPrecioUnitarioAttribute()
    {
        return $this->tarifa_pago;
    }

    public function getPagadoAttribute()
    {
        return true;
    }

    public function getContratoAttribute()
    {
        return null;
    }

    public function getMontoLetrasAttribute()
    {
        return self::convertirNumeroALetras($this->monto_pagado);
    }

    public static function convertirNumeroALetras($numero)
    {
        $enteros = floor($numero);
        $centavos = round(($numero - $enteros) * 100);

        $letras = self::convertir($enteros);
        if ($centavos > 0) {
            return strtoupper($letras) . ' CON ' . $centavos . ' CENTAVOS';
        } else {
            return strtoupper($letras);
        }
    }

    private static function convertir($n) {
        if ($n == 0) return 'cero';
        $unidades = ['', 'un', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'];
        $decenas = ['', 'diez', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 'ochenta', 'noventa'];
        $dieces = ['diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve'];
        $veintes = ['veinte', 'veintiuno', 'veintidós', 'veintitrés', 'veinticuatro', 'veinticinco', 'veintiséis', 'veintisiete', 'veintiocho', 'veintinueve'];
        $centenas = ['', 'cien', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'];

        if ($n < 10) return $unidades[$n];
        if ($n < 20) return $dieces[$n - 10];
        if ($n < 30) return $veintes[$n - 20];
        if ($n < 100) {
            $u = $n % 10;
            return $decenas[floor($n / 10)] . ($u > 0 ? ' y ' . $unidades[$u] : '');
        }
        if ($n == 100) return 'cien';
        if ($n < 1000) {
            $d = $n % 100;
            $c = floor($n / 100);
            $c_txt = $centenas[$c];
            if ($c == 1 && $d > 0) $c_txt = 'ciento';
            return $c_txt . ($d > 0 ? ' ' . self::convertir($d) : '');
        }
        if ($n == 1000) return 'mil';
        if ($n < 1000000) {
            $m = floor($n / 1000);
            $r = $n % 1000;
            $m_txt = ($m == 1) ? 'mil' : self::convertir($m) . ' mil';
            return $m_txt . ($r > 0 ? ' ' . self::convertir($r) : '');
        }
        if ($n == 1000000) return 'un millón';
        if ($n < 1000000000) {
            $mill = floor($n / 1000000);
            $r = $n % 1000000;
            $mill_txt = ($mill == 1) ? 'un millón' : self::convertir($mill) . ' millones';
            return $mill_txt . ($r > 0 ? ' ' . self::convertir($r) : '');
        }
        return (string)$n;
    }
}
