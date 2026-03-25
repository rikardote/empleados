<?php

namespace Tests\Feature;

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ApiEmployeeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_can_search_employees_via_api()
    {
        Employee::create([
            'id_empleado' => '12345',
            'nombre' => 'JUAN',
            'apellido_1' => 'PEREZ',
            'apellido_2' => 'GONZALEZ',
            'periodo' => '01-2026'
        ]);

        $response = $this->getJson('/api/employees/search?id_empleado=12345');

        $response->assertStatus(200);
        $response->assertJsonFragment(['nombre' => 'JUAN']);
    }

    /** @test */
    public function test_it_returns_import_status()
    {
        Cache::put('import_progress_test_id', [
            'total' => 100,
            'current' => 50,
            'status' => 'processing'
        ], 3600);
        Cache::put('import_progress_test_id_count', 50, 3600);

        $response = $this->getJson('/api/employees/import-status/test_id');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'processing', 'current' => 50]);
    }

    /** @test */
    public function test_it_returns_available_periods()
    {
        Employee::create([
            'id_empleado' => '1',
            'nombre' => 'A',
            'apellido_1' => 'B',
            'apellido_2' => 'C',
            'periodo' => '01-2026'
        ]);

        $response = $this->getJson('/api/employees/periods');

        $response->assertStatus(200);
        $this->assertContains('01-2026', $response->json());
    }
}
