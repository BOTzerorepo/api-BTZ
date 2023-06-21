<?php

namespace App\Exports;

use App\Models\CustomerCnee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class consignee implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
    public function headings(): array
    {
        return [
            'id',
            'Nombre',
            'tax_id',
            'Direccion',
            'ciudad',
            'pais',
            'Codigo Postal',
            'Creado por',
            'Empresa',
            'remarks',
            'creado',
            'Editado'
        ];
    }
    public function collection()
    {
        return CustomerCnee::all();
    }
}
