<?php

namespace App\Http\Controllers;

use App\Imports\Fm1Import;
use App\Models\Fm1Form;
use App\Models\Fm1ImportBatch;
use App\Services\Fm1PdfService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use setasign\Fpdi\Fpdi;

class Fm1ImportController extends Controller
{
    protected Fm1PdfService $pdfService;

    public function __construct(Fm1PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Formulario de importación + listado de lotes.
     */
    public function index()
    {
        $batches = Fm1ImportBatch::withCount('forms')
            ->orderByDesc('created_at')
            ->get();

        return view('fm1.import', compact('batches'));
    }

    /**
     * Descarga una plantilla Excel con las columnas esperadas.
     */
    public function downloadTemplate()
    {
        $headers = [
            'nombre','num_empleado','rfc','curp','sexo','escolaridad','cedula',
            'domicilio','hijos','nacionalidad','cod_tipo_movimiento','tipo_mov',
            'fecha_movimiento','fecha_final',
            'codigo_puesto','nivel_subnivel','denominacion_puesto','numero_plaza',
            'tipo_plaza','ocupacion','estatus_plaza',
            'unidad_administrativa','unidad_administrativa_denominacion',
            'adscripcion','adscripcion_denominacion',
            'adscripcion_fisica','adscripcion_fisica_denominacion',
            'servicio','servicio_denominacion',
            'codigo_turno','codigo_turno_descripcion','jornada',
            'horario_codigo','horario_entrada1','horario_salida1',
            'horario_entrada2','horario_salida2','observaciones',
            'turno_opcional','percepcion_adicional','riesgos_profesionales',
            'mando','enlace_alta_responsabilidad','enlace','operativo','rama_medica',
            'nombre_ant','num_empleado_ant','cod_movi_ant','tipo_mov_ant',
            'fecha_inicio_ant','fecha_fin_ant',
            'turno_opcional_ant','percepcion_adicional_ant','riesgos_prof_ant',
            'nombre_trab_ant',
            'titular_area','cargo_titular_area',
            'responsable_admvo','cargo_responsable_admvo',
            'titular_centro','cargo_titular_centro',
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8 compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, $headers);
            // Example row
            fputcsv($file, array_fill(0, count($headers), ''));
            fclose($file);
        };

        return response()->streamDownload($callback, 'plantilla_fm1.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Procesa el archivo Excel subido.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file'  => 'required|mimes:xlsx,xls,csv',
            'notes' => 'nullable|string|max:500',
        ]);

        ini_set('memory_limit', '512M');

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $storedName   = now()->format('YmdHis') . '_' . $originalName;

        // Guardar archivo
        $file->storeAs('fm1_imports', $storedName, 'public');

        // Crear lote primero para obtener el ID
        $batch = Fm1ImportBatch::create([
            'original_filename' => $originalName,
            'stored_filename'   => $storedName,
            'record_count'      => 0,
            'status'            => 'processing',
            'notes'             => $request->input('notes'),
        ]);

        try {
            $importer = new Fm1Import($batch->id);
            Excel::import($importer, $file);

            $count = $batch->forms()->count();
            $batch->update([
                'record_count' => $count,
                'status'       => 'completed',
            ]);
        } catch (\Throwable $e) {
            $batch->update(['status' => 'failed']);
            return back()->with('error', 'Error al importar: ' . $e->getMessage());
        }

        return redirect()
            ->route('fm1.import.index')
            ->with('success', "Importación completada. {$batch->record_count} registros cargados.");
    }

    /**
     * Genera el PDF de UN registro FM1.
     */
    public function downloadOne(int $id)
    {
        $form = Fm1Form::findOrFail($id);

        try {
            $pdfContent = $this->pdfService->generate($form);
            $filename   = 'FM1_' . ($form->num_empleado ?? $form->id) . '_' . now()->format('Ymd') . '.pdf';

            return response($pdfContent, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }

    /**
     * Genera un ZIP con los PDFs de todos los registros de un lote.
     */
    public function downloadBatch(int $batchId)
    {
        $batch = Fm1ImportBatch::with('forms')->findOrFail($batchId);
        $forms = $batch->forms;

        if ($forms->isEmpty()) {
            return back()->with('error', 'Este lote no tiene registros.');
        }

        $zipName = 'FM1_Lote_' . $batch->id . '_' . now()->format('Ymd') . '.zip';
        $zipPath = storage_path('app/tmp/' . $zipName);

        // Ensure tmp dir exists
        if (!is_dir(storage_path('app/tmp'))) {
            mkdir(storage_path('app/tmp'), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'No se pudo crear el archivo ZIP.');
        }

        $errors = 0;
        foreach ($forms as $form) {
            try {
                $pdfContent = $this->pdfService->generate($form);
                $pdfName    = 'FM1_' . ($form->num_empleado ?? $form->id) . '_' . $form->nombre . '.pdf';
                // Sanitize filename
                $pdfName    = preg_replace('/[^A-Za-záéíóúÁÉÍÓÚüÜñÑ0-9_\-.]/', '_', $pdfName);
                $zip->addFromString($pdfName, $pdfContent);
            } catch (\Throwable) {
                $errors++;
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipName, [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Elimina un lote y todos sus registros FM1 asociados.
     */
    public function destroyBatch(int $batchId)
    {
        $batch = Fm1ImportBatch::findOrFail($batchId);

        // Delete associated file
        $filePath = storage_path('app/public/fm1_imports/' . $batch->stored_filename);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // fm1_forms with this batch_id will be nulled by DB constraint, we delete them
        Fm1Form::where('import_batch_id', $batchId)->delete();
        $batch->delete();

        return redirect()
            ->route('fm1.import.index')
            ->with('success', 'Lote eliminado correctamente.');
    }

    /**
     * Muestra los registros de un lote específico.
     */
    public function showBatch(int $batchId)
    {
        $batch = Fm1ImportBatch::findOrFail($batchId);
        $forms = Fm1Form::where('import_batch_id', $batchId)
            ->orderBy('id')
            ->paginate(50);

        return view('fm1.batch-detail', compact('batch', 'forms'));
    }
}
