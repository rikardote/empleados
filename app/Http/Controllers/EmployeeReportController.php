<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

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
                ->get();
        }

        return view('employees.concept-report', compact('concepts', 'periods', 'selectedConcept', 'selectedPeriod', 'employees'));
    }
}
