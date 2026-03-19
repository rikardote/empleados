<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Imports\EmployeesImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Employee::paginate(50));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_empleado' => 'required|unique:employees',
            'nombre' => 'required',
            'apellido_1' => 'required',
            'id_legal' => 'nullable', // RFC
            'id_c_u_r_p_st' => 'nullable', // CURP
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $employee = Employee::create($request->all());

        return response()->json($employee, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return response()->json($employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $employee->update($request->all());

        return response()->json($employee);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return response()->json(null, 204);
    }

    /**
     * Import employees from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
            'periodo' => 'nullable|string',
            'import_id' => 'required|string',
        ]);

        ini_set('memory_limit', '1G');
        
        $importId = $request->input('import_id');
        $periodo = $request->input('periodo', 'N_A');
        
        // Guardar el archivo para futura consulta/descarga
        $file = $request->file('file');
        $fileName = $periodo . '_' . now()->format('YmdHis') . '_' . $file->getClientOriginalName();
        $file->storeAs('imports', $fileName, 'public');

        // Background imports are better, but for now we do it synchronously
        // and Laravel-Excel will trigger events.
        Excel::import(new EmployeesImport($periodo, $importId), $file);

        return response()->json(['message' => 'Employees imported successfully', 'saved_file' => $fileName], 200);
    }

    /**
     * Get the status of an ongoing import.
     */
    public function getImportStatus($id)
    {
        $data = \Illuminate\Support\Facades\Cache::get("import_progress_{$id}");
        $realCount = \Illuminate\Support\Facades\Cache::get("import_progress_{$id}_count", 0);
        
        if ($data) {
            $data['current'] = $realCount; // Use the most up-to-date count
            return response()->json($data);
        }
        
        return response()->json(['status' => 'not_found'], 404);
    }

    /**
     * Search for an employee by ID and period.
     */
    public function search(Request $request)
    {
        $query = Employee::query();

        if ($request->has('id_empleado')) {
            $query->where('id_empleado', $request->input('id_empleado'));
        }

        if ($request->has('periodo')) {
            $query->where('periodo', $request->input('periodo'));
        }

        return response()->json($query->get());
    }

    /**
     * Get unique periods.
     */
    public function getPeriods()
    {
        return response()->json(Employee::distinct()->pluck('periodo'));
    }
}
