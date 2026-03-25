<?php

namespace Tests\Feature;

use App\Imports\EmployeesImport;
use App\Exports\EmployeeConceptExport;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EmployeeImportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_imports_special_characters_correctly_from_win1252_csv()
    {
        // 1. Prepare a CSV content in Windows-1252 with "Ñ"
        $headers = "periodo,id_empleado,nombre,apellido_1,apellido_2,fec_pago\n";
        $row = "01-2026,12345,JUAN,NUÑEZ,PÉREZ,2026-01-15\n";
        $csvContent = mb_convert_encoding($headers . $row, 'Windows-1252', 'UTF-8');
        
        // 2. Write to a temporary file
        Storage::fake('public');
        $filePath = 'temp_import.csv';
        Storage::disk('public')->put($filePath, $csvContent);
        $fullPath = Storage::disk('public')->path($filePath);

        // 3. Run the import
        $import = new EmployeesImport('01-2026', 'test_import', 'temp_import.csv');
        Excel::import($import, $fullPath, null, \Maatwebsite\Excel\Excel::CSV);

        // 4. Verify database content
        $employee = Employee::where('id_empleado', '12345')->first();
        
        $this->assertNotNull($employee);
        $this->assertEquals('NUÑEZ', $employee->apellido_1);
        $this->assertEquals('PÉREZ', $employee->apellido_2);
    }

    /** @test */
    public function test_it_exports_concept_report_sorted_by_center_then_id()
    {
        // 1. Seed some data with mixed order
        Employee::create([
            'id_empleado' => '200',
            'periodo' => '01-2026',
            'id_centro_pago' => '00020',
            'nombre' => 'User 200',
            'apellido_1' => 'Test',
            'apellido_2' => 'Test',
            'nomina_data' => ['c_sindic_local' => 100]
        ]);

        Employee::create([
            'id_empleado' => '100',
            'periodo' => '01-2026',
            'id_centro_pago' => '00010',
            'nombre' => 'User 100',
            'apellido_1' => 'Test',
            'apellido_2' => 'Test',
            'nomina_data' => ['c_sindic_local' => 100]
        ]);

        Employee::create([
            'id_empleado' => '150',
            'periodo' => '01-2026',
            'id_centro_pago' => '00010',
            'nombre' => 'User 150',
            'apellido_1' => 'Test',
            'apellido_2' => 'Test',
            'nomina_data' => ['c_sindic_local' => 100]
        ]);

        // 2. Initialize export
        $export = new EmployeeConceptExport('c_sindic_local', '01-2026', 'SNTISSSTE');
        $collection = $export->collection();

        // 3. Verify order: Center 10 (IDs 100, 150), then Center 20 (ID 200)
        $sortedIds = $collection->pluck('id_empleado')->toArray();
        
        $this->assertEquals(['100', '150', '200'], array_values($sortedIds));
    }
}
