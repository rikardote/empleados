<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * EmployeesImport – versión de alto rendimiento
 *
 * Optimizaciones aplicadas:
 *  1. ToCollection en lugar de ToModel  → sin sobrecarga de Eloquent por fila.
 *  2. DB::table()->insert() en bulk      → 1 query por chunk (vs N queries).
 *  3. chunkSize grande (1 000)           → menos round-trips con el disco/lectura.
 *  4. Cache actualizada 1 vez por chunk  → no 2 llamadas a Redis por fila.
 *  5. transformDate() sin instancia OOP  → parsing mínimo via Carbon::parse.
 *  6. Columna extra precalculada 1 vez   → no se itera el array de llaves innecesariamente.
 */
class EmployeesImport implements ToCollection, WithHeadingRow, WithEvents, WithChunkReading
{
    private string|null $periodo;
    private string|null $importId;
    private int $processedRows = 0;

    // Columnas que van directamente a la tabla `employees`
    private const MAIN_COLUMNS = [
        'periodo', 'n_empresa', 'id_tipo_plaza', 'id_plaza_empleado', 'id_empleado',
        'apellido_1', 'apellido_2', 'nombre', 'id_legal', 'id_c_u_r_p_st',
        'fecha_ingreso_st', 'fec_alta_empleado', 'fec_imputacion', 'fec_pago',
        'id_forma_pago', 'id_banco', 'num_cuenta', 'cancelado',
        'id_tipo_puesto', 'n_tipo_puesto', 'id_tipo_tabulador', 'n_tipo_tabulador',
        'id_turno', 'id_tipo_jornada', 'id_horario', 'n_horario',
        'hora_entrada_to', 'hora_salida_to', 'hora_entrada_op', 'hora_salida_op',
        'num_horas', 'numero_ss', 'id_plaza', 'id_zona', 'id_puesto_plaza',
        'id_nivel', 'id_sub_nivel', 'id_grupo_grado_nivel', 'id_integracion',
        'id_clasificacion', 'id_rama', 'n_puesto_plaza', 'id_centro_pago',
        'id_clave_servicio', 'n_clave_servicio', 'id_centro_trabajo', 'n_centro_trabajo',
        'poblacion', 'n_municipio', 'n_div_geografica',
        'id_area_generadora', 'n_area_generadora', 'id_div_geografica',
    ];

    // Columnas de nómina que se almacenan en `nomina_data` (JSON)
    private const NOMINA_COLUMNS = [
        'sal_base', 'prev_social', 'compensacion', 'riesgo_prof', 'concepto_nuevo_01',
        'comp_x_antig', 'quniquenio', 'turno_opcional', 'percep_adic', 'imp_hext_dob',
        'imp_hext_tri', 'despensa', 'honorarios', 'beca_pasan_pre', 'ayuda_renta_bec',
        'prima_dom_gra', 'p_domtrab_ex_st', 'prima_dom_ex', 'p_domtrab_gr_st',
        'remun_guardias', 'dev_ded_indeb', 'compensa_unica', 'grava_vac_st',
        'exento_vacacion', 'beca_hijos', 'ayuda_lentes', 'premio_aniver', 'primo_10_mayo',
        'premio_antig', 'estimulo_adic', 'dias_eco_no_disf', 'servs_event', 'paga_extra_1',
        'premio_moneda', 'est_prod_cal', 'material_didac', 'estimulo_pro_mm',
        'remun_suplencia', 'bono_reyes', 'ayuda_transp', 'ayuda_utiles', 'asigna_medico',
        'comple_beca_med', 'esti_asistencia', 'esti_puntuali', 'esti_desempeno',
        'esti_merito_rel', 'prem_ant_25_30', 'ayuda_muerfam', 'impr_tesis',
        'apoyo_desa_capa', 'exceso_credito', 'vale_dec', 'maternid_tot_st',
        'ayuda_actualiza', 'esti_trab_mes', 'ajuste_sueldo', 'apoyo_deporte',
        'mant_vehiculo', 'beca_residentes', 'ajuste_residen', 'pago_retiro',
        'premio_nac_anti', 'ajuste_calend', 'comision', 'jornada_noct', 'prem_est_recomp',
        'cobro_puest_ant', 'prest_pago_exce', 'guardias_provac', 'suplen_provac',
        'dev_nograv', 'grat_mes_beca', 'f_ahorro_ind_dv', 'f_ahorro_empr_dv',
        'f_ahorro_sind_dv', 'f_ahorro_sind_rt', 'f_ahorro_seg_dv', 'f_ahorro_seg_rt',
        'compen_serv_esp', 'prevenissste', 'reco_serpub', 'rezago_quir', 'aport_pat_bruta',
        'compen_isr_agui', 'total_devengos', 'ayuda_discapa', 'serv_soc_y_cult',
        'seguro_cesantia', 'insasis', 'servicio_medico', 'fondo_prestaci', 'ispt_ext',
        'ispt', 'c_sindic_local', 'seguro_hip', 'credito_hip', 'seguro_hip_aval',
        'credito_hip_aval', 'renta_multi', 'prest_med_plazo', 'comision_auxil',
        'cred_fovissste', 'seg_vida_hid1', 'seg_vida_hid2', 'seg_institucion', 'seg_retiro',
        'cuota_dep', 'total_p_alimen', 'retardos', 'rei_sdo_cob_ind', 'otras_deduc',
        'prest_auto_sup', 'seg_auto_ca', 'resto_enfermeda', 'f_ahorro_indi',
        'prest_auto_med', 'aport_vol_sar', 'ahorro_solid', 'serv_gastos_fun',
        'seg_auto_nprov', 'prest_adicional', 'seg_danios_fov', 'seg_vida_int',
        'cred_ahorra_ya', 'adeud_pens_alim', 'recup_viaticos', 'aten_med_no_der',
        'desc_ant_sueldo', 'respon_dano_pat', 'respon_dif_suel', 'exc_pag_mes_a',
        'sancion_adminis', 'sancion_pecunia', 'campo_5', 'aport_pat_neta', 'seg_argos',
        'fonacot', 'credipresto', 'c_sindic_nacion', 'desc_pago_exces', 'exceso_licencias',
        'desc_optica', 'anticipo', 'credito_etesa', 'fimubac', 'gasmedmay',
        'ispt_mant_vehi', 'isr_patronal_ss', 'seg_vida_fam', 'presta_auto_fun',
        'gastos_funer', 'prestamo_ssi', 'publiseg', 'seg_qualitas', 'amort_fonacot',
        'reten_judicial', 'seguro_salud', 'seguro_ries_trab', 'seguro_inval_y_vida',
        'campo_4', 'seg_auto_cop', 'viaja_cdmex', 'campo_6', 'sindicato_tres',
        'sindicato_cuatro', 'sindicato_cinco', 'concepto_nuevo_02', 'concepto_nuevo_03',
        'isr_aguin', 'isr_ajuste_ant', 'dev_isr_ajuste_ant', 'total_retenido_a', 'liquido',
    ];

