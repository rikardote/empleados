<?php

namespace Tests\Unit;

use App\Models\Employee;
use Tests\TestCase;

class EmployeeModelTest extends TestCase
{
    /** @test */
    public function test_it_categorizes_nomina_data_correctly()
    {
        $employee = new Employee([
            'nomina_data' => [
                'sal_base' => 1000.50, // perception
                'ispt' => 150.20,       // deduction
                'total_devengos' => 1000.50,
                'total_retenido_a' => 150.20,
                'liquido' => 850.30
            ]
        ]);

        $categorized = $employee->getCategorizedNomina();

        $this->assertArrayHasKey('sal_base', $categorized['perceptions']);
        $this->assertArrayHasKey('ispt', $categorized['deductions']);
        $this->assertEquals(1000.50, $categorized['total_devengos']);
        $this->assertEquals(150.20, $categorized['total_retenido_a']);
        $this->assertEquals(850.30, $categorized['liquido']);
    }

    /** @test */
    public function test_it_handles_missing_nomina_data()
    {
        $employee = new Employee(['nomina_data' => null]);
        $categorized = $employee->getCategorizedNomina();

        $this->assertIsArray($categorized['perceptions']);
        $this->assertEmpty($categorized['perceptions']);
        $this->assertEquals(0, $categorized['liquido']);
    }
}
