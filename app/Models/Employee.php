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

    public static function getPerceptionKeys()
    {
        return [
            'sal_base', 'prev_social', 'compensacion', 'riesgo_prof', 'concepto_nuevo_01', 
            'comp_x_antig', 'quniquenio', 'turno_opcional', 'percep_adic', 'imp_hext_dob', 
            'imp_hext_tri', 'despensa', 'honorarios', 'beca_pasan_pre', 'ayuda_renta_bec', 
            'prima_dom_gra', 'p_domtrab_ex_st', 'prima_dom_ex', 'p_domtrab_gr_st', 'remun_guardias', 
            'dev_ded_indeb', 'compensa_unica', 'grava_vac_st', 'exento_vacacion', 'beca_hijos', 
            'ayuda_lentes', 'premio_aniver', 'primo_10_mayo', 'premio_antig', 'estimulo_adic', 
            'dias_eco_no_disf', 'servs_event', 'paga_extra_1', 'premio_moneda', 'est_prod_cal', 
            'material_didac', 'estimulo_pro_mm', 'remun_suplencia', 'bono_reyes', 'ayuda_transp', 
            'ayuda_utiles', 'asigna_medico', 'comple_beca_med', 'esti_asistencia', 'esti_puntuali', 
            'esti_desempeno', 'esti_merito_rel', 'prem_ant_25_30', 'ayuda_muerfam', 'impr_tesis', 
            'apoyo_desa_capa', 'exceso_credito', 'vale_dec', 'maternid_tot_st', 'ayuda_actualiza', 
            'esti_trab_mes', 'ajuste_sueldo', 'apoyo_deporte', 'mant_vehiculo', 'beca_residentes', 
            'ajuste_residen', 'pago_retiro', 'premio_nac_anti', 'ajuste_calend', 'comision', 
            'jornada_noct', 'prem_est_recomp', 'cobro_puest_ant', 'prest_pago_exce', 'guardias_provac', 
            'suplen_provac', 'dev_nograv', 'grat_mes_beca', 'f_ahorro_ind_dv', 'f_ahorro_empr_dv', 
            'f_ahorro_sind_dv', 'f_ahorro_sind_rt', 'f_ahorro_seg_dv', 'f_ahorro_seg_rt', 'compen_serv_esp', 
            'prevenissste', 'reco_serpub', 'rezago_quir', 'aport_pat_bruta', 'compen_isr_agui', 'total_devengos'
        ];
    }

    public function getCategorizedNomina()
    {
        $perceptions = [];
        $deductions = [];
        $perceptionKeys = self::getPerceptionKeys();
        $nomina = $this->nomina_data ?? [];
        
        // Skip keys that are totals to avoid redundancy in the breakdown
        $totalKeys = ['liquido', 'total_devengos', 'total_retenido_a', 'total_neto', 'total_deducciones'];

        foreach ($nomina as $key => $value) {
            if ($value == 0 || in_array($key, $totalKeys)) continue;
            
            if (in_array($key, $perceptionKeys)) {
                $perceptions[$key] = $value;
            } else {
                $deductions[$key] = $value;
            }
        }
        
        return [
            'perceptions' => $perceptions,
            'deductions' => $deductions,
            'liquido' => $nomina['liquido'] ?? ($nomina['total_neto'] ?? 0),
            'total_devengos' => $nomina['total_devengos'] ?? 0,
            'total_retenido_a' => $nomina['total_retenido_a'] ?? ($nomina['total_deducciones'] ?? 0),
        ];
    }
}