    // Columnas de fecha que necesitan transformación
    private const DATE_COLUMNS = [
        'fecha_ingreso_st', 'fec_alta_empleado', 'fec_imputacion', 'fec_pago',
    ];

    public function __construct(?string $periodo = null, ?string $importId = null)
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
                    $total = is_array($totalRows) ? array_sum($totalRows) : (int) $totalRows;
                    Cache::put("import_progress_{$this->importId}", [
                        'total'   => $total,
                        'current' => 0,
                        'status'  => 'processing',
                    ], 3600);
                    Cache::put("import_progress_{$this->importId}_count", 0, 3600);
                }
            },
            AfterImport::class => function (AfterImport $event) {
                if ($this->importId) {
                    $data = Cache::get("import_progress_{$this->importId}");
                    if ($data) {
                        $data['current'] = $data['total'];
                        $data['status']  = 'completed';
                        Cache::put("import_progress_{$this->importId}", $data, 3600);
                    }
                }
            },
        ];
    }

    /**
     * Recibe un chunk de filas como Collection y las inserta en bulk con una sola query.
     */
    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        $now = now()->toDateTimeString();
        $records = [];

        foreach ($rows as $row) {
            $row = $row->toArray();

            // ── Columnas principales ───────────────────────────────────────
            $record = ['periodo' => $this->periodo];

            foreach (self::MAIN_COLUMNS as $col) {
                if ($col === 'periodo') continue; // ya asignado arriba
                $record[$col] = $row[$col] ?? null;
            }

            // Transformar fechas sin instanciar objetos Eloquent
            foreach (self::DATE_COLUMNS as $dateCol) {
                $record[$dateCol] = $this->transformDate($record[$dateCol] ?? null);
            }

            // ── Columnas de nómina (JSON) ──────────────────────────────────
            $nomina = [];
            foreach (self::NOMINA_COLUMNS as $col) {
                $nomina[$col] = $row[$col] ?? null;
            }
            $record['nomina_data'] = json_encode($nomina);

            // Timestamps de Eloquent (la tabla los espera si usas timestamps=true)
            $record['created_at'] = $now;
            $record['updated_at'] = $now;

            $records[] = $record;
        }

        // ── Insert masivo: 1 query por chunk ───────────────────────────────
        DB::table('employees')->insert($records);

        // ── Actualizar progreso 1 vez por chunk ────────────────────────────
        if ($this->importId) {
            $this->processedRows += count($records);
            Cache::put("import_progress_{$this->importId}_count", $this->processedRows, 3600);

            $data = Cache::get("import_progress_{$this->importId}");
            if ($data) {
                $data['current'] = $this->processedRows;
                Cache::put("import_progress_{$this->importId}", $data, 3600);
            }
        }
    }

    /**
     * Tamaño del chunk leído desde el disco por vez.
     * 1 000 filas = buen equilibrio entre memoria y velocidad.
     */
    public function chunkSize(): int
    {
        return 1000;
    }

    /**
     * Convierte un valor de fecha Excel (serial numérico o string) a 'Y-m-d'.
     * Evita instanciar Carbon/DateTime salvo cuando es estrictamente necesario.
     */
    private function transformDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                // fromExcelDate() devuelve un timestamp PHP directamente
                $timestamp = Date::excelToTimestamp((float) $value);
                return date('Y-m-d', $timestamp);
            } catch (\Throwable) {
                return (string) $value;
            }
        }

        return (string) $value;
    }
}
