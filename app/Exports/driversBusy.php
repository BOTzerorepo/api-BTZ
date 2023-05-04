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

class driversBusy implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
            "Vto Licencia",
            "Transporte",
            "Lugar",
            "Observaciones"
        ];
    } 
    public function collection()
    {
        $driversBusy = DB::table('drivers')->select('id', 'nombre', 'vto_carnet', 'transporte', 'place', 'Observaciones')->where('status_chofer', '=', 'ocupado')->get();
        return $driversBusy;
    }
}

