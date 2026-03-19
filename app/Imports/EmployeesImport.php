<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class EmployeesImport implements ToModel, WithHeadingRow, WithEvents, WithBatchInserts, WithChunkReading
{
    private $periodo;
    private $importId;

    public function __construct($periodo = null, $importId = null)
    {
        $this->periodo = $periodo;
        $this->importId = $importId;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                if ($this->importId) {
                    $totalRows = $event->getReader()->getTotalRows();
                    // Laravel multi-sheet reader returns an array of row counts
                    $total = array_sum($totalRows);
                    Cache::put("import_progress_{$this->importId}", [
                        'total' => $total,
                        'current' => 0,
                        'status' => 'processing'
                    ], 3600);
                }
            },
            AfterImport::class => function (AfterImport $event) {
                if ($this->importId) {
                    $data = Cache::get("import_progress_{$this->importId}");
                    if ($data) {
                        $data['current'] = $data['total'];
                        $data['status'] = 'completed';
                        Cache::put("import_progress_{$this->importId}", $data, 3600);
                    }
                }
            },
        ];
    }

    public function model(array $row)
    {
        if ($this->importId) {
            Cache::increment("import_progress_{$this->importId}_count");
            // Periodically sync count to the main progress array to avoid too many writes
            $count = Cache::get("import_progress_{$this->importId}_count");
            if ($count % 50 === 0) {
                $data = Cache::get("import_progress_{$this->importId}");
                if ($data) {
                    $data['current'] = $count;
                    Cache::put("import_progress_{$this->importId}", $data, 3600);
                }
            }
        }

        $data = [
            'periodo' => $this->periodo,
            'n_empresa' => $row['n_empresa'] ?? null,
            'id_tipo_plaza' => $row['id_tipo_plaza'] ?? null,
            'id_plaza_empleado' => $row['id_plaza_empleado'] ?? null,
            'id_empleado' => $row['id_empleado'] ?? null,
            'apellido_1' => $row['apellido_1'] ?? null,
            'apellido_2' => $row['apellido_2'] ?? null,
            'nombre' => $row['nombre'] ?? null,
            'id_legal' => $row['id_legal'] ?? null,
            'id_c_u_r_p_st' => $row['id_c_u_r_p_st'] ?? null,
            'fecha_ingreso_st' => $this->transformDate($row['fecha_ingreso_st'] ?? null),
            'fec_alta_empleado' => $this->transformDate($row['fec_alta_empleado'] ?? null),
            'fec_imputacion' => $this->transformDate($row['fec_imputacion'] ?? null),
            'fec_pago' => $this->transformDate($row['fec_pago'] ?? null),
            'id_forma_pago' => $row['id_forma_pago'] ?? null,
            'id_banco' => $row['id_banco'] ?? null,
            'num_cuenta' => $row['num_cuenta'] ?? null,
            'cancelado' => $row['cancelado'] ?? null,
            'id_tipo_puesto' => $row['id_tipo_puesto'] ?? null,
            'n_tipo_puesto' => $row['n_tipo_puesto'] ?? null,
            'id_tipo_tabulador' => $row['id_tipo_tabulador'] ?? null,
            'n_tipo_tabulador' => $row['n_tipo_tabulador'] ?? null,
            'id_turno' => $row['id_turno'] ?? null,
            'id_tipo_jornada' => $row['id_tipo_jornada'] ?? null,
            'id_horario' => $row['id_horario'] ?? null,
            'n_horario' => $row['n_horario'] ?? null,
            'hora_entrada_to' => $row['hora_entrada_to'] ?? null,
            'hora_salida_to' => $row['hora_salida_to'] ?? null,
            'hora_entrada_op' => $row['hora_entrada_op'] ?? null,
            'hora_salida_op' => $row['hora_salida_op'] ?? null,
            'num_horas' => $row['num_horas'] ?? null,
            'numero_ss' => $row['numero_ss'] ?? null,
            'id_plaza' => $row['id_plaza'] ?? null,
            'id_zona' => $row['id_zona'] ?? null,
            'id_puesto_plaza' => $row['id_puesto_plaza'] ?? null,
            'id_nivel' => $row['id_nivel'] ?? null,
            'id_sub_nivel' => $row['id_sub_nivel'] ?? null,
            'id_grupo_grado_nivel' => $row['id_grupo_grado_nivel'] ?? null,
            'id_integracion' => $row['id_integracion'] ?? null,
            'id_clasificacion' => $row['id_clasificacion'] ?? null,
            'id_rama' => $row['id_rama'] ?? null,
            'n_puesto_plaza' => $row['n_puesto_plaza'] ?? null,
            'id_centro_pago' => $row['id_centro_pago'] ?? null,
            'id_clave_servicio' => $row['id_clave_servicio'] ?? null,
            'n_clave_servicio' => $row['n_clave_servicio'] ?? null,
            'id_centro_trabajo' => $row['id_centro_trabajo'] ?? null,
            'n_centro_trabajo' => $row['n_centro_trabajo'] ?? null,
            'poblacion' => $row['poblacion'] ?? null,
            'n_municipio' => $row['n_municipio'] ?? null,
            'n_div_geografica' => $row['n_div_geografica'] ?? null,
            'id_area_generadora' => $row['id_area_generadora'] ?? null,
            'n_area_generadora' => $row['n_area_generadora'] ?? null,
            'id_div_geografica' => $row['id_div_geografica'] ?? null,
        ];

        $extra = [];
        $extra['sal_base'] = $row['sal_base'] ?? null;
        $extra['prev_social'] = $row['prev_social'] ?? null;
        $extra['compensacion'] = $row['compensacion'] ?? null;
        $extra['riesgo_prof'] = $row['riesgo_prof'] ?? null;
        $extra['concepto_nuevo_01'] = $row['concepto_nuevo_01'] ?? null;
        $extra['comp_x_antig'] = $row['comp_x_antig'] ?? null;
        $extra['quniquenio'] = $row['quniquenio'] ?? null;
        $extra['turno_opcional'] = $row['turno_opcional'] ?? null;
        $extra['percep_adic'] = $row['percep_adic'] ?? null;
        $extra['imp_hext_dob'] = $row['imp_hext_dob'] ?? null;
        $extra['imp_hext_tri'] = $row['imp_hext_tri'] ?? null;
        $extra['despensa'] = $row['despensa'] ?? null;
        $extra['honorarios'] = $row['honorarios'] ?? null;
        $extra['beca_pasan_pre'] = $row['beca_pasan_pre'] ?? null;
        $extra['ayuda_renta_bec'] = $row['ayuda_renta_bec'] ?? null;
        $extra['prima_dom_gra'] = $row['prima_dom_gra'] ?? null;
        $extra['p_domtrab_ex_st'] = $row['p_domtrab_ex_st'] ?? null;
        $extra['prima_dom_ex'] = $row['prima_dom_ex'] ?? null;
        $extra['p_domtrab_gr_st'] = $row['p_domtrab_gr_st'] ?? null;
        $extra['remun_guardias'] = $row['remun_guardias'] ?? null;
        $extra['dev_ded_indeb'] = $row['dev_ded_indeb'] ?? null;
        $extra['compensa_unica'] = $row['compensa_unica'] ?? null;
        $extra['grava_vac_st'] = $row['grava_vac_st'] ?? null;
        $extra['exento_vacacion'] = $row['exento_vacacion'] ?? null;
        $extra['beca_hijos'] = $row['beca_hijos'] ?? null;
        $extra['ayuda_lentes'] = $row['ayuda_lentes'] ?? null;
        $extra['premio_aniver'] = $row['premio_aniver'] ?? null;
        $extra['primo_10_mayo'] = $row['primo_10_mayo'] ?? null;
        $extra['premio_antig'] = $row['premio_antig'] ?? null;
        $extra['estimulo_adic'] = $row['estimulo_adic'] ?? null;
        $extra['dias_eco_no_disf'] = $row['dias_eco_no_disf'] ?? null;
        $extra['servs_event'] = $row['servs_event'] ?? null;
        $extra['paga_extra_1'] = $row['paga_extra_1'] ?? null;
        $extra['premio_moneda'] = $row['premio_moneda'] ?? null;
        $extra['est_prod_cal'] = $row['est_prod_cal'] ?? null;
        $extra['material_didac'] = $row['material_didac'] ?? null;
        $extra['estimulo_pro_mm'] = $row['estimulo_pro_mm'] ?? null;
        $extra['remun_suplencia'] = $row['remun_suplencia'] ?? null;
        $extra['bono_reyes'] = $row['bono_reyes'] ?? null;
        $extra['ayuda_transp'] = $row['ayuda_transp'] ?? null;
        $extra['ayuda_utiles'] = $row['ayuda_utiles'] ?? null;
        $extra['asigna_medico'] = $row['asigna_medico'] ?? null;
        $extra['comple_beca_med'] = $row['comple_beca_med'] ?? null;
        $extra['esti_asistencia'] = $row['esti_asistencia'] ?? null;
        $extra['esti_puntuali'] = $row['esti_puntuali'] ?? null;
        $extra['esti_desempeno'] = $row['esti_desempeno'] ?? null;
        $extra['esti_merito_rel'] = $row['esti_merito_rel'] ?? null;
        $extra['prem_ant_25_30'] = $row['prem_ant_25_30'] ?? null;
        $extra['ayuda_muerfam'] = $row['ayuda_muerfam'] ?? null;
        $extra['impr_tesis'] = $row['impr_tesis'] ?? null;
        $extra['apoyo_desa_capa'] = $row['apoyo_desa_capa'] ?? null;
        $extra['exceso_credito'] = $row['exceso_credito'] ?? null;
        $extra['vale_dec'] = $row['vale_dec'] ?? null;
        $extra['maternid_tot_st'] = $row['maternid_tot_st'] ?? null;
        $extra['ayuda_actualiza'] = $row['ayuda_actualiza'] ?? null;
        $extra['esti_trab_mes'] = $row['esti_trab_mes'] ?? null;
        $extra['ajuste_sueldo'] = $row['ajuste_sueldo'] ?? null;
        $extra['apoyo_deporte'] = $row['apoyo_deporte'] ?? null;
        $extra['mant_vehiculo'] = $row['mant_vehiculo'] ?? null;
        $extra['beca_residentes'] = $row['beca_residentes'] ?? null;
        $extra['ajuste_residen'] = $row['ajuste_residen'] ?? null;
        $extra['pago_retiro'] = $row['pago_retiro'] ?? null;
        $extra['premio_nac_anti'] = $row['premio_nac_anti'] ?? null;
        $extra['ajuste_calend'] = $row['ajuste_calend'] ?? null;
        $extra['comision'] = $row['comision'] ?? null;
        $extra['jornada_noct'] = $row['jornada_noct'] ?? null;
        $extra['prem_est_recomp'] = $row['prem_est_recomp'] ?? null;
        $extra['cobro_puest_ant'] = $row['cobro_puest_ant'] ?? null;
        $extra['prest_pago_exce'] = $row['prest_pago_exce'] ?? null;
        $extra['guardias_provac'] = $row['guardias_provac'] ?? null;
        $extra['suplen_provac'] = $row['suplen_provac'] ?? null;
        $extra['dev_nograv'] = $row['dev_nograv'] ?? null;
        $extra['grat_mes_beca'] = $row['grat_mes_beca'] ?? null;
        $extra['f_ahorro_ind_dv'] = $row['f_ahorro_ind_dv'] ?? null;
        $extra['f_ahorro_empr_dv'] = $row['f_ahorro_empr_dv'] ?? null;
        $extra['f_ahorro_sind_dv'] = $row['f_ahorro_sind_dv'] ?? null;
        $extra['f_ahorro_sind_rt'] = $row['f_ahorro_sind_rt'] ?? null;
        $extra['f_ahorro_seg_dv'] = $row['f_ahorro_seg_dv'] ?? null;
        $extra['f_ahorro_seg_rt'] = $row['f_ahorro_seg_rt'] ?? null;
        $extra['compen_serv_esp'] = $row['compen_serv_esp'] ?? null;
        $extra['prevenissste'] = $row['prevenissste'] ?? null;
        $extra['reco_serpub'] = $row['reco_serpub'] ?? null;
        $extra['rezago_quir'] = $row['rezago_quir'] ?? null;
        $extra['aport_pat_bruta'] = $row['aport_pat_bruta'] ?? null;
        $extra['compen_isr_agui'] = $row['compen_isr_agui'] ?? null;
        $extra['total_devengos'] = $row['total_devengos'] ?? null;
        $extra['ayuda_discapa'] = $row['ayuda_discapa'] ?? null;
        $extra['serv_soc_y_cult'] = $row['serv_soc_y_cult'] ?? null;
        $extra['seguro_cesantia'] = $row['seguro_cesantia'] ?? null;
        $extra['insasis'] = $row['insasis'] ?? null;
        $extra['servicio_medico'] = $row['servicio_medico'] ?? null;
        $extra['fondo_prestaci'] = $row['fondo_prestaci'] ?? null;
        $extra['ispt_ext'] = $row['ispt_ext'] ?? null;
        $extra['ispt'] = $row['ispt'] ?? null;
        $extra['c_sindic_local'] = $row['c_sindic_local'] ?? null;
        $extra['seguro_hip'] = $row['seguro_hip'] ?? null;
        $extra['credito_hip'] = $row['credito_hip'] ?? null;
        $extra['seguro_hip_aval'] = $row['seguro_hip_aval'] ?? null;
        $extra['credito_hip_aval'] = $row['credito_hip_aval'] ?? null;
        $extra['renta_multi'] = $row['renta_multi'] ?? null;
        $extra['prest_med_plazo'] = $row['prest_med_plazo'] ?? null;
        $extra['comision_auxil'] = $row['comision_auxil'] ?? null;
        $extra['cred_fovissste'] = $row['cred_fovissste'] ?? null;
        $extra['seg_vida_hid1'] = $row['seg_vida_hid1'] ?? null;
        $extra['seg_vida_hid2'] = $row['seg_vida_hid2'] ?? null;
        $extra['seg_institucion'] = $row['seg_institucion'] ?? null;
        $extra['seg_retiro'] = $row['seg_retiro'] ?? null;
        $extra['cuota_dep'] = $row['cuota_dep'] ?? null;
        $extra['total_p_alimen'] = $row['total_p_alimen'] ?? null;
        $extra['retardos'] = $row['retardos'] ?? null;
        $extra['rei_sdo_cob_ind'] = $row['rei_sdo_cob_ind'] ?? null;
        $extra['otras_deduc'] = $row['otras_deduc'] ?? null;
        $extra['prest_auto_sup'] = $row['prest_auto_sup'] ?? null;
        $extra['seg_auto_ca'] = $row['seg_auto_ca'] ?? null;
        $extra['resto_enfermeda'] = $row['resto_enfermeda'] ?? null;
        $extra['f_ahorro_indi'] = $row['f_ahorro_indi'] ?? null;
        $extra['prest_auto_med'] = $row['prest_auto_med'] ?? null;
        $extra['aport_vol_sar'] = $row['aport_vol_sar'] ?? null;
        $extra['ahorro_solid'] = $row['ahorro_solid'] ?? null;
        $extra['serv_gastos_fun'] = $row['serv_gastos_fun'] ?? null;
        $extra['seg_auto_nprov'] = $row['seg_auto_nprov'] ?? null;
        $extra['prest_adicional'] = $row['prest_adicional'] ?? null;
        $extra['seg_danios_fov'] = $row['seg_danios_fov'] ?? null;
        $extra['seg_vida_int'] = $row['seg_vida_int'] ?? null;
        $extra['cred_ahorra_ya'] = $row['cred_ahorra_ya'] ?? null;
        $extra['adeud_pens_alim'] = $row['adeud_pens_alim'] ?? null;
        $extra['recup_viaticos'] = $row['recup_viaticos'] ?? null;
        $extra['aten_med_no_der'] = $row['aten_med_no_der'] ?? null;
        $extra['desc_ant_sueldo'] = $row['desc_ant_sueldo'] ?? null;
        $extra['respon_dano_pat'] = $row['respon_dano_pat'] ?? null;
        $extra['respon_dif_suel'] = $row['respon_dif_suel'] ?? null;
        $extra['exc_pag_mes_a'] = $row['exc_pag_mes_a'] ?? null;
        $extra['sancion_adminis'] = $row['sancion_adminis'] ?? null;
        $extra['sancion_pecunia'] = $row['sancion_pecunia'] ?? null;
        $extra['campo_5'] = $row['campo_5'] ?? null;
        $extra['aport_pat_neta'] = $row['aport_pat_neta'] ?? null;
        $extra['seg_argos'] = $row['seg_argos'] ?? null;
        $extra['fonacot'] = $row['fonacot'] ?? null;
        $extra['credipresto'] = $row['credipresto'] ?? null;
        $extra['c_sindic_nacion'] = $row['c_sindic_nacion'] ?? null;
        $extra['desc_pago_exces'] = $row['desc_pago_exces'] ?? null;
        $extra['exceso_licencias'] = $row['exceso_licencias'] ?? null;
        $extra['desc_optica'] = $row['desc_optica'] ?? null;
        $extra['anticipo'] = $row['anticipo'] ?? null;
        $extra['credito_etesa'] = $row['credito_etesa'] ?? null;
        $extra['fimubac'] = $row['fimubac'] ?? null;
        $extra['gasmedmay'] = $row['gasmedmay'] ?? null;
        $extra['ispt_mant_vehi'] = $row['ispt_mant_vehi'] ?? null;
        $extra['isr_patronal_ss'] = $row['isr_patronal_ss'] ?? null;
        $extra['seg_vida_fam'] = $row['seg_vida_fam'] ?? null;
        $extra['presta_auto_fun'] = $row['presta_auto_fun'] ?? null;
        $extra['gastos_funer'] = $row['gastos_funer'] ?? null;
        $extra['prestamo_ssi'] = $row['prestamo_ssi'] ?? null;
        $extra['publiseg'] = $row['publiseg'] ?? null;
        $extra['seg_qualitas'] = $row['seg_qualitas'] ?? null;
        $extra['amort_fonacot'] = $row['amort_fonacot'] ?? null;
        $extra['reten_judicial'] = $row['reten_judicial'] ?? null;
        $extra['seguro_salud'] = $row['seguro_salud'] ?? null;
        $extra['seguro_ries_trab'] = $row['seguro_ries_trab'] ?? null;
        $extra['seguro_inval_y_vida'] = $row['seguro_inval_y_vida'] ?? null;
        $extra['campo_4'] = $row['campo_4'] ?? null;
        $extra['seg_auto_cop'] = $row['seg_auto_cop'] ?? null;
        $extra['viaja_cdmex'] = $row['viaja_cdmex'] ?? null;
        $extra['campo_6'] = $row['campo_6'] ?? null;
        $extra['sindicato_tres'] = $row['sindicato_tres'] ?? null;
        $extra['sindicato_cuatro'] = $row['sindicato_cuatro'] ?? null;
        $extra['sindicato_cinco'] = $row['sindicato_cinco'] ?? null;
        $extra['concepto_nuevo_02'] = $row['concepto_nuevo_02'] ?? null;
        $extra['concepto_nuevo_03'] = $row['concepto_nuevo_03'] ?? null;
        $extra['isr_aguin'] = $row['isr_aguin'] ?? null;
        $extra['isr_ajuste_ant'] = $row['isr_ajuste_ant'] ?? null;
        $extra['dev_isr_ajuste_ant'] = $row['dev_isr_ajuste_ant'] ?? null;
        $extra['total_retenido_a'] = $row['total_retenido_a'] ?? null;
        $extra['liquido'] = $row['liquido'] ?? null;
        $data['nomina_data'] = $extra;

        return new Employee($data);
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    private function transformDate($value)
    {
        if (!$value) return null;
        
        if (is_numeric($value)) {
            try {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Exception $e) {
                return $value;
            }
        }
        
        return $value;
    }
}
