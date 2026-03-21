<?php

namespace App\Http\Controllers;

use App\Models\Fm1Form;
use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;

class PdfFillingController extends Controller
{
    /**
     * Muestra el formulario para capturar los datos y el listado de registros previos.
     */
    public function showForm($id = null)
    {
        $forms = Fm1Form::orderBy('created_at', 'desc')->get();
        $selectedForm = $id ?Fm1Form::findOrFail($id) : null;

        return view('pdf.fill-form', compact('forms', 'selectedForm'));
    }

    /**
     * Procesa los datos, los guarda en BD y genera el PDF resultante.
     */
    public function fill(Request $request)
    {
        $validated = $this->validateForm($request);

        // Guardar en la base de datos
        $formRecord = Fm1Form::create($validated);

        return $this->generatePdfResponse($formRecord);
    }

    /**
     * Actualiza un registro existente y genera el PDF.
     */
    public function update(Request $request, $id)
    {
        $formRecord = Fm1Form::findOrFail($id);
        $validated = $this->validateForm($request);

        $formRecord->update($validated);

        return $this->generatePdfResponse($formRecord);
    }

    /**
     * Elimina un registro.
     */
    public function destroy($id)
    {
        $formRecord = Fm1Form::findOrFail($id);
        $formRecord->delete();

        return redirect()->route('pdf.fill')->with('success', 'Registro eliminado correctamente.');
    }

    /**
     * Valida los datos comunes del formulario.
     */
    protected function validateForm(Request $request)
    {
        return $request->validate([
            'nombre' => 'required|string|max:255',
            'num_empleado' => 'nullable|string|max:50',
            'rfc' => 'nullable|string|max:13',
            'curp' => 'nullable|string|max:18',
            'sexo' => 'nullable|string|max:20',
            'escolaridad' => 'nullable|string|max:255',
            'cedula' => 'nullable|string|max:50',
            'domicilio' => 'nullable|string',
            'hijos' => 'nullable|string|max:100',
            'nacionalidad' => 'nullable|string|max:100',
            'cod_tipo_movimiento' => 'nullable|string|max:255',
            'tipo_mov' => 'nullable|string|max:255',
            'fecha_movimiento' => 'nullable|date',
            'fecha_final' => 'nullable|date',

            // Datos de la Plaza
            'codigo_puesto' => 'nullable|string|max:100',
            'nivel_subnivel' => 'nullable|string|max:100',
            'denominacion_puesto' => 'nullable|string|max:255',
            'numero_plaza' => 'nullable|string|max:100',
            'tipo_plaza' => 'nullable|string|max:100',
            'ocupacion' => 'nullable|string|max:100',
            'estatus_plaza' => 'nullable|string|max:100',
            'unidad_administrativa' => 'nullable|string|max:100',
            'unidad_administrativa_denominacion' => 'nullable|string|max:255',
            'adscripcion' => 'nullable|string|max:100',
            'adscripcion_denominacion' => 'nullable|string|max:255',
            'adscripcion_fisica' => 'nullable|string|max:100',
            'adscripcion_fisica_denominacion' => 'nullable|string|max:255',
            'servicio' => 'nullable|string|max:100',
            'servicio_denominacion' => 'nullable|string|max:255',
            'codigo_turno' => 'nullable|string|max:50',
            'codigo_turno_descripcion' => 'nullable|string|max:100',
            'jornada' => 'nullable|string|max:100',
            'horario_codigo' => 'nullable|string|max:50',
            'horario_entrada1' => 'nullable|string|max:10',
            'horario_salida1' => 'nullable|string|max:10',
            'horario_entrada2' => 'nullable|string|max:10',
            'horario_salida2' => 'nullable|string|max:10',
            'observaciones' => 'nullable|string',
            'turno_opcional' => 'nullable|string|max:10',
            'percepcion_adicional' => 'nullable|string|max:10',
            'riesgos_profesionales' => 'nullable|string|max:10',
            'mando' => 'nullable|string|max:10',
            'enlace_alta_responsabilidad' => 'nullable|string|max:10',
            'enlace' => 'nullable|string|max:10',
            'operativo' => 'nullable|string|max:10',
            'rama_medica' => 'nullable|string|max:10',

            // Antecedentes de Ocupacion de la Plaza
            'nombre_ant' => 'nullable|string|max:255',
            'num_empleado_ant' => 'nullable|string|max:50',
            'cod_movi_ant' => 'nullable|string|max:50',
            'tipo_mov_ant' => 'nullable|string|max:255',
            'fecha_inicio_ant' => 'nullable|date',
            'fecha_fin_ant' => 'nullable|date',
            'turno_opcional_ant' => 'nullable|string|max:10',
            'percepcion_adicional_ant' => 'nullable|string|max:10',
            'riesgos_prof_ant' => 'nullable|string|max:10',
            'nombre_trab_ant' => 'nullable|string|max:255',

            // Firmas / Cargos
            'titular_area' => 'nullable|string|max:255',
            'responsable_admvo' => 'nullable|string|max:255',
            'titular_centro' => 'nullable|string|max:255',

            'cargo_titular_area' => 'nullable|string|max:255',
            'cargo_responsable_admvo' => 'nullable|string|max:255',
            'cargo_titular_centro' => 'nullable|string|max:255',
        ]);
    }

