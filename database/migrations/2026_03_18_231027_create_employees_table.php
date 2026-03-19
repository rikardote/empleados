<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('periodo')->nullable(); // For historical data
            $table->text('n_empresa')->nullable();
            $table->text('id_tipo_plaza')->nullable();
            $table->text('id_plaza_empleado')->nullable();
            $table->string('id_empleado', 30)->nullable();
            $table->text('apellido_1')->nullable();
            $table->text('apellido_2')->nullable();
            $table->text('nombre')->nullable();
            $table->string('id_legal', 30)->nullable();
            $table->string('id_c_u_r_p_st', 30)->nullable();
            $table->text('fecha_ingreso_st')->nullable();
            $table->text('fec_alta_empleado')->nullable();
            $table->text('fec_imputacion')->nullable();
            $table->text('fec_pago')->nullable();
            $table->text('id_forma_pago')->nullable();
            $table->text('id_banco')->nullable();
            $table->text('num_cuenta')->nullable();
            $table->text('cancelado')->nullable();
            $table->text('id_tipo_puesto')->nullable();
            $table->text('n_tipo_puesto')->nullable();
            $table->text('id_tipo_tabulador')->nullable();
            $table->text('n_tipo_tabulador')->nullable();
            $table->text('id_turno')->nullable();
            $table->text('id_tipo_jornada')->nullable();
            $table->text('id_horario')->nullable();
            $table->text('n_horario')->nullable();
            $table->text('hora_entrada_to')->nullable();
            $table->text('hora_salida_to')->nullable();
            $table->text('hora_entrada_op')->nullable();
            $table->text('hora_salida_op')->nullable();
            $table->text('num_horas')->nullable();
            $table->text('numero_ss')->nullable();
            $table->text('id_plaza')->nullable();
            $table->text('id_zona')->nullable();
            $table->text('id_puesto_plaza')->nullable();
            $table->text('id_nivel')->nullable();
            $table->text('id_sub_nivel')->nullable();
            $table->text('id_grupo_grado_nivel')->nullable();
            $table->text('id_integracion')->nullable();
            $table->text('id_clasificacion')->nullable();
            $table->text('id_rama')->nullable();
            $table->text('n_puesto_plaza')->nullable();
            $table->text('id_centro_pago')->nullable();
            $table->text('id_clave_servicio')->nullable();
            $table->text('n_clave_servicio')->nullable();
            $table->text('id_centro_trabajo')->nullable();
            $table->text('n_centro_trabajo')->nullable();
            $table->text('poblacion')->nullable();
            $table->text('n_municipio')->nullable();
            $table->text('n_div_geografica')->nullable();
            $table->text('id_area_generadora')->nullable();
            $table->text('n_area_generadora')->nullable();
            $table->text('id_div_geografica')->nullable();
            $table->json('nomina_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
