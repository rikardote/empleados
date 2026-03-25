<?php

namespace Tests\Feature;

use App\Models\Fm1ImportBatch;
use App\Models\Fm1Form;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class Fm1ImportTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_can_import_fm1_records_from_excel()
    {
        // 1. Prepare fake storage and file
        Storage::fake('public');
        
        // Headers consistent with Fm1Import.php
        $headers = "nombre,num_empleado,rfc,curp,sexo,escolaridad,cedula,domicilio,hijos,nacionalidad,cod_tipo_movimiento,tipo_mov,fecha_movimiento,fecha_final,codigo_puesto,nivel_subnivel,denominacion_puesto,numero_plaza,tipo_plaza,ocupacion,estatus_plaza,unidad_administrativa,unidad_administrativa_denominacion,adscripcion,adscripcion_denominacion,adscripcion_fisica,adscripcion_fisica_denominacion,servicio,servicio_denominacion,codigo_turno,codigo_turno_descripcion,jornada,horario_codigo,horario_entrada1,horario_salida1,horario_entrada2,horario_salida2,observaciones,turno_opcional,percepcion_adicional,riesgos_profesionales,mando,enlace_alta_responsabilidad,enlace,operativo,rama_medica,nombre_ant,num_empleado_ant,cod_movi_ant,tipo_mov_ant,fecha_inicio_ant,fecha_fin_ant,turno_opcional_ant,percepcion_adicional_ant,riesgos_prof_ant,nombre_trab_ant,titular_area,cargo_titular_area,responsable_admvo,cargo_responsable_admvo,titular_centro,cargo_titular_centro\n";
        $row = "PEDRO PEREZ,1234,PERP800101,CURP123,M,LIC,123,CALLE 1,0,MEX,1,ALTA,2026-01-01,2026-12-31,P1,N1,PROF,P123,B,PROF,A,UA1,UA1D,A1,A1D,AF1,AF1D,S1,S1D,T1,T1D,J1,H1,08:00,16:00,,,OBS,0,0,0,0,0,0,1,0,ANT,ANT1,ANTC,ANTT,2025-01-01,2025-12-31,0,0,0,TRABANT,TITULAR,CARGOTIT,RESP,CARGORESP,TITCENT,CARGOCENT\n";
        
        $file = UploadedFile::fake()->createWithContent('fm1_test.csv', $headers . $row);

        // 2. Perform import request
        $response = $this->post(route('fm1.import.store'), [
            'file' => $file,
            'notes' => 'Test import'
        ]);

        // 3. Assertions
        $response->assertStatus(302); // Redirect back
        $this->assertDatabaseHas('fm1_import_batches', [
            'original_filename' => 'fm1_test.csv',
            'status' => 'completed',
            'record_count' => 1
        ]);
        
        $this->assertDatabaseHas('fm1_forms', [
            'nombre' => 'PEDRO PEREZ',
            'num_empleado' => '1234'
        ]);
    }

    /** @test */
    public function test_it_can_delete_a_batch_and_its_records()
    {
        $batch = Fm1ImportBatch::create([
            'original_filename' => 'test.xlsx',
            'stored_filename' => 'test_123.xlsx',
            'status' => 'completed'
        ]);

        Fm1Form::create([
            'import_batch_id' => $batch->id,
            'nombre' => 'TEST USER'
        ]);

        $response = $this->delete(route('fm1.import.batch.destroy', $batch->id));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('fm1_import_batches', ['id' => $batch->id]);
        $this->assertDatabaseMissing('fm1_forms', ['import_batch_id' => $batch->id]);
    }

    /** @test */
    public function test_it_can_view_batch_details()
    {
        $batch = Fm1ImportBatch::create([
            'original_filename' => 'test.xlsx',
            'stored_filename' => 'test_123.xlsx',
            'status' => 'completed'
        ]);

        $response = $this->get(route('fm1.import.batch', $batch->id));

        $response->assertStatus(200);
        $response->assertViewHas('batch');
        $response->assertSee('test.xlsx');
    }
}
