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

class agencies implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
            "Razon Social",
            "Tax ID",
            "Puerto",
            "Contacto",
            "Celular",
            "email",
            "Observaciones"
        ];
    } 

    public function collection()
    {
        $agencies = DB::table('agencies')->select('id',"description","razon_social","tax_id","puerto","contact_name","contact_phone","contact_mail","observation_gral")->get();
        return $agencies;
    }
}