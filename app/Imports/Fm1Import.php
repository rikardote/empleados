<?php

namespace App\Imports;

use App\Models\Fm1Form;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Fm1Import — Importa registros de FM1 desde Excel
 *
 * Las columnas del Excel deben coincidir (insensiblemente) con los campos del FM1.
 * Columnas esperadas: nombre, num_empleado, rfc, curp, sexo, escolaridad, cedula,
 * domicilio, hijos, nacionalidad, cod_tipo_movimiento, tipo_mov, fecha_movimiento,
 * fecha_final, codigo_puesto, nivel_subnivel, denominacion_puesto, numero_plaza,
 * tipo_plaza, ocupacion, estatus_plaza, unidad_administrativa,
 * unidad_administrativa_denominacion, adscripcion, adscripcion_denominacion,
 * adscripcion_fisica, adscripcion_fisica_denominacion, servicio,
 * servicio_denominacion, codigo_turno, codigo_turno_descripcion, jornada,
 * horario_codigo, horario_entrada1, horario_salida1, horario_entrada2,
 * horario_salida2, observaciones, turno_opcional, percepcion_adicional,
 * riesgos_profesionales, mando, enlace_alta_responsabilidad, enlace,
 * operativo, rama_medica, nombre_ant, num_empleado_ant, cod_movi_ant,
 * tipo_mov_ant, fecha_inicio_ant, fecha_fin_ant, turno_opcional_ant,
 * percepcion_adicional_ant, riesgos_prof_ant, nombre_trab_ant,
 * titular_area, cargo_titular_area, responsable_admvo, cargo_responsable_admvo,
 * titular_centro, cargo_titular_centro
 */
class Fm1Import implements ToCollection, WithHeadingRow, WithChunkReading, WithCustomCsvSettings
{
    private ?int $batchId;
    private int $importedCount = 0;

    private const DATE_COLUMNS = [
        'fecha_movimiento', 'fecha_final', 'fecha_inicio_ant', 'fecha_fin_ant',
    ];

    // Todos los campos mapeables del formulario FM1
    private const FM1_COLUMNS = [
        'nombre', 'num_empleado', 'rfc', 'curp', 'sexo', 'escolaridad', 'cedula',
        'domicilio', 'hijos', 'nacionalidad', 'cod_tipo_movimiento', 'tipo_mov',
        'fecha_movimiento', 'fecha_final',
        // Plaza
        'codigo_puesto', 'nivel_subnivel', 'denominacion_puesto', 'numero_plaza',
        'tipo_plaza', 'ocupacion', 'estatus_plaza',
        'unidad_administrativa', 'unidad_administrativa_denominacion',
        'adscripcion', 'adscripcion_denominacion',
        'adscripcion_fisica', 'adscripcion_fisica_denominacion',
        'servicio', 'servicio_denominacion',
        'codigo_turno', 'codigo_turno_descripcion', 'jornada',
        'horario_codigo', 'horario_entrada1', 'horario_salida1',
        'horario_entrada2', 'horario_salida2', 'observaciones',
        'turno_opcional', 'percepcion_adicional', 'riesgos_profesionales',
        'mando', 'enlace_alta_responsabilidad', 'enlace', 'operativo', 'rama_medica',
        // Antecedentes
        'nombre_ant', 'num_empleado_ant', 'cod_movi_ant', 'tipo_mov_ant',
        'fecha_inicio_ant', 'fecha_fin_ant',
        'turno_opcional_ant', 'percepcion_adicional_ant', 'riesgos_prof_ant',
        'nombre_trab_ant',
        // Firmas
        'titular_area', 'cargo_titular_area',
        'responsable_admvo', 'cargo_responsable_admvo',
        'titular_centro', 'cargo_titular_centro',
    ];

    public function __construct(?int $batchId = null)
    {
        $this->batchId = $batchId;
    }

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        $now = now()->toDateTimeString();
        $records = [];

        foreach ($rows as $row) {
            $row = $row->toArray();

            // Skip rows where nombre is empty (required field)
            if (empty($row['nombre'])) {
                continue;
            }

            $record = ['import_batch_id' => $this->batchId];

            foreach (self::FM1_COLUMNS as $col) {
                $record[$col] = $row[$col] ?? null;
            }

            // Transform dates
            foreach (self::DATE_COLUMNS as $dateCol) {
                if (!empty($record[$dateCol])) {
                    $record[$dateCol] = $this->transformDate($record[$dateCol]);
                }
            }

            $record['created_at'] = $now;
            $record['updated_at'] = $now;

            $records[] = $record;
        }

        if (!empty($records)) {
            \Illuminate\Support\Facades\DB::table('fm1_forms')->insert($records);
            $this->importedCount += count($records);
        }
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'Windows-1252'
        ];
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    private function transformDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                $timestamp = Date::excelToTimestamp((float) $value);
                return date('Y-m-d', $timestamp);
            } catch (\Throwable) {
                return null;
            }
        }

        // Try to parse common date formats
        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return (string) $value;
        }
    }
}
