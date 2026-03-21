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
        Schema::table('fm1_forms', function (Blueprint $table) {
            $table->string('nombre_ant')->nullable();
            $table->string('num_empleado_ant')->nullable();
            $table->string('cod_movi_ant')->nullable();
            $table->string('tipo_mov_ant')->nullable();
            $table->date('fecha_inicio_ant')->nullable();
            $table->date('fecha_fin_ant')->nullable();
            $table->string('turno_opcional_ant')->nullable();
            $table->string('percepcion_adicional_ant')->nullable();
            $table->string('riesgos_prof_ant')->nullable();
            $table->string('nombre_trab_ant')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fm1_forms', function (Blueprint $table) {
            $table->dropColumn([
                'nombre_ant', 'num_empleado_ant', 'cod_movi_ant', 'tipo_mov_ant',
                'fecha_inicio_ant', 'fecha_fin_ant', 'turno_opcional_ant',
                'percepcion_adicional_ant', 'riesgos_prof_ant', 'nombre_trab_ant'
            ]);
        });
    }
};
