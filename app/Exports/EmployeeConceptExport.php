<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithTitle;

class EmployeeConceptExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize, WithCustomStartCell, WithTitle
{
    private $concept;
    private $period;
    private $conceptName;
    private $rowNumber = 1;

    public function __construct($concept, $period, $conceptName = null)
    {
        $this->concept = $concept;
        $this->period = $period;
        $this->conceptName = $conceptName ?? $concept;
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public function title(): string
    {
        return $this->conceptName;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Employee::where('periodo', $this->period)
            ->where(function ($query) {
                $query->whereNotNull("nomina_data->{$this->concept}")
                      ->where("nomina_data->{$this->concept}", '!=', 0);
            })
            ->get()
            ->unique('id_empleado')
            ->sortBy([
                ['id_centro_pago', 'asc'],
                ['id_empleado', 'asc'],
            ]);
    }

    public function headings(): array
    {
        return [
            '#',
            'Num Empleado',
            'Nombre',
            'Puesto',
            'Plaza',
            'Centro de Trabajo',
        ];
    }

    public function map($employee): array
    {
        return [
            $this->rowNumber++,
            $employee->id_empleado,
            $employee->nombre . ' ' . $employee->apellido_1 . ' ' . $employee->apellido_2,
            $employee->id_puesto_plaza,
            $employee->id_plaza,
            str_pad($employee->id_centro_pago, 5, '0', STR_PAD_LEFT),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Formatting the main title at the top
                $title = strtoupper($this->conceptName) . " - QNA: " . $this->period;
                
                $event->sheet->getDelegate()->mergeCells('A1:F1');
                $event->sheet->getDelegate()->setCellValue('A1', $title);
                
                // Styling
                $event->sheet->getDelegate()->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Headers style (Row 2)
                $event->sheet->getDelegate()->getStyle('A2:F2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'C00000'],
                    ],
                ]);
            },
        ];
    }
}
