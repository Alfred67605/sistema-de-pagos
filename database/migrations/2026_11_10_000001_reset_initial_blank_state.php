<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Deja el sistema completamente en blanco (sin registros de prueba),
     * conservando únicamente la cuenta de acceso principal (admin@mina.com).
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();
        Schema::disableForeignKeyConstraints();

        $tablesToClear = [
            'pago_items',
            'pagos',
            'anticipos',
            'fondos_pagos',
            'servicios_externos',
            'lote_analisis',
            'transacciones_minerales',
            'caja_minerales_recargas',
            'trabajadores',
            'bocaminas',
            'tipos_trabajo',
            'tipos_contrato',
            'audit_logs',
        ];

        try {
            if ($driver === 'sqlite') {
                DB::statement('DROP TRIGGER IF EXISTS trg_pagos_audit_insert');
                DB::statement('DROP TRIGGER IF EXISTS trg_pagos_audit_update');
                DB::statement('DROP TRIGGER IF EXISTS trg_pagos_audit_delete');
            }

            foreach ($tablesToClear as $table) {
                if (Schema::hasTable($table)) {
                    if ($driver === 'pgsql') {
                        DB::statement('TRUNCATE TABLE "' . $table . '" RESTART IDENTITY CASCADE');
                    } else {
                        DB::table($table)->delete();
                        if ($driver === 'sqlite') {
                            try {
                                DB::statement("DELETE FROM sqlite_sequence WHERE name = ?", [$table]);
                            } catch (\Throwable $e) {
                                // sqlite_sequence may not exist yet
                            }
                        }
                    }
                }
            }

            // Dejar únicamente el usuario administrador por defecto y sin avatar de prueba
            if (Schema::hasTable('users')) {
                DB::table('users')->where('email', '!=', 'admin@mina.com')->delete();

                $adminExists = DB::table('users')->where('email', 'admin@mina.com')->exists();
                if ($adminExists) {
                    DB::table('users')->where('email', 'admin@mina.com')->update([
                        'name' => 'Administrador Minero',
                        'avatar' => null,
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('users')->insert([
                        'name' => 'Administrador Minero',
                        'email' => 'admin@mina.com',
                        'password' => Hash::make('admin123'),
                        'email_verified_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Opciones base para el selector desplegable de Tipo de Contrato al crear un trabajador
            if (Schema::hasTable('tipos_contrato')) {
                $tiposContrato = [
                    'Por saco',
                    'Por volqueta',
                    'Por metro avanzado',
                    'Por viaje',
                    'Mensual',
                    'Diario',
                ];
                foreach ($tiposContrato as $nombre) {
                    DB::table('tipos_contrato')->insert([
                        'nombre' => $nombre,
                        'estado' => 'activo',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Recrear triggers de auditoría en SQLite después de limpiar
            if ($driver === 'sqlite' && Schema::hasTable('audit_logs') && Schema::hasTable('pagos')) {
                DB::statement("
                    CREATE TRIGGER IF NOT EXISTS trg_pagos_audit_insert
                    AFTER INSERT ON pagos
                    BEGIN
                        INSERT INTO audit_logs (tabla_afectada, registro_id, accion, valores_nuevos, created_at)
                        VALUES ('pagos', NEW.id, 'INSERT', json_object('trabajador_id', NEW.trabajador_id, 'neto', NEW.neto, 'monto_pagado', NEW.monto_pagado), datetime('now'));
                    END;
                ");
                DB::statement("
                    CREATE TRIGGER IF NOT EXISTS trg_pagos_audit_update
                    AFTER UPDATE ON pagos
                    BEGIN
                        INSERT INTO audit_logs (tabla_afectada, registro_id, accion, valores_anteriores, valores_nuevos, created_at)
                        VALUES ('pagos', NEW.id, 'UPDATE',
                            json_object('neto', OLD.neto, 'monto_pagado', OLD.monto_pagado, 'saldo_pendiente', OLD.saldo_pendiente),
                            json_object('neto', NEW.neto, 'monto_pagado', NEW.monto_pagado, 'saldo_pendiente', NEW.saldo_pendiente),
                            datetime('now'));
                    END;
                ");
                DB::statement("
                    CREATE TRIGGER IF NOT EXISTS trg_pagos_audit_delete
                    AFTER DELETE ON pagos
                    BEGIN
                        INSERT INTO audit_logs (tabla_afectada, registro_id, accion, valores_anteriores, created_at)
                        VALUES ('pagos', OLD.id, 'DELETE', json_object('trabajador_id', OLD.trabajador_id, 'neto', OLD.neto, 'monto_pagado', OLD.monto_pagado), datetime('now'));
                    END;
                ");
            }
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        // Limpiar archivos de respaldos de prueba y configuraciones temporales
        $backupDir = storage_path('app/backups');
        if (File::exists($backupDir)) {
            foreach (File::files($backupDir) as $file) {
                $ext = strtolower($file->getExtension());
                if (in_array($ext, ['zip', 'sqlite'], true)) {
                    @File::delete($file->getPathname());
                }
            }
        }

        foreach (['tipos_servicio_custom.json', 'backup_settings.json'] as $jsonFile) {
            $path = storage_path('app/' . $jsonFile);
            if (File::exists($path)) {
                @File::delete($path);
            }
        }
    }

    public function down(): void
    {
        // No-op
    }
};
