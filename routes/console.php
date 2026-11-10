<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('db:verify-integrity {--reconcile : Reconcilia saldos derivados de anticipos}', function () {
    $this->info("=== VERIFICACIÓN Y AUDITORÍA DE INTEGRIDAD DE BASE DE DATOS ===");

    // 1. Verificar Anticipos
    $anticiposInvalidos = \App\Models\Anticipo::where('saldo', '<', 0)->orWhereRaw('saldo > monto')->count();
    if ($anticiposInvalidos > 0) {
        $this->error("❌ Se encontraron {$anticiposInvalidos} anticipos con saldos inconsistentes (negativos o superiores al monto original).");
    } else {
        $this->info("✓ Anticipos: Todos los saldos son válidos (0 <= saldo <= monto).");
    }

    // 2. Verificar Pagos
    $pagosInvalidos = \App\Models\Pago::where('neto', '<', 0)->count();
    if ($pagosInvalidos > 0) {
        $this->error("❌ Se encontraron {$pagosInvalidos} pagos con neto negativo.");
    } else {
        $this->info("✓ Liquidaciones: Todos los pagos tienen neto no negativo.");
    }

    // 3. Balance de Caja
    $recargado = \App\Models\FondoPago::sum('monto');
    $gastadoPagos = \App\Models\Pago::sum('monto_pagado');
    $gastadoAnticipos = \App\Models\Anticipo::sum('monto');
    $saldoCalculado = $recargado - ($gastadoPagos + $gastadoAnticipos);

    $this->line(sprintf("  - Total Fondos Recargados: Bs. %s", number_format($recargado, 2)));
    $this->line(sprintf("  - Total Gastado en Pagos:   Bs. %s", number_format($gastadoPagos, 2)));
    $this->line(sprintf("  - Total Gastado Anticipos:  Bs. %s", number_format($gastadoAnticipos, 2)));
    $this->line(sprintf("  - Saldo Disponible en Caja: Bs. %s", number_format($saldoCalculado, 2)));

    // 4. Auditoría
    $auditLogsCount = \Illuminate\Support\Facades\DB::table('audit_logs')->count();
    $this->info("✓ Auditoría: {$auditLogsCount} registros de auditoría almacenados.");

    if ($this->option('reconcile')) {
        $this->info("Reconciliando saldos derivados...");
        $anticipos = \App\Models\Anticipo::all();
        foreach ($anticipos as $a) {
            $totalDescontado = \Illuminate\Support\Facades\DB::table('pago_anticipo')
                ->where('anticipo_id', $a->id)
                ->sum('monto_descontado');
            $nuevoSaldo = max(0, $a->monto - $totalDescontado);
            if ($a->saldo != $nuevoSaldo) {
                $a->saldo = $nuevoSaldo;
                $a->save();
            }
        }
        $this->info("✓ Reconciliación completada exitosamente.");
    }

    $this->info("Verificación finalizada con éxito.");
})->purpose('Verifica y reconcilia la integridad de saldos y registros contables de la base de datos');

