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

class transports implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
            "Razón Social",
            "CUIT",
            "Direccion",
            "Provincia",
            "Pais",
            "Contacto Logistica",
            "Celular Logistica",
            "Mail Logistica",
            "Contacto Administración",
            "Celular Administración",
            "Mail Administración"
        ];
    } 
    public function collection()
    {
        $transport = DB::table('transports')->select('id', 'razon_social', 'CUIT', 'Direccion','Provincia', 'Pais','contacto_logistica_nombre', 'contacto_logistica_celular', 'contacto_logistica_mail','contacto_admin_nombre', 'contacto_admin_celular', 'contacto_admin_mail')->get();
        return $transport;
    }
}

