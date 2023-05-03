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

class trucks implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
            "Modelo",
            "Dominio",
            "Año",
            "Tipo",
            "Sendores",
            "Satelital",
            "Transporte"
        ];
    } 
    public function collection()
    {
        $trucks = DB::table('trucks')->join('transports','transports.id','=','trucks.transport_id')->select('trucks.id', 'trucks.model', 'trucks.domain', 'trucks.year','trucks.type', 'trucks.device_truck','trucks.satelital_location', 'transports.razon_social')->get();
        return $trucks;
    }
}

