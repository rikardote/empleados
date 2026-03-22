<?php

namespace App\Http\Controllers;

use App\Models\Fm1Form;
use App\Services\Fm1PdfService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PdfInspectorController extends Controller
{
    private function servicePath(): string
    {
        return app_path('Services/Fm1PdfService.php');
    }

    /**
     * Página principal – pasa la lista de formularios guardados para elegir
     * con qué datos reales previsualizar.
     */
    public function index()
    {
        $forms = Fm1Form::orderByDesc('created_at')
            ->select('id', 'nombre', 'num_empleado', 'tipo_mov', 'created_at')
            ->limit(50)
            ->get();

        return view('pdf.inspector', compact('forms'));
    }

    /**
     * Genera el PDF real con datos de un formulario y lo devuelve al navegador.
     * El inspector lo carga directamente en pdf.js → lo que ves = lo que FPDI produce.
     */
    public function previewPdf(Request $request)
    {
        $formId = $request->query('form_id');
        if (!$formId) {
            return response()->json(['error' => 'form_id requerido'], 400);
        }

        $form = Fm1Form::findOrFail($formId);

        try {
            $pdfContent = (new Fm1PdfService())->generate($form);

            return response($pdfContent, 200, [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => 'inline; filename="preview.pdf"',
                'Cache-Control'       => 'no-store',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Lee Fm1PdfService.php y extrae todas las llamadas writeText con sus coords.
     * Si se pasa ?form_id=N, también resuelve el valor real de cada campo.
     */
    public function getCoordinates(Request $request): JsonResponse
    {
        try {
            $source = file_get_contents($this->servicePath());
            $lines  = explode("\n", $source);
            $points = [];
            $id     = 0;

            // Cargar datos reales del formulario si se solicita
            $form = null;
            if ($request->filled('form_id')) {
                $form = Fm1Form::find($request->form_id);
            }

            $pattern = '/\$this->writeText\(\s*\$pdf\s*,\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([^,\)]+)/';

            foreach ($lines as $lineNo => $line) {
                if (preg_match($pattern, $line, $m)) {
                    $x   = (float) $m[1];
                    $y   = (float) $m[2];
                    $raw = trim($m[3]);

                    // Extraer nombre del campo
                    $field = $raw;
                    if (preg_match('/\$form->(\w+)/', $raw, $fm)) {
                        $field = $fm[1];
                    }

                    // Comentario inline
                    $comment = '';
                    if (preg_match('/\/\/\s*(.+)$/', $line, $cm)) {
                        $comment = trim($cm[1]);
                    }

                    // Valor real del campo si hay formulario cargado
                    $value = null;
                    if ($form && isset($form->$field)) {
                        $val = $form->$field;
                        if ($val instanceof \Illuminate\Support\Carbon || $val instanceof \Carbon\Carbon) {
                            $val = $val->format('d/m/Y');
                        }
                        $value = (string) $val;
                    }

                    $points[] = [
                        'id'    => ++$id,
                        'label' => $comment ?: $field,
                        'field' => $field,
                        'value' => $value,          // null si no hay form seleccionado
                        'x'     => $x,
                        'y'     => $y,
                        'line'  => $lineNo + 1,
                        'raw'   => trim($line),
                    ];
                }
            }

            return response()->json($points);

        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Guarda coordenadas actualizadas en Fm1PdfService.php.
     * Body JSON: [ { line: N, x: float, y: float }, ... ]
     */
    public function saveCoordinates(Request $request): JsonResponse
    {
        try {
            $payload = $request->json()->all();

            if (empty($payload) || !is_array($payload)) {
                return response()->json(['success' => false, 'message' => 'Payload vacío.'], 400);
            }

            foreach ($payload as $i => $item) {
                if (!isset($item['line']) || !is_numeric($item['line']))
                    return response()->json(['success' => false, 'message' => "Item {$i}: falta 'line'."], 422);
                if (!isset($item['x']) || !is_numeric($item['x']))
                    return response()->json(['success' => false, 'message' => "Item {$i}: falta 'x'."], 422);
                if (!isset($item['y']) || !is_numeric($item['y']))
                    return response()->json(['success' => false, 'message' => "Item {$i}: falta 'y'."], 422);
            }

            $path = $this->servicePath();

            if (!file_exists($path))
                return response()->json(['success' => false, 'message' => 'Fm1PdfService.php no encontrado.'], 500);

            if (!is_writable($path))
                return response()->json(['success' => false,
                    'message' => 'Sin permisos de escritura. Ejecuta: chmod 666 ' . $path], 500);

            $source  = file_get_contents($path);
            $lines   = explode("\n", $source);
            $changed = 0;

            foreach ($payload as $update) {
                $idx = (int) $update['line'] - 1;
                if (!isset($lines[$idx])) continue;

                $original = $lines[$idx];
                $newLine  = preg_replace_callback(
                    '/(\$this->writeText\(\s*\$pdf\s*,\s*)([\d.]+)(\s*,\s*)([\d.]+)/',
                    function ($m) use ($update) {
                        return $m[1]
                            . number_format((float) $update['x'], 1, '.', '')
                            . $m[3]
                            . number_format((float) $update['y'], 1, '.', '');
                    },
                    $original,
                    1
                );

                if ($newLine !== null && $newLine !== $original) {
                    $lines[$idx] = $newLine;
                    $changed++;
                }
            }

            if ($changed > 0) {
                $backupDir = storage_path('app/pdf-backups');
                if (!is_dir($backupDir)) mkdir($backupDir, 0755, true);
                copy($path, $backupDir . '/Fm1PdfService.php.bak.' . now()->format('YmdHis'));
                file_put_contents($path, implode("\n", $lines));
            }

            return response()->json([
                'success' => true,
                'changed' => $changed,
                'message' => $changed > 0
                    ? "✅ {$changed} coordenada(s) actualizadas. Backup guardado."
                    : 'Sin cambios detectados.',
            ]);

        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
