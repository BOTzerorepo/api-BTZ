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

class companies implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
            "Razon Social",
            "CUIT",
            "IIBB",
            "Contacto Admin",
            "Celular Admin",
            "Email Administración",
            "Contacto Logistica",
            "Celular Logistica",
            "Email Logistica",
            "Fecha de Creación",
        ];
    } 
    public function collection()
    {
        $companies = DB::table('empresas')->select('id','razon_social', 'CUIT', 'IIBB', 'name_admin',  'cel_admin','mail_admin', 'name_logistic', 'cel_logistic','mail_logistic' , 'created_at')->get();
        return $companies;
    }
}