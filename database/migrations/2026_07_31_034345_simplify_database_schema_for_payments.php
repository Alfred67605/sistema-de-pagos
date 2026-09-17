<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop old tables that are no longer needed or will be rebuilt
        Schema::dropIfExists('trabajos');
        Schema::dropIfExists('contratos');

        // 2. Create simplified contratos table (Tipos de Contrato Catalog)
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('estado')->default('activo'); // activo, inactivo (desactivado)
            $table->timestamps();
        });

        // 3. Add fields to trabajadores
        Schema::table('trabajadores', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_contrato_id')->nullable()->after('bocamina_id');
            $table->decimal('tarifa_acordada', 10, 2)->nullable()->after('tipo_contrato_id');
            $table->text('observaciones')->nullable()->after('estado');
        });

        // 4. Add fields to pagos
        Schema::table('pagos', function (Blueprint $table) {
            $table->decimal('tarifa_pago', 10, 2)->default(0.00)->after('fecha');
            $table->decimal('cantidad_trabajada', 10, 2)->default(0.00)->after('tarifa_pago');
            $table->string('tipo_contrato_nombre')->nullable()->after('cantidad_trabajada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn(['tarifa_pago', 'cantidad_trabajada', 'tipo_contrato_nombre']);
        });

        Schema::table('trabajadores', function (Blueprint $table) {
            $table->dropForeign(['tipo_contrato_id']);
            $table->dropColumn(['tipo_contrato_id', 'tarifa_acordada', 'observaciones']);
        });

        Schema::dropIfExists('contratos');
    }
};
