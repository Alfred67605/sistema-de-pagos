<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios_externos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('numero_comprobante')->nullable();
            $table->string('tipo_servicio'); // Volqueta / Transporte, Excavadora, Gallinita / Retro, Oruga, Mantenimiento, Otro
            $table->string('chofer_operador'); // Chofer / Operador / Proveedor (Fundamental)
            $table->string('placa_maquinaria')->nullable(); // Placa o N.º de Máquina (Fundamental)
            $table->foreignId('bocamina_id')->nullable()->constrained('bocaminas')->nullOnDelete();
            $table->decimal('cantidad', 12, 2)->default(1); // Cantidad (ej. N.º de viajes o horas)
            $table->string('unidad_medida')->default('viajes'); // viajes, horas, global
            $table->decimal('precio_unitario', 12, 2)->default(0);
            $table->decimal('monto_total', 12, 2); // Monto pagado
            $table->string('origen_destino')->nullable(); // Tramo origen - destino
            $table->string('metodo_pago')->default('efectivo'); // efectivo, cheque, transferencia
            $table->string('entregado_por')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios_externos');
    }
};