    /**
     * Lógica compartida para generar y retornar el PDF.
     */
    protected function generatePdfResponse(Fm1Form $formRecord)
    {
        $pdf = new Fpdi();
        $sourceFile = public_path('fm1_v1_4.pdf');

        if (!file_exists($sourceFile)) {
            return back()->with('error', 'El archivo base fm1_v1_4.pdf no se encuentra en la carpeta public.');
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
                // Mapeo de campos (Coordenadas ajustadas por el USUARIO)
                $pdf->SetXY(29, 34.5);
                $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->nombre)));
                $pdf->SetXY(112, 28);
                $pdf->Write(0, $formRecord->num_empleado ?? '');
                $pdf->SetXY(20, 39);
                $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->rfc ?? '')));
                $pdf->SetXY(60, 39);
                $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->curp ?? '')));
                $pdf->SetXY(109, 39);
                $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->sexo ?? '')));
                $pdf->SetXY(32, 49);
                $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->escolaridad ?? '')));
                $pdf->SetXY(95, 51);
                $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->cedula ?? '')));
                $pdf->SetXY(32, 53.0);
                $pdf->MultiCell(104, 4, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->domicilio ?? '')), 0, 'L');

                if ($formRecord->hijos) {
                //$pdf->SetXY(40, 63); // Ajustar según posición en PDF
                //$pdf->Write(0, mb_strtoupper($formRecord->hijos));
                }

                $pdf->SetXY(80, 43);
                $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->nacionalidad ?? '')));

                // Movimiento
                if ($formRecord->cod_tipo_movimiento) {
                    $pdf->SetXY(195, 39);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->cod_tipo_movimiento)));
                }

                if ($formRecord->tipo_mov) {
                    $pdf->SetXY(139, 39);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->tipo_mov)));
                }

                if ($formRecord->fecha_movimiento) {
                    $pdf->SetXY(139, 59);
                    $pdf->Write(0, $formRecord->fecha_movimiento->format('d'));
                    $pdf->SetXY(150, 59);
                    $pdf->Write(0, $formRecord->fecha_movimiento->format('m'));
                    $pdf->SetXY(160, 59);
                    $pdf->Write(0, $formRecord->fecha_movimiento->format('Y'));
                }

                if ($formRecord->fecha_final) {
                    $pdf->SetXY(175, 59);
                    $pdf->Write(0, $formRecord->fecha_final->format('d'));
                    $pdf->SetXY(186, 59);
                    $pdf->Write(0, $formRecord->fecha_final->format('m'));
                    $pdf->SetXY(196, 59);
                    $pdf->Write(0, $formRecord->fecha_final->format('Y'));
                }

                // Datos de la Plaza (Placeholder Coords: Y=150+)
                // El usuario debe definirlas
                if ($formRecord->codigo_puesto) {
                    $pdf->SetXY(51, 72);
                    $textoPuesto = $formRecord->codigo_puesto . " / " . ($formRecord->nivel_subnivel ?? '');
                    $pdf->Write(0, $textoPuesto);
                }
                if ($formRecord->denominacion_puesto) {
                    $pdf->SetFontSize(7); // Tamaño más pequeño
                    $pdf->SetXY(112, 72);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->denominacion_puesto)));
                    $pdf->SetFontSize(9); // Restaurar tamaño original
                }

                // Siguiente fila (Fila 78 - Estimada)
                if ($formRecord->numero_plaza) {
                    $pdf->SetXY(30, 77);
                    $pdf->Write(0, $formRecord->numero_plaza);
                }
                if ($formRecord->tipo_plaza) {
                    $pdf->SetXY(182, 77);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->tipo_plaza)));
                }

                // Fila 84 - Ocupación y Estatus
                if ($formRecord->ocupacion) {
                    $pdf->SetXY(30, 82);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->ocupacion)));
                }
                if ($formRecord->estatus_plaza) {
                    $pdf->SetXY(75, 82);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->estatus_plaza)));
                }

                // --- UBICACIÓN ADMINISTRATIVA (Filas 90, 96, 102, 108 estimada) ---
                // Unidad Administrativa
                if ($formRecord->unidad_administrativa) {
                    $pdf->SetXY(50, 91);
                    $pdf->Write(0, $formRecord->unidad_administrativa);
                }
                if ($formRecord->unidad_administrativa_denominacion) {
                    $pdf->SetXY(80, 91);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->unidad_administrativa_denominacion)));
                }

                // Adscripción
                if ($formRecord->adscripcion) {
                    $pdf->SetXY(50, 96);
                    $pdf->Write(0, $formRecord->adscripcion);
                }
                if ($formRecord->adscripcion_denominacion) {
                    $pdf->SetXY(80, 96);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->adscripcion_denominacion)));
                }

                // Adscripción Física
                if ($formRecord->adscripcion_fisica) {
                    $pdf->SetXY(50, 101);
                    $pdf->Write(0, $formRecord->adscripcion_fisica);
                }
                if ($formRecord->adscripcion_fisica_denominacion) {
                    $pdf->SetXY(80, 100);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->adscripcion_fisica_denominacion)));
                }

                // Servicio
                if ($formRecord->servicio) {
                    $pdf->SetXY(50, 105);
                    $pdf->Write(0, $formRecord->servicio);
                }
                if ($formRecord->servicio_denominacion) {
                    $pdf->SetXY(80, 105);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->servicio_denominacion)));
                }

                // --- TURNO Y JORNADA (Filas 110, 115 estimada) ---
                if ($formRecord->codigo_turno) {
                    $pdf->SetXY(22, 113);
                    $pdf->Write(0, $formRecord->codigo_turno);
                }
                if ($formRecord->codigo_turno_descripcion) {
                    $pdf->SetXY(30, 113);
                    $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->codigo_turno_descripcion)));
                }

                if ($formRecord->jornada) {
                    $pdf->SetXY(103, 113);
                    $pdf->Write(0, mb_strtoupper($formRecord->jornada));
                }

                // --- HORARIOS (Fila 123 estimada) ---
                // Se asume que van en una línea: Codigo, E1, S1, E2, S2
                if ($formRecord->horario_codigo) {
                    $pdf->SetXY(132, 113);
                    $pdf->Write(0, $formRecord->horario_codigo);
                }
                if ($formRecord->horario_entrada1) {
                    $pdf->SetXY(148, 113);
                    $pdf->Write(0, $formRecord->horario_entrada1);
                }
                if ($formRecord->horario_salida1) {
                    $pdf->SetXY(164, 113);
                    $pdf->Write(0, $formRecord->horario_salida1);
                }
                if ($formRecord->horario_entrada2) {
                    $pdf->SetXY(178, 113);
                    $pdf->Write(0, $formRecord->horario_entrada2);
                }
                if ($formRecord->horario_salida2) {
                    $pdf->SetXY(194, 113);
                    $pdf->Write(0, $formRecord->horario_salida2);
                }

                // Observaciones (Si es multilínea, habría que usar MultiCell, pero por ahora Write)
                if ($formRecord->observaciones) {
                    $pdf->SetXY(30, 130);
                    $pdf->SetFontSize(7);
                    $pdf->Write(4, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->observaciones)));
                    $pdf->SetFontSize(9);
                }

                // --- INDICADORES (X o ---- según captura) ---
                $indicators = [
                    'turno_opcional' => [108, 131],
                    'percepcion_adicional' => [108, 135],
                    'riesgos_profesionales' => [108, 139],
                    'mando' => [68, 131],
                    'enlace_alta_responsabilidad' => [68, 135],
                    'enlace' => [68, 139],
                    'operativo' => [68, 143],
                    'rama_medica' => [68, 147],
                ];

                foreach ($indicators as $field => $coords) {
                    $val = $formRecord->$field;
                    if (!empty($val)) {
                        $isPositive = in_array(strtoupper(trim($val)), ['S', '1', 'SI', 'X']);
                        $text = $isPositive ? 'X' : '---';
                        $pdf->SetXY($coords[0], $coords[1]);
                        $pdf->Write(0, $text);
                    }
                }

                // --- ANTECEDENTES DE OCUPACION DE LA PLAZA (Requieren coordenadas reales del usuario) ---
                $pdf->SetXY(30, 162); // Ajustar Y según PDF
                $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->nombre_ant ?? '')));
                $pdf->SetXY(45, 167);
                $pdf->Write(0, $formRecord->num_empleado_ant ?? '');

                $pdf->SetXY(42, 172);
                $pdf->Write(0, $formRecord->cod_movi_ant ?? '');
                $pdf->SetXY(85, 172);
                $pdf->Write(0, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->tipo_mov_ant ?? '')));
                if ($formRecord->fecha_inicio_ant) {
                    $pdf->SetXY(139, 176); // Ajustar Y según PDF
                    $pdf->Write(0, $formRecord->fecha_inicio_ant->format('d'));
                    $pdf->SetXY(150, 176);
                    $pdf->Write(0, $formRecord->fecha_inicio_ant->format('m'));
                    $pdf->SetXY(160, 176);
                    $pdf->Write(0, $formRecord->fecha_inicio_ant->format('Y'));
                }
                if ($formRecord->fecha_fin_ant) {
                    $pdf->SetXY(175, 176);
                    $pdf->Write(0, $formRecord->fecha_fin_ant->format('d'));
                    $pdf->SetXY(186, 176);
                    $pdf->Write(0, $formRecord->fecha_fin_ant->format('m'));
                    $pdf->SetXY(196, 176);
                    $pdf->Write(0, $formRecord->fecha_fin_ant->format('Y'));
                }

                $pdf->SetXY(45, 218);
                $pdf->MultiCell(120, 4, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->nombre_trab_ant ?? '')), 0, 'C');

                // Indicadores Antecedentes (X o ---)
                $antIndicators = [
                    'turno_opcional_ant' => [90, 182],
                    'percepcion_adicional_ant' => [148, 182],
                    'riesgos_prof_ant' => [200, 182],
                ];

                foreach ($antIndicators as $field => $coords) {
                    $val = $formRecord->$field;
                    if (!empty($val)) {
                        $text = in_array(strtoupper(trim($val)), ['S', '1', 'SI', 'X']) ? 'X' : '---';
                        $pdf->SetXY($coords[0], $coords[1]);
                        $pdf->Write(0, $text);
                    }
                }

                // --- FIRMAS / CARGOS (MultiCell para manejar textos largos y centrado) ---
                // Desactivar salto automático para evitar páginas extra al final
                $pdf->SetAutoPageBreak(false);
                $width = 65; // Ancho ampliado para más caracteres por línea (aprox. 38)

                // 1. Titular del Área
                $pdf->SetFontSize(9);
                $pdf->SetXY(7.5, 252.5);
                $pdf->MultiCell($width, 4, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->titular_area ?? '')), 0, 'C');
                $pdf->SetX(7.5);
                $pdf->SetFontSize(6.5); // Estandarizado para todos
                $pdf->MultiCell($width, 3, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->cargo_titular_area ?? '')), 0, 'C');

                // 2. Responsable Admvo.
                $pdf->SetFontSize(9);
                $pdf->SetXY(75, 252.5);
                $pdf->MultiCell($width, 4, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->responsable_admvo ?? '')), 0, 'C');
                $pdf->SetX(75);
                $pdf->SetFontSize(6.5); // Estandarizado para todos
                $pdf->MultiCell($width, 3, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->cargo_responsable_admvo ?? '')), 0, 'C');

                // 3. Titular del Centro
                $pdf->SetFontSize(9);
                $pdf->SetXY(142.5, 252.5);
                $pdf->MultiCell($width, 4, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->titular_centro ?? '')), 0, 'C');
                $pdf->SetX(142.5);
                $pdf->SetFontSize(6.5); // Estandarizado para todos
                $pdf->MultiCell($width, 3, iconv('UTF-8', 'windows-1252', mb_strtoupper($formRecord->cargo_titular_centro ?? '')), 0, 'C');

                $pdf->SetFontSize(9); // Restaurar
                $pdf->SetAutoPageBreak(true, 20); // Restaurar con margen de 2cm
            }
        }

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="fm1_llenado_' . time() . '.pdf"');
    }
}