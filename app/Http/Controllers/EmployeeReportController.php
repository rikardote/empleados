<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

use App\Exports\EmployeeConceptExport;
use App\Imports\EmployeesImport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeReportController extends Controller
{
    public function conceptReport(Request $request)
    {
        $concepts = [
            'c_sindic_local' => 'SNTISSSTE',
            'sindicato_tres' => 'SNADETISSSTE',
            'sindicato_cuatro' => 'SINADTEISSSTE',
            'concepto_nuevo_02' => 'SUTISSSTE',
            'concepto_nuevo_03' => 'SINAPTEISSSTE',
        ];

        $periods = $this->getSortedPeriods();

        $selectedConcept = $request->input('concept');
        $selectedPeriod = $request->input('period');

        $previousCount = 0;
        $previousEmployeeIds = [];
        $joinsCount = 0;
        $leavesCount = 0;
        $employees = collect();
        $leavesDetails = [];

        if ($selectedConcept && $selectedPeriod) {
            $employees = Employee::where('periodo', $selectedPeriod)
                ->where(function ($query) use ($selectedConcept) {
                    $query->whereNotNull("nomina_data->$selectedConcept")
                          ->where("nomina_data->$selectedConcept", '!=', 0);
                })
                ->get()
                ->unique('id_empleado');

            $currentEmployeeIds = $employees->pluck('id_empleado')->toArray();

            // Find previous period for comparison
            $currentIndex = $periods->search($selectedPeriod);
            $previousPeriodKey = $periods->get($currentIndex + 1);
            
            if ($previousPeriodKey) {
                $previousEmployeesQuery = Employee::where('periodo', $previousPeriodKey)
                    ->where(function ($query) use ($selectedConcept) {
                        $query->whereNotNull("nomina_data->$selectedConcept")
                              ->where("nomina_data->$selectedConcept", '!=', 0);
                    });
                
                $previousEmployeeIds = $previousEmployeesQuery->pluck('id_empleado')->unique()->toArray();
                $previousCount = count($previousEmployeeIds);

                // Calculate gross variations
                $leavesIds = array_diff($previousEmployeeIds, $currentEmployeeIds);
                $joinsCount = count(array_diff($currentEmployeeIds, $previousEmployeeIds));
                $leavesCount = count($leavesIds);

                // Trace where leaves went
                $leavesDetails = [];
                if ($leavesCount > 0) {
                    $currentLeavesStatus = Employee::where('periodo', $selectedPeriod)
                        ->whereIn('id_empleado', $leavesIds)
                        ->get();
                    
                    $foundIds = $currentLeavesStatus->pluck('id_empleado')->toArray();

                    foreach ($currentLeavesStatus as $emp) {
                        $newUnion = 'Sin Sindicato';
                        foreach ($concepts as $id => $name) {
                            if (isset($emp->nomina_data[$id]) && $emp->nomina_data[$id] != 0) {
                                $newUnion = $name;
                                break;
                            }
                        }
                        $leavesDetails[] = [
                            'id_empleado' => $emp->id_empleado,
                            'nombre' => "{$emp->nombre} {$emp->apellido_1} {$emp->apellido_2}",
                            'nueva_union' => $newUnion,
                            'status' => 'cambio'
                        ];
                    }

                    // People who are not even in the current period (Actual Bajas from the company)
                    $missingIds = array_diff($leavesIds, $foundIds);
                    if (!empty($missingIds)) {
                        $prevEmployeesData = Employee::where('periodo', $previousPeriodKey)
                            ->whereIn('id_empleado', $missingIds)
                            ->get()
                            ->unique('id_empleado');
                            
                        foreach ($prevEmployeesData as $prevEmp) {
                            $leavesDetails[] = [
                                'id_empleado' => $prevEmp->id_empleado,
                                'nombre' => "{$prevEmp->nombre} {$prevEmp->apellido_1} {$prevEmp->apellido_2}",
                                'nueva_union' => 'Baja',
                                'status' => 'baja_total'
                            ];
                        }
                    }
                }
            }
        }

        return view('employees.concept-report', compact(
            'concepts', 'periods', 'selectedConcept', 'selectedPeriod', 
            'employees', 'previousCount', 'previousEmployeeIds', 'joinsCount', 'leavesCount', 'leavesDetails'
        ));
    }

    public function exportExcel(Request $request)
    {
        $selectedConcept = $request->input('concept');
        $selectedPeriod = $request->input('period');

        if (!$selectedConcept || !$selectedPeriod) {
            return back()->with('error', 'Debe seleccionar un concepto y un periodo para exportar.');
        }

        $concepts = [
            'c_sindic_local' => 'SNTISSSTE',
            'sindicato_tres' => 'SNADETISSSTE',
            'sindicato_cuatro' => 'SINADTEISSSTE',
            'concepto_nuevo_02' => 'SUTISSSTE',
            'concepto_nuevo_03' => 'SINAPTEISSSTE',
        ];

        $conceptName = $concepts[$selectedConcept] ?? $selectedConcept;
        $fileName = "Reporte_{$conceptName}_{$selectedPeriod}.xlsx";
        return Excel::download(new EmployeeConceptExport($selectedConcept, $selectedPeriod, $conceptName), $fileName);
    }

    public function comparePeriods(Request $request)
    {
        $periods = $this->getSortedPeriods();

        if ($periods->count() < 2) {
            return view('employees.compare-periods', [
                'error' => 'Se necesitan al menos dos quincenas para realizar la comparación.',
                'periods' => $periods,
                'latestPeriod' => $periods[0] ?? null,
                'previousPeriod' => null,
                'differences' => [],
                'plazaTypes' => [],
                'selectedPlazaType' => null
            ]);
        }

        $latestPeriod = $request->input('latest_period');
        $previousPeriod = $request->input('previous_period');
        $selectedPlazaType = $request->input('plaza_type');
        $differences = [];
        
        $plazaTypes = Employee::distinct()
            ->pluck('id_tipo_plaza')
            ->filter()
            ->values();

        if (!$latestPeriod || !$previousPeriod) {
            return view('employees.compare-periods', compact('latestPeriod', 'previousPeriod', 'differences', 'periods', 'plazaTypes', 'selectedPlazaType'));
        }
        $latestIdx = $periods->search($latestPeriod);
        $previousIdx = $periods->search($previousPeriod);

        if ($latestIdx > $previousIdx) {
            $temp = $latestPeriod;
            $latestPeriod = $previousPeriod;
            $previousPeriod = $temp;
        }

        $queryLatest = Employee::where('periodo', $latestPeriod);
        $queryPrevious = Employee::where('periodo', $previousPeriod);

        if ($selectedPlazaType) {
            $queryLatest->where('id_tipo_plaza', $selectedPlazaType);
            $queryPrevious->where('id_tipo_plaza', $selectedPlazaType);
        }

        $latestEmployeesRaw = $queryLatest->get();
        $previousEmployeesRaw = $queryPrevious->get();


        $exclude = [
            'id', 
            'created_at', 
            'updated_at', 
            'periodo', 
            'nomina_data',
            'fecha_ingreso_st',
            'fec_alta_empleado',
            'fec_imputacion',
            'fec_pago',
            'nombre',
            'apellido_1',
            'apellido_2',
            'id_banco',
            'num_cuenta',
            'id_forma_pago',
            'source_file',
        ];

        // Funcion para obtener la data relevante para comparar/deduplicar
        $getRelevantData = function($employee) use ($exclude) {
            $attr = $employee->getAttributes();
            foreach ($exclude as $field) {
                unset($attr[$field]);
            }
            // Trim all strings to avoid whitespace differences
            return array_map(function($value) {
                return is_string($value) ? trim($value) : $value;
            }, $attr);
        };

        // Deduplicar: si toda la info relevante es igual, solo dejar uno.
        $latestEmployees = $latestEmployeesRaw->unique(function ($item) use ($getRelevantData) {
            return serialize($getRelevantData($item));
        });

        $previousEmployees = $previousEmployeesRaw->unique(function ($item) use ($getRelevantData) {
            return serialize($getRelevantData($item));
        });

        $previousGrouped = $previousEmployees->groupBy('id_empleado');

        $differences = [];

        foreach ($latestEmployees as $employee) {
            $empId = $employee->id_empleado;
            $prevOptions = $previousGrouped->get($empId, collect());
            
            // Intentar buscar coincidencia por plaza si hay varias opciones
            $prevEmployee = $prevOptions->firstWhere('id_plaza_empleado', $employee->id_plaza_empleado);
            if (!$prevEmployee) {
                $prevEmployee = $prevOptions->first();
            }
            
            if (!$prevEmployee) {
                // Nuevo empleado o nueva plaza
                $differences[] = [
                    'id_empleado' => $employee->id_empleado,
                    'id_plaza_empleado' => $employee->id_plaza_empleado,
                    'nombre_completo' => "{$employee->nombre} {$employee->apellido_1} {$employee->apellido_2}",
                    'type' => 'new',
                    'changes' => []
                ];
                continue;
            }

            $changes = [];
            $attributes = $employee->getAttributes();
            
            foreach ($attributes as $key => $value) {
                if (in_array($key, $exclude)) continue;
                
                $prevValue = $prevEmployee->getAttribute($key);
                
                $v1 = is_string($value) ? trim($value) : $value;
                $v2 = is_string($prevValue) ? trim($prevValue) : $prevValue;

                if ($v1 != $v2 && !($v1 === null && $v2 === '') && !($v1 === '' && $v2 === null)) {
                    $changes[$key] = [
                        'old' => $prevValue,
                        'new' => $value
                    ];
                }
            }

            if (!empty($changes)) {
                // Ordenar cambios: plaza presupuestal primero, luego alfabéticamente
                uksort($changes, function($a, $b) {
                    $prio = ['id_plaza_empleado' => 1, 'id_plaza' => 2];
                    $prioA = $prio[$a] ?? 100;
                    $prioB = $prio[$b] ?? 100;
                    if ($prioA != $prioB) return $prioA - $prioB;
                    return strcmp($a, $b);
                });

                $differences[] = [
                    'id_empleado' => $employee->id_empleado,
                    'id_plaza_empleado' => $employee->id_plaza_empleado,
                    'nombre_completo' => "{$employee->nombre} {$employee->apellido_1} {$employee->apellido_2}",
                    'type' => 'modified',
                    'changes' => $changes
                ];
            }
            
            // Quitar de las opciones para detectar bajas despues
            // Nota: Esto es un poco complejo con duplicados, pero al haber deduplicado arriba, 
            // deberiamos tener una correspondencia mas limpia.
        }

        // Detectar los que estaban antes y ya no estan en absoluto
        $latestEmployeeIds = $latestEmployees->pluck('id_empleado')->unique()->toArray();
        foreach ($previousEmployees as $prevEmployee) {
            if (!in_array($prevEmployee->id_empleado, $latestEmployeeIds)) {
                // No esta en el nuevo periodo en absoluto
                $differences[] = [
                    'id_empleado' => $prevEmployee->id_empleado,
                    'id_plaza_empleado' => $prevEmployee->id_plaza_empleado,
                    'nombre_completo' => "{$prevEmployee->nombre} {$prevEmployee->apellido_1} {$prevEmployee->apellido_2}",
                    'type' => 'removed',
                    'changes' => []
                ];
            }
        }
        
        // Limpiar duplicados en $differences por id_empleado + tipo + cambios
        $differences = collect($differences)->unique(function($d) {
            return $d['id_empleado'] . $d['type'] . serialize($d['changes']);
        })->values()->all();
        return view('employees.compare-periods', compact('latestPeriod', 'previousPeriod', 'differences', 'periods', 'plazaTypes', 'selectedPlazaType'));
    }

    public function downloadImport($filename)
    {
        $path = storage_path('app/public/imports/' . $filename);
        if (!file_exists($path)) {
            abort(404);
        }
        return response()->download($path);
    }

    public function deleteImport($filename)
    {
        $path = storage_path('app/public/imports/' . $filename);
        if (file_exists($path)) {
            // Eliminar registros que tengan este archivo específico como origen
            Employee::where('source_file', $filename)->delete();

            unlink($path);
        }

        return redirect()->back()->with('success', 'Importación y registros eliminados correctamente.');
    }

    public function importForm()
    {
        $importFiles = [];
        if (file_exists(storage_path('app/public/imports'))) {
            $files = scandir(storage_path('app/public/imports'));
            $importFiles = collect($files)
                ->filter(function ($f) {
                    return $f !== '.' && $f !== '..';
                })
                ->map(function ($f) {
                    return [
                        'name' => $f,
                        'url' => route('employees.download-import', ['filename' => $f]),
                        'date' => filemtime(storage_path('app/public/imports/' . $f))
                    ];
                })
                ->sortByDesc('date')
                ->values()
                ->all();
        }

        return view('employees.import', compact('importFiles'));
    }

    public function import(Request $request)
    {
        if (!$request->hasFile('file') && $request->isMethod('POST')) {
            $max = ini_get('upload_max_filesize');
            return redirect()->back()->with('error', "No se recibió ningún archivo. Es posible que el archivo sea demasiado grande (límite: $max) o el formato sea inválido.");
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,bin',
        ]);

        ini_set('memory_limit', '1G');
        
        $file = $request->file('file');
        
        try {
            // Leer solo las primeras 2 filas para detectar el periodo automáticamente
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file->getRealPath());
            $reader->setReadDataOnly(true);
            $reader->setReadFilter(new class implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter {
                public function readCell($columnAddress, $row, $worksheetName = '') {
                    return $row <= 2;
                }
            });

            $spreadsheet = $reader->load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $headers = [];
            $firstDataRow = [];
            
            foreach ($worksheet->getRowIterator(1, 1) as $row) {
                foreach ($row->getCellIterator() as $cell) {
                    $val = $cell->getValue();
                    if ($val === null || $val === '') continue;
                    $headers[] = strtolower(trim($val));
                }
            }
            
            foreach ($worksheet->getRowIterator(2, 2) as $row) {
                foreach ($row->getCellIterator() as $cell) {
                    $firstDataRow[] = $cell->getValue();
                }
            }
            
            // Asegurarnos de que coincidan las longitudes para array_combine
            $dataCount = count($firstDataRow);
            $headerCount = count($headers);
            if ($dataCount > $headerCount) {
                $firstDataRow = array_slice($firstDataRow, 0, $headerCount);
            } elseif ($dataCount < $headerCount) {
                $headers = array_slice($headers, 0, $dataCount);
            }
            
            $data = array_combine($headers, $firstDataRow);
            
            if (!isset($data['fec_pago'])) {
                return redirect()->back()->with('error', 'No se pudo encontrar la columna "fec_pago" en la primera fila de datos. Verifica el encabezado del archivo.');
            }

            $fecPagoRaw = $data['fec_pago'];
            if (is_numeric($fecPagoRaw)) {
                $dateObj = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecPagoRaw);
            } else {
                $dateObj = \Carbon\Carbon::parse($fecPagoRaw);
            }

            $month = (int) $dateObj->format('m');
            $day = (int) $dateObj->format('d');
            $year = $dateObj->format('Y');

            $qq = ($month * 2) - ($day <= 15 ? 1 : 0);
            $periodo = str_pad($qq, 2, '0', STR_PAD_LEFT) . '-' . $year;

            // Validar que no exista ya esa plantilla (periodo) o su versión alternativa (AAAA-QQ)
            $parts = explode('-', $periodo);
            $alternatePeriodo = $parts[1] . '-' . $parts[0];
            
            $exists = Employee::whereIn('periodo', [$periodo, $alternatePeriodo])->exists();
            if ($exists) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Ya existen registros para el periodo $periodo (detectado de 'fec_pago'). Debes eliminar la importación previa si deseas re-subirla.");
            }

            $importId = uniqid();
            $fileName = $periodo . '_' . now()->format('YmdHis') . '_' . $file->getClientOriginalName();
            $file->storeAs('imports', $fileName, 'public');

            Excel::import(new EmployeesImport($periodo, $importId, $fileName), $file);

            return redirect()->route('employees.import-form')->with('success', "Archivo detectado como periodo $periodo e importado correctamente.");

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Error en importación: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene los periodos únicos ordenados cronológicamente descendente,
     * manejando ambos formatos: QQ-AAAA y AAAA-QQ.
     */
    private function getSortedPeriods()
    {
        return Employee::distinct()
            ->pluck('periodo')
            ->map(function ($p) {
                if (empty($p)) return null;
                $parts = explode('-', $p);
                if (count($parts) !== 2) return ['original' => $p, 'normalized' => $p];
                
                // Normalizar a AAAA-QQ para ordenación alfabética correcta
                $normalized = (strlen($parts[0]) === 4) ? $p : $parts[1] . '-' . $parts[0];
                return [
                    'original' => $p,
                    'normalized' => $normalized
                ];
            })
            ->filter()
            ->sortByDesc('normalized')
            ->pluck('original')
            ->values();
    }

    public function search(Request $request)
    {
        $id_empleado = $request->input('id_empleado');
        $selected_periodo = $request->input('periodo');
        
        $employees = [];
        $available_periods = [];
        $selected_employee = null;
        
        if ($id_empleado) {
            // Get available periods for this employee
            $available_periods = Employee::where('id_empleado', $id_empleado)
                ->distinct()
                ->pluck('periodo')
                ->all();
            
            rsort($available_periods);

            if (!$selected_periodo && !empty($available_periods)) {
                $selected_periodo = $available_periods[0];
            }

            if ($selected_periodo) {
                // Get all copies (plazas) for the selected period
                $employees = Employee::where('id_empleado', $id_empleado)
                    ->where('periodo', $selected_periodo)
                    ->get();
            }
        }

        return view('employees.search', compact('employees', 'id_empleado', 'available_periods', 'selected_periodo'));
    }
}
