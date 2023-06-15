<?php

namespace App\Exports;

use App\Models\customer;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ExportCustomer implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function styles(Worksheet $sheet){
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
    
    public function headings():array{
        return[
            'id', 
            'Nombre',
            'Tax ID', 
            'Nombre de Contacto', 
            'Email de Contacto', 
            'Celular de Contacto', 
            'Creado', 
            'Editado'
        ];
    } 

    public function collection()
    {
        return customer::all();
    }
}
