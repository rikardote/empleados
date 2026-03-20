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
        $selectedForm = $id ? Fm1Form::findOrFail($id) : null;
        
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
            'nombre'           => 'required|string|max:255',
            'num_empleado'     => 'nullable|string|max:50',
            'rfc'              => 'nullable|string|max:13',
            'curp'             => 'nullable|string|max:18',
            'sexo'             => 'nullable|string|max:20',
            'escolaridad'      => 'nullable|string|max:255',
            'cedula'           => 'nullable|string|max:50',
            'domicilio'        => 'nullable|string',
            'hijos'            => 'nullable|string|max:100',
            'nacionalidad'     => 'nullable|string|max:100',
            'tipo_movimiento'  => 'nullable|string|max:255',
            'fecha_movimiento' => 'nullable|date',
            'fecha_final'      => 'nullable|date',
            
            // Datos de la Plaza
            'codigo_puesto'             => 'nullable|string|max:100',
            'nivel_subnivel'            => 'nullable|string|max:100',
            'denominacion_puesto'       => 'nullable|string|max:255',
            'numero_plaza'              => 'nullable|string|max:100',
            'tipo_plaza'                => 'nullable|string|max:100',
            'ocupacion'                 => 'nullable|string|max:100',
            'estatus_plaza'             => 'nullable|string|max:100',
            'unidad_administrativa'     => 'nullable|string|max:100',
            'unidad_administrativa_denominacion' => 'nullable|string|max:255',
            'adscripcion'               => 'nullable|string|max:100',
            'adscripcion_denominacion'   => 'nullable|string|max:255',
            'adscripcion_fisica'        => 'nullable|string|max:100',
            'adscripcion_fisica_denominacion' => 'nullable|string|max:255',
            'servicio'                  => 'nullable|string|max:100',
            'servicio_denominacion'     => 'nullable|string|max:255',
            'codigo_turno'              => 'nullable|string|max:50',
            'codigo_turno_descripcion'  => 'nullable|string|max:100',
            'jornada'                   => 'nullable|string|max:100',
            'horario_codigo'            => 'nullable|string|max:50',
            'horario_entrada1'          => 'nullable|string|max:10',
            'horario_salida1'           => 'nullable|string|max:10',
            'horario_entrada2'          => 'nullable|string|max:10',
            'horario_salida2'           => 'nullable|string|max:10',
            'observaciones'             => 'nullable|string',
            'turno_opcional'            => 'nullable|string|max:10',
            'percepcion_adicional'      => 'nullable|string|max:10',
            'riesgos_profesionales'     => 'nullable|string|max:10',
            'mando'                     => 'nullable|string|max:10',
            'enlace_alta_responsabilidad' => 'nullable|string|max:10',
            'enlace'                    => 'nullable|string|max:10',
            'operativo'                 => 'nullable|string|max:10',
            'rama_medica'               => 'nullable|string|max:10',
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
                $pdf->SetXY(29, 34.5);  $pdf->Write(0, mb_strtoupper($formRecord->nombre));
                $pdf->SetXY(112, 28); $pdf->Write(0, $formRecord->num_empleado ?? '');
                $pdf->SetXY(20, 39);  $pdf->Write(0, mb_strtoupper($formRecord->rfc ?? ''));
                $pdf->SetXY(60, 39); $pdf->Write(0, mb_strtoupper($formRecord->curp ?? ''));
                $pdf->SetXY(109, 39); $pdf->Write(0, mb_strtoupper($formRecord->sexo ?? ''));
                $pdf->SetXY(32, 49);  $pdf->Write(0, mb_strtoupper($formRecord->escolaridad ?? ''));
                $pdf->SetXY(95, 51); $pdf->Write(0, mb_strtoupper($formRecord->cedula ?? ''));
                $pdf->SetXY(32, 57);  $pdf->Write(0, mb_strtoupper($formRecord->domicilio ?? ''));
                $pdf->SetXY(40, 90);  $pdf->Write(0, mb_strtoupper($formRecord->hijos ?? ''));
                $pdf->SetXY(80, 43); $pdf->Write(0, mb_strtoupper($formRecord->nacionalidad ?? ''));

                // Movimiento
                if ($formRecord->tipo_movimiento) {
                    $pdf->SetXY(195, 39); 
                    $pdf->Write(0, mb_strtoupper($formRecord->tipo_movimiento));
                }

                if ($formRecord->fecha_movimiento) {
                    $pdf->SetXY(139, 59); $pdf->Write(0, $formRecord->fecha_movimiento->format('d'));
                    $pdf->SetXY(150, 59); $pdf->Write(0, $formRecord->fecha_movimiento->format('m'));
                    $pdf->SetXY(160, 59); $pdf->Write(0, $formRecord->fecha_movimiento->format('Y'));
                }

                if ($formRecord->fecha_final) {
                    $pdf->SetXY(175, 59); $pdf->Write(0, $formRecord->fecha_final->format('d'));
                    $pdf->SetXY(186, 59); $pdf->Write(0, $formRecord->fecha_final->format('m'));
                    $pdf->SetXY(196, 59); $pdf->Write(0, $formRecord->fecha_final->format('Y'));
                }

                // Datos de la Plaza (Placeholder Coords: Y=150+)
                // El usuario debe definirlas
                $y = 150;
                if ($formRecord->codigo_puesto) { $pdf->SetXY(20, $y); $pdf->Write(0, $formRecord->codigo_puesto); }
                if ($formRecord->denominacion_puesto) { $pdf->SetXY(60, $y); $pdf->Write(0, $formRecord->denominacion_puesto); }
            }
        }

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="fm1_llenado_'.time().'.pdf"');
    }
}
