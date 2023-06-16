<?php

namespace App\Exports;

use App\Models\CustomerShipper;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class shipper implements  FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{ /**
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
            'razon_social', 
            'tax_id', 
            'address', 
            'city', 
            'country', 
            'postal_code', 
            'create_user', 
            'company', 
            'remarks', 
            'created_at', 
            'updated_at'
        ];
    } 
    public function collection()
    {
        return CustomerShipper::all();
    }
}
