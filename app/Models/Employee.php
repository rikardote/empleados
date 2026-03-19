<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'periodo',
        'n_empresa',
        'id_tipo_plaza',
        'id_plaza_empleado',
        'id_empleado',
        'apellido_1',
        'apellido_2',
        'nombre',
        'id_legal',
        'id_c_u_r_p_st',
        'fecha_ingreso_st',
        'fec_alta_empleado',
        'fec_imputacion',
        'fec_pago',
        'id_forma_pago',
        'id_banco',
        'num_cuenta',
        'cancelado',
        'id_tipo_puesto',
        'n_tipo_puesto',
        'id_tipo_tabulador',
        'n_tipo_tabulador',
        'id_turno',
        'id_tipo_jornada',
        'id_horario',
        'n_horario',
        'hora_entrada_to',
        'hora_salida_to',
        'hora_entrada_op',
        'hora_salida_op',
        'num_horas',
        'numero_ss',
        'id_plaza',
        'id_zona',
        'id_puesto_plaza',
        'id_nivel',
        'id_sub_nivel',
        'id_grupo_grado_nivel',
        'id_integracion',
        'id_clasificacion',
        'id_rama',
        'n_puesto_plaza',
        'id_centro_pago',
        'id_clave_servicio',
        'n_clave_servicio',
        'id_centro_trabajo',
        'n_centro_trabajo',
        'poblacion',
        'n_municipio',
        'n_div_geografica',
        'id_area_generadora',
        'n_area_generadora',
        'id_div_geografica',
        'nomina_data',
    ];

    protected $casts = [
        'nomina_data' => 'array',
    ];
}
