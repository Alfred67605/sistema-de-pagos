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
