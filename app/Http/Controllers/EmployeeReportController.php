<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

use App\Exports\EmployeeConceptExport;
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

        $periods = Employee::distinct()->orderByDesc('periodo')->pluck('periodo');

        $selectedConcept = $request->input('concept');
        $selectedPeriod = $request->input('period');

        $employees = collect();

        if ($selectedConcept && $selectedPeriod) {
            $employees = Employee::where('periodo', $selectedPeriod)
                ->where(function ($query) use ($selectedConcept) {
                    $query->whereNotNull("nomina_data->$selectedConcept")
                          ->where("nomina_data->$selectedConcept", '!=', 0);
                })
                ->get()
                ->unique('id_empleado');
        }

        return view('employees.concept-report', compact('concepts', 'periods', 'selectedConcept', 'selectedPeriod', 'employees'));
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
}
