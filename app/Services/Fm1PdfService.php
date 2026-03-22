<?php

namespace App\Services;

use App\Models\Fm1Form;
use setasign\Fpdi\Fpdi;

class Fm1PdfService
{
    /**
     * Genera el contenido del PDF llenando la plantilla con los datos del registro.
     *
     * @param Fm1Form $formRecord
     * @return string Contenido binario del PDF
     */
    public function generate(Fm1Form $formRecord): string
    {
        $pdf = new Fpdi();
        $sourceFile = public_path('fm1_v1_4.pdf');

        if (!file_exists($sourceFile)) {
            throw new \Exception('El archivo base fm1_v1_4.pdf no se encuentra en la carpeta public.');
        }

        $pageCount = $pdf->setSourceFile($sourceFile);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);

            $pdf->SetFont('Helvetica');
            $pdf->SetFontSize(9);
            $pdf->SetTextColor(0, 0, 0);

            if ($pageNo == 1) {
                $this->fillPageOne($pdf, $formRecord);
            }
        }

        return $pdf->Output('S');
    }

    /**
     * Llena la primera página del FM1.
     */
    private function fillPageOne(Fpdi $pdf, Fm1Form $form): void
    {
        // 1. DATOS DEL TRABAJADOR
        $this->writeText($pdf, 29, 34.5, $form->nombre);
        $this->writeText($pdf, 112, 28, $form->num_empleado, null, 'L', false, false); // No upper para IDs
        $this->writeText($pdf, 20, 39, $form->rfc);
        $this->writeText($pdf, 60, 39, $form->curp);
        $this->writeText($pdf, 109, 39, $form->sexo);
        $this->writeText($pdf, 80, 43, $form->nacionalidad);
        $this->writeText($pdf, 32, 49, $form->escolaridad);
        $this->writeText($pdf, 95, 51, $form->cedula);
        $this->writeText($pdf, 32, 53.0, $form->domicilio, null, 'L', true, true, 104); // MultiCell con ancho 104

        // 2. MOVIMIENTO
        $this->writeText($pdf, 195, 39, $form->cod_tipo_movimiento); // Código (Top-right)
        $this->writeText($pdf, 139, 39, $form->tipo_mov);           // Descripción (User-defined coord)

        if ($form->fecha_movimiento) {
            $this->writeText($pdf, 139, 59, $form->fecha_movimiento->format('d'), null, 'L', false, false);
            $this->writeText($pdf, 150, 59, $form->fecha_movimiento->format('m'), null, 'L', false, false);
            $this->writeText($pdf, 160, 59, $form->fecha_movimiento->format('Y'), null, 'L', false, false);
        }

        if ($form->fecha_final) {
            $this->writeText($pdf, 175, 59, $form->fecha_final->format('d'), null, 'L', false, false);
            $this->writeText($pdf, 186, 59, $form->fecha_final->format('m'), null, 'L', false, false);
            $this->writeText($pdf, 196, 59, $form->fecha_final->format('Y'), null, 'L', false, false);
        }

        // 3. DATOS DE LA PLAZA
        $this->fillPlazaData($pdf, $form);

        // 4. ANTECEDENTES Y FIRMAS
        $this->fillOccupancyAndSignatures($pdf, $form);
    }

    private function fillPlazaData(Fpdi $pdf, Fm1Form $form): void
    {
        if ($form->codigo_puesto) {
            $this->writeText($pdf, 51, 72, $form->codigo_puesto . " / " . ($form->nivel_subnivel ?? ''), null, 'L', false, false);
        }
        $this->writeText($pdf, 112, 72, $form->denominacion_puesto, 7);
        $this->writeText($pdf, 30, 77, $form->numero_plaza, null, 'L', false, false);
        $this->writeText($pdf, 182, 77, $form->tipo_plaza);
        $this->writeText($pdf, 30, 82, $form->ocupacion);
        $this->writeText($pdf, 75, 82, $form->estatus_plaza);

        // Ubicación
        $this->writeText($pdf, 50, 91, $form->unidad_administrativa, null, 'L', false, false);
        $this->writeText($pdf, 80, 91, $form->unidad_administrativa_denominacion);
        $this->writeText($pdf, 50, 96, $form->adscripcion, null, 'L', false, false);
        $this->writeText($pdf, 80, 96, $form->adscripcion_denominacion);
        $this->writeText($pdf, 50, 101, $form->adscripcion_fisica, null, 'L', false, false);
        $this->writeText($pdf, 80, 100, $form->adscripcion_fisica_denominacion);
        $this->writeText($pdf, 50, 105, $form->servicio, null, 'L', false, false);
        $this->writeText($pdf, 80, 105, $form->servicio_denominacion);

        // Jornada/Horarios
        $this->writeText($pdf, 22, 113, $form->codigo_turno, null, 'L', false, false);
        $this->writeText($pdf, 30, 113, $form->codigo_turno_descripcion);
        $this->writeText($pdf, 103, 113, $form->jornada);
        $this->writeText($pdf, 132, 113, $form->horario_codigo, null, 'L', false, false);
        $this->writeText($pdf, 148, 113, $form->horario_entrada1, null, 'L', false, false);
        $this->writeText($pdf, 164, 113, $form->horario_salida1, null, 'L', false, false);
        $this->writeText($pdf, 178, 113, $form->horario_entrada2, null, 'L', false, false);
        $this->writeText($pdf, 194, 113, $form->horario_salida2, null, 'L', false, false);

        if ($form->observaciones) {
            $this->writeText($pdf, 30, 130, $form->observaciones, 7);
        }

        // Indicadores (X o ---)
        $this->fillIndicators($pdf, $form);
    }

    private function fillIndicators(Fpdi $pdf, Fm1Form $form): void
    {
        $indicators = [
            'turno_opcional'              => [108, 131],
            'percepcion_adicional'        => [108, 135],
            'riesgos_profesionales'       => [108, 139],
            'mando'                        => [68, 131],
            'enlace_alta_responsabilidad' => [68, 135],
            'enlace'                       => [68, 139],
            'operativo'                    => [68, 143],
            'rama_medica'                  => [68, 147],
        ];

        foreach ($indicators as $field => $coords) {
            $val = $form->$field;
            if (!empty($val)) {
                $isX = in_array(strtoupper(trim($val)), ['S', '1', 'SI', 'X']);
                $this->writeText($pdf, $coords[0], $coords[1], $isX ? 'X' : '---', null, 'L', false, false);
            }
        }
    }

    private function fillOccupancyAndSignatures(Fpdi $pdf, Fm1Form $form): void
    {
        // Antecedentes
        $this->writeText($pdf, 30, 162, $form->nombre_ant);
        $this->writeText($pdf, 45, 167, $form->num_empleado_ant, null, 'L', false, false);
        $this->writeText($pdf, 42, 172, $form->cod_movi_ant, null, 'L', false, false);
        $this->writeText($pdf, 85, 172, $form->tipo_mov_ant);

        if ($form->fecha_inicio_ant) {
            $this->writeText($pdf, 139, 176, $form->fecha_inicio_ant->format('d'), null, 'L', false, false);
            $this->writeText($pdf, 150, 176, $form->fecha_inicio_ant->format('m'), null, 'L', false, false);
            $this->writeText($pdf, 160, 176, $form->fecha_inicio_ant->format('Y'), null, 'L', false, false);
        }
        if ($form->fecha_fin_ant) {
            $this->writeText($pdf, 175, 176, $form->fecha_fin_ant->format('d'), null, 'L', false, false);
            $this->writeText($pdf, 186, 176, $form->fecha_fin_ant->format('m'), null, 'L', false, false);
            $this->writeText($pdf, 196, 176, $form->fecha_fin_ant->format('Y'), null, 'L', false, false);
        }

        $this->writeText($pdf, 45, 218, $form->nombre_trab_ant, null, 'C', true, true, 120, 4);

        // Indicadores Antecedentes
        $antIndicators = [
            'turno_opcional_ant'       => [90, 182],
            'percepcion_adicional_ant' => [148, 182],
            'riesgos_prof_ant'         => [200, 182],
        ];
        foreach ($antIndicators as $field => $coords) {
            $val = $form->$field;
            if (!empty($val)) {
                $isX = in_array(strtoupper(trim($val)), ['S', '1', 'SI', 'X']);
                $this->writeText($pdf, $coords[0], $coords[1], $isX ? 'X' : '---', null, 'L', false, false);
            }
        }

        // Firmas
        $pdf->SetAutoPageBreak(false);
        $w = 65;
        $y = 252.5;

        // Titular Área
        $this->writeText($pdf, 7.5, $y, $form->titular_area, 9, 'C', true, true, $w, 4);
        $this->writeText($pdf, 7.5, $pdf->GetY(), $form->cargo_titular_area, 6.5, 'C', true, true, $w, 3);

        // Responsable Admvo.
        $this->writeText($pdf, 75, $y, $form->responsable_admvo, 9, 'C', true, true, $w, 4);
        $this->writeText($pdf, 75, $pdf->GetY(), $form->cargo_responsable_admvo, 6.5, 'C', true, true, $w, 3);

        // Titular Centro
        $this->writeText($pdf, 142.5, $y, $form->titular_centro, 9, 'C', true, true, $w, 4);
        $this->writeText($pdf, 142.5, $pdf->GetY(), $form->cargo_titular_centro, 6.5, 'C', true, true, $w, 3);

        $pdf->SetAutoPageBreak(true, 20);
    }

    /**
     * Helper para escribir texto con encoding y formato consistente.
     */
    private function writeText(Fpdi $pdf, $x, $y, $text, $fontSize = null, $align = 'L', $multiCell = false, $upper = true, $w = 0, $h = 4): void
    {
        if (is_null($text) || $text === '') return;

        $pdf->SetXY($x, $y);
        if ($fontSize) $pdf->SetFontSize($fontSize);

        $processedText = $upper ? mb_strtoupper($text) : $text;
        $encodedText = iconv('UTF-8', 'windows-1252//TRANSLIT', $processedText);

        if ($multiCell) {
            $pdf->MultiCell($w, $h, $encodedText, 0, $align);
        } else {
            $pdf->Write(0, $encodedText);
        }

        if ($fontSize) $pdf->SetFontSize(9); // Restaurar default
    }
}
