<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class atas implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    
    use Exportable;
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
            "Nombre",
            "Tax ID",
            "Provincia",
            "Pais",
            "email"
        ];
    } 
   
    
    public function collection()
    {
        $atas = DB::table('ata')->select('id',"razon_social","tax_id","provincia","pais","mail")->get();
        return $atas;
    }
}