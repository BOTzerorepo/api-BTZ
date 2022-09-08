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

class trailers implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
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
            "Dominio",
            "Tipo",
            "Año",
            "Transporte"
        ];
    } 
    public function collection()
    {
        $trailers = DB::table('trailers')->join('transporte','transporte.id','=','trailers.transport_id')->select('trailers.id', 'trailers.domain','trailers.type','trailers.year','transporte.razon_social')->get();
        return $trailers;
    }
}

