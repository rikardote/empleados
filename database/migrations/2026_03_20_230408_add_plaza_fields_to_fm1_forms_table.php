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
            $table->string('codigo_puesto')->after('fecha_final')->nullable();
            $table->string('nivel_subnivel')->nullable();
            $table->string('denominacion_puesto')->nullable();
            $table->string('numero_plaza')->nullable();
            $table->string('tipo_plaza')->nullable();
            $table->string('ocupacion')->nullable();
            $table->string('estatus_plaza')->nullable();
            $table->string('unidad_administrativa')->nullable();
            $table->string('unidad_administrativa_denominacion')->nullable();
            $table->string('adscripcion')->nullable();
            $table->string('adscripcion_denominacion')->nullable();
            $table->string('adscripcion_fisica')->nullable();
            $table->string('adscripcion_fisica_denominacion')->nullable();
            $table->string('servicio')->nullable();
            $table->string('servicio_denominacion')->nullable();
            $table->string('codigo_turno')->nullable();
            $table->string('codigo_turno_descripcion')->nullable();
            $table->string('jornada')->nullable();
            $table->string('horario_codigo')->nullable();
            $table->string('horario_entrada1')->nullable();
            $table->string('horario_salida1')->nullable();
            $table->string('horario_entrada2')->nullable();
            $table->string('horario_salida2')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('turno_opcional')->nullable();
            $table->string('percepcion_adicional')->nullable();
            $table->string('riesgos_profesionales')->nullable();
            $table->string('mando')->nullable();
            $table->string('enlace_alta_responsabilidad')->nullable();
            $table->string('enlace')->nullable();
            $table->string('operativo')->nullable();
            $table->string('rama_medica')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fm1_forms', function (Blueprint $table) {
            $table->dropColumn([
                'codigo_puesto', 'nivel_subnivel', 'denominacion_puesto', 'numero_plaza', 'tipo_plaza', 'ocupacion', 'estatus_plaza',
                'unidad_administrativa', 'unidad_administrativa_denominacion', 'adscripcion', 'adscripcion_denominacion',
                'adscripcion_fisica', 'adscripcion_fisica_denominacion', 'servicio', 'servicio_denominacion',
                'codigo_turno', 'codigo_turno_descripcion', 'jornada', 'horario_codigo', 
                'horario_entrada1', 'horario_salida1', 'horario_entrada2', 'horario_salida2',
                'observaciones', 'turno_opcional', 'percepcion_adicional', 'riesgos_profesionales',
                'mando', 'enlace_alta_responsabilidad', 'enlace', 'operativo', 'rama_medica'
            ]);
        });
    }
};
