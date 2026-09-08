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
        Schema::table('anticipos', function (Blueprint $table) {
            $table->string('observacion', 255)->nullable()->after('pagado');
            $table->decimal('dias_debe', 8, 2)->default(0)->after('observacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('anticipos', function (Blueprint $table) {
            $table->dropColumn(['observacion', 'dias_debe']);
        });
    }
};
