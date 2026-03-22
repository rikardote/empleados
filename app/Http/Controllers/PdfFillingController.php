<?php

namespace App\Http\Controllers;

use App\Models\Fm1Form;
use App\Services\Fm1PdfService;
use Illuminate\Http\Request;

class PdfFillingController extends Controller
{
    protected $pdfService;

    public function __construct(Fm1PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

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
     * Formulario visual: inputs superpuestos directamente sobre el PDF.
     */
    public function showVisualForm($id = null)
    {
        $selectedForm = $id ? Fm1Form::findOrFail($id) : null;
        return view('pdf.fill-visual', compact('selectedForm'));
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
        try {
            $pdfContent = $this->pdfService->generate($formRecord);

            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="fm1_llenado_' . time() . '.pdf"');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el PDF: ' . $e->getMessage());
        }
    }
}