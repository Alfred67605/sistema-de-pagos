<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pago_items')) {
            Schema::create('pago_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pago_id')->constrained('pagos')->cascadeOnDelete();
                if (Schema::hasTable('contratos')) {
                    $table->foreignId('contrato_id')->nullable()->constrained('contratos')->nullOnDelete();
                } else {
                    $table->unsignedBigInteger('contrato_id')->nullable();
                }
                $table->string('tipo_trabajo');           // Jornales, Volqueta, Pala, etc.
                $table->string('descripcion')->nullable();
                $table->decimal('cantidad', 12, 2)->default(1);
                $table->decimal('precio_unitario', 12, 2)->default(0);
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_items');
    }
};
