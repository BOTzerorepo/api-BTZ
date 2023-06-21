<?php

namespace App\Exports;

use App\Models\CustomerUnloadPlace;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UnloadPlaces implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
            'Descripcion',
            'Direccion', 
            'Link Maps', 
            'Latitud', 
            'Longitud', 
            'Pais', 
            'Provincia',
            'km de la ciudad',
            'user',
            'Empresa',
            'Comentario',
            'Creado',
            'Editado'

        ];
    } 
    public function collection()
    {
        return CustomerUnloadPlace::all();
    }
}
