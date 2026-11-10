<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabla de Auditoría Contable y de Operaciones
        if (!Schema::hasTable('audit_logs')) {
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->id();
                $table->string('table_name', 50);
                $table->unsignedBigInteger('record_id');
                $table->string('action', 20); // 'UPDATE', 'DELETE'
                $table->text('old_values')->nullable();
                $table->text('new_values')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index(['table_name', 'record_id'], 'idx_audit_table_record');
                $table->index('created_at', 'idx_audit_created_at');
            });
        }

        // 2. Creación de Índices de Rendimiento para filtros, relaciones y ordenamientos frecuentes
        Schema::table('anticipos', function (Blueprint $table) {
            $table->index('saldo', 'idx_anticipos_saldo');
            $table->index(['trabajador_id', 'fecha'], 'idx_anticipos_trabajador_fecha');
            if (Schema::hasColumn('anticipos', 'bocamina_id')) {
                $table->index('bocamina_id', 'idx_anticipos_bocamina_id');
            }
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->index(['trabajador_id', 'fecha'], 'idx_pagos_trabajador_fecha');
            $table->index('fecha', 'idx_pagos_fecha');
        });

        Schema::table('trabajadores', function (Blueprint $table) {
            $table->index(['bocamina_id', 'estado'], 'idx_trabajadores_bocamina_estado');
            $table->index('estado', 'idx_trabajadores_estado');
        });

        Schema::table('transacciones_minerales', function (Blueprint $table) {
            $table->index(['tipo', 'fecha'], 'idx_transacciones_tipo_fecha');
            $table->index('bocamina_id', 'idx_transacciones_bocamina_id');
        });

        if (Schema::hasTable('pago_items')) {
            Schema::table('pago_items', function (Blueprint $table) {
                $table->index('pago_id', 'idx_pago_items_pago_id');
                $table->index('tipo_trabajo_id', 'idx_pago_items_tipo_trabajo_id');
            });
        }

        if (Schema::hasTable('pago_anticipo')) {
            Schema::table('pago_anticipo', function (Blueprint $table) {
                $table->index('pago_id', 'idx_pago_anticipo_pago_id');
                $table->index('anticipo_id', 'idx_pago_anticipo_anticipo_id');
            });
        }

        if (Schema::hasTable('fondos_pagos')) {
            Schema::table('fondos_pagos', function (Blueprint $table) {
                $table->index(['tipo_movimiento', 'created_at'], 'idx_fondos_tipo_fecha');
            });
        }

        if (Schema::hasTable('servicios_externos')) {
            Schema::table('servicios_externos', function (Blueprint $table) {
                $table->index(['tipo_servicio', 'fecha'], 'idx_servicios_tipo_fecha');
            });
        }

        // 3. Triggers en Base de Datos (Auditoría e Integridad)
        // A. Auditoría de Modificación en Pagos
        DB::unprepared("
            CREATE TRIGGER IF NOT EXISTS trg_audit_pagos_update
            AFTER UPDATE ON pagos
            WHEN OLD.neto != NEW.neto OR OLD.subtotal != NEW.subtotal OR OLD.anticipos_descontados != NEW.anticipos_descontados OR OLD.monto_pagado != NEW.monto_pagado
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, created_at)
                VALUES (
                    'pagos',
                    OLD.id,
                    'UPDATE',
                    json_object('neto', OLD.neto, 'subtotal', OLD.subtotal, 'anticipos_descontados', OLD.anticipos_descontados, 'monto_pagado', OLD.monto_pagado, 'fecha', OLD.fecha),
                    json_object('neto', NEW.neto, 'subtotal', NEW.subtotal, 'anticipos_descontados', NEW.anticipos_descontados, 'monto_pagado', NEW.monto_pagado, 'fecha', NEW.fecha),
                    datetime('now')
                );
            END;
        ");

        // B. Auditoría de Eliminación en Pagos
        DB::unprepared("
            CREATE TRIGGER IF NOT EXISTS trg_audit_pagos_delete
            AFTER DELETE ON pagos
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, created_at)
                VALUES (
                    'pagos',
                    OLD.id,
                    'DELETE',
                    json_object('trabajador_id', OLD.trabajador_id, 'neto', OLD.neto, 'subtotal', OLD.subtotal, 'anticipos_descontados', OLD.anticipos_descontados, 'fecha', OLD.fecha),
                    NULL,
                    datetime('now')
                );
            END;
        ");

        // C. Auditoría de Modificación en Anticipos
        DB::unprepared("
            CREATE TRIGGER IF NOT EXISTS trg_audit_anticipos_update
            AFTER UPDATE ON anticipos
            WHEN OLD.monto != NEW.monto OR OLD.saldo != NEW.saldo
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, created_at)
                VALUES (
                    'anticipos',
                    OLD.id,
                    'UPDATE',
                    json_object('monto', OLD.monto, 'saldo', OLD.saldo, 'trabajador_id', OLD.trabajador_id),
                    json_object('monto', NEW.monto, 'saldo', NEW.saldo, 'trabajador_id', NEW.trabajador_id),
                    datetime('now')
                );
            END;
        ");

        // D. Auditoría de Eliminación en Anticipos
        DB::unprepared("
            CREATE TRIGGER IF NOT EXISTS trg_audit_anticipos_delete
            AFTER DELETE ON anticipos
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, created_at)
                VALUES (
                    'anticipos',
                    OLD.id,
                    'DELETE',
                    json_object('trabajador_id', OLD.trabajador_id, 'monto', OLD.monto, 'saldo', OLD.saldo, 'fecha', OLD.fecha),
                    NULL,
                    datetime('now')
                );
            END;
        ");

        // E. Auditoría de Transacciones de Minerales (Compras y Ventas)
        DB::unprepared("
            CREATE TRIGGER IF NOT EXISTS trg_audit_transacciones_update
            AFTER UPDATE ON transacciones_minerales
            WHEN OLD.monto_total != NEW.monto_total OR OLD.tipo != NEW.tipo OR OLD.peso_neto_seco != NEW.peso_neto_seco
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, created_at)
                VALUES (
                    'transacciones_minerales',
                    OLD.id,
                    'UPDATE',
                    json_object('tipo', OLD.tipo, 'monto_total', OLD.monto_total, 'cliente_proveedor', OLD.cliente_proveedor, 'peso_neto_seco', OLD.peso_neto_seco),
                    json_object('tipo', NEW.tipo, 'monto_total', NEW.monto_total, 'cliente_proveedor', NEW.cliente_proveedor, 'peso_neto_seco', NEW.peso_neto_seco),
                    datetime('now')
                );
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER IF NOT EXISTS trg_audit_transacciones_delete
            AFTER DELETE ON transacciones_minerales
            BEGIN
                INSERT INTO audit_logs (table_name, record_id, action, old_values, new_values, created_at)
                VALUES (
                    'transacciones_minerales',
                    OLD.id,
                    'DELETE',
                    json_object('tipo', OLD.tipo, 'monto_total', OLD.monto_total, 'cliente_proveedor', OLD.cliente_proveedor),
                    NULL,
                    datetime('now')
                );
            END;
        ");

        // F. Trigger de Integridad: El saldo de un anticipo nunca puede ser negativo
        DB::unprepared("
            CREATE TRIGGER IF NOT EXISTS trg_integrity_anticipos_saldo_non_negative
            BEFORE UPDATE OF saldo ON anticipos
            WHEN NEW.saldo < 0
            BEGIN
                SELECT RAISE(ABORT, 'Error de Integridad Contable: El saldo del anticipo no puede ser menor a cero.');
            END;
        ");

        // G. Trigger de Integridad: El monto neto de un pago no puede ser negativo
        DB::unprepared("
            CREATE TRIGGER IF NOT EXISTS trg_integrity_pagos_neto_non_negative_ins
            BEFORE INSERT ON pagos
            WHEN NEW.neto < 0
            BEGIN
                SELECT RAISE(ABORT, 'Error de Integridad Contable: El monto neto a liquidar no puede ser negativo.');
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER IF NOT EXISTS trg_integrity_pagos_neto_non_negative_upd
            BEFORE UPDATE OF neto ON pagos
            WHEN NEW.neto < 0
            BEGIN
                SELECT RAISE(ABORT, 'Error de Integridad Contable: El monto neto a liquidar no puede ser negativo.');
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_audit_pagos_update;");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_audit_pagos_delete;");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_audit_anticipos_update;");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_audit_anticipos_delete;");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_audit_transacciones_update;");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_audit_transacciones_delete;");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_integrity_anticipos_saldo_non_negative;");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_integrity_pagos_neto_non_negative_ins;");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_integrity_pagos_neto_non_negative_upd;");

        Schema::table('anticipos', function (Blueprint $table) {
            $table->dropIndex('idx_anticipos_saldo');
            $table->dropIndex('idx_anticipos_trabajador_fecha');
            if (Schema::hasColumn('anticipos', 'bocamina_id')) {
                $table->dropIndex('idx_anticipos_bocamina_id');
            }
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->dropIndex('idx_pagos_trabajador_fecha');
            $table->dropIndex('idx_pagos_fecha');
        });

        Schema::table('trabajadores', function (Blueprint $table) {
            $table->dropIndex('idx_trabajadores_bocamina_estado');
            $table->dropIndex('idx_trabajadores_estado');
        });

        Schema::table('transacciones_minerales', function (Blueprint $table) {
            $table->dropIndex('idx_transacciones_tipo_fecha');
            $table->dropIndex('idx_transacciones_bocamina_id');
        });

        if (Schema::hasTable('pago_items')) {
            Schema::table('pago_items', function (Blueprint $table) {
                $table->dropIndex('idx_pago_items_pago_id');
                $table->dropIndex('idx_pago_items_tipo_trabajo_id');
            });
        }

        if (Schema::hasTable('pago_anticipo')) {
            Schema::table('pago_anticipo', function (Blueprint $table) {
                $table->dropIndex('idx_pago_anticipo_pago_id');
                $table->dropIndex('idx_pago_anticipo_anticipo_id');
            });
        }

        if (Schema::hasTable('fondos_pagos')) {
            Schema::table('fondos_pagos', function (Blueprint $table) {
                $table->dropIndex('idx_fondos_tipo_fecha');
            });
        }

        if (Schema::hasTable('servicios_externos')) {
            Schema::table('servicios_externos', function (Blueprint $table) {
                $table->dropIndex('idx_servicios_tipo_fecha');
            });
        }

        Schema::dropIfExists('audit_logs');
    }
};
