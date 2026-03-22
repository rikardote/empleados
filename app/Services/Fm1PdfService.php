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
        $this->writeText($pdf, 53.3, 89.8, $form->unidad_administrativa, null, 'L', false, false);
        $this->writeText($pdf, 78.5, 90.5, $form->unidad_administrativa_denominacion);
        $this->writeText($pdf, 51.4, 95.1, $form->adscripcion, null, 'L', false, false);
        $this->writeText($pdf, 78.2, 95.4, $form->adscripcion_denominacion);
        $this->writeText($pdf, 51.3, 99.9, $form->adscripcion_fisica, null, 'L', false, false);
        $this->writeText($pdf, 78.5, 100.5, $form->adscripcion_fisica_denominacion);
        $this->writeText($pdf, 51.0, 104.6, $form->servicio, null, 'L', false, false);
        $this->writeText($pdf, 78.9, 104.8, $form->servicio_denominacion);

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
            $this->writeText($pdf, 121.8, 125.0, $form->observaciones, 7);
        }

        // Indicadores (X o ---)
        $this->fillIndicators($pdf, $form);
    }

    private function fillIndicators(Fpdi $pdf, Fm1Form $form): void
    {
        // Indicadores lado derecho
        if (!empty($form->turno_opcional))
            $this->writeText($pdf, 108, 131, in_array(strtoupper(trim($form->turno_opcional)), ['S','1','SI','X']) ? 'X' : '---', null, 'L', false, false); // turno_opcional
        if (!empty($form->percepcion_adicional))
            $this->writeText($pdf, 108, 135, in_array(strtoupper(trim($form->percepcion_adicional)), ['S','1','SI','X']) ? 'X' : '---', null, 'L', false, false); // percepcion_adicional
        if (!empty($form->riesgos_profesionales))
            $this->writeText($pdf, 108, 139, in_array(strtoupper(trim($form->riesgos_profesionales)), ['S','1','SI','X']) ? 'X' : '---', null, 'L', false, false); // riesgos_profesionales

        // Indicadores lado izquierdo
        if (!empty($form->mando))
            $this->writeText($pdf, 68, 131, in_array(strtoupper(trim($form->mando)), ['S','1','SI','X']) ? 'X' : '---', null, 'L', false, false); // mando
        if (!empty($form->enlace_alta_responsabilidad))
            $this->writeText($pdf, 68, 135, in_array(strtoupper(trim($form->enlace_alta_responsabilidad)), ['S','1','SI','X']) ? 'X' : '---', null, 'L', false, false); // enlace_alta_responsabilidad
        if (!empty($form->enlace))
            $this->writeText($pdf, 68, 139, in_array(strtoupper(trim($form->enlace)), ['S','1','SI','X']) ? 'X' : '---', null, 'L', false, false); // enlace
        if (!empty($form->operativo))
            $this->writeText($pdf, 68, 143, in_array(strtoupper(trim($form->operativo)), ['S','1','SI','X']) ? 'X' : '---', null, 'L', false, false); // operativo
        if (!empty($form->rama_medica))
            $this->writeText($pdf, 68, 147, in_array(strtoupper(trim($form->rama_medica)), ['S','1','SI','X']) ? 'X' : '---', null, 'L', false, false); // rama_medica
    }

    private function fillOccupancyAndSignatures(Fpdi $pdf, Fm1Form $form): void
    {
        // Antecedentes
        $this->writeText($pdf, 30, 162, $form->nombre_ant);
        $this->writeText($pdf, 45, 167, $form->num_empleado_ant, null, 'L', false, false);
        $this->writeText($pdf, 42, 172, $form->cod_movi_ant, null, 'L', false, false);
        $this->writeText($pdf, 88.4, 171.7, $form->tipo_mov_ant);

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
        if (!empty($form->turno_opcional_ant))
            $this->writeText($pdf, 90, 182, in_array(strtoupper(trim($form->turno_opcional_ant)), ['S','1','SI','X']) ? 'X' : '---', null, 'L', false, false); // turno_opcional_ant
        if (!empty($form->percepcion_adicional_ant))
            $this->writeText($pdf, 148, 182, in_array(strtoupper(trim($form->percepcion_adicional_ant)), ['S','1','SI','X']) ? 'X' : '---', null, 'L', false, false); // percepcion_adicional_ant
        if (!empty($form->riesgos_prof_ant))
            $this->writeText($pdf, 200, 182, in_array(strtoupper(trim($form->riesgos_prof_ant)), ['S','1','SI','X']) ? 'X' : '---', null, 'L', false, false); // riesgos_prof_ant

        // Firmas
        $pdf->SetAutoPageBreak(false);

        // Titular Área
        $this->writeText($pdf, 7.5, 253.5, $form->titular_area, 9, 'C', true, true, 65, 4); // titular_area
        $this->writeText($pdf, 9.1, 257.2, $form->cargo_titular_area, 6.5, 'C', true, true, 65, 3); // cargo_titular_area

        // Responsable Admvo.
        $this->writeText($pdf, 75.0, 254.2, $form->responsable_admvo, 9, 'C', true, true, 65, 4); // responsable_admvo
        $this->writeText($pdf, 75, 258.5, $form->cargo_responsable_admvo, 6.5, 'C', true, true, 65, 3); // cargo_responsable_admvo

        // Titular Centro
        $this->writeText($pdf, 142.5, 253.4, $form->titular_centro, 9, 'C', true, true, 65, 4); // titular_centro
        $this->writeText($pdf, 142.5, 257.0, $form->cargo_titular_centro, 6.5, 'C', true, true, 65, 3); // cargo_titular_centro

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
