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

class thisWeek implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles

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
            "Tipo",
            "Teferencia Customer",
            "Referencia Carga",
            "Referencia Viaje",
            "Tipo de Carga",
            "Shipper",
            "Commodity",
            "Lugar de Retiro",
            "Lugar de Carga",
            "Día de Carga",
            "Lugar de Descarga",
            "Dia de Descarga",
            "Aduana"
        ];
    } 

    public function collection()
    {
        $cargas = DB::table('cntr')->join('carga','cntr.booking', '=', 'carga.booking')->select('id_cntr',"type","ref_customer","carga.booking","cntr_number","cntr_type","shipper","commodity","retiro_place","load_place","load_date","unload_place","cut_off_fis","custom_place")
        ->whereBetween('carga.load_date', [
            Carbon::parse('last monday')->startOfDay(),
            Carbon::parse('next sunday')->endOfDay(),
            ])->where('carga.status', '!=', 'TERMINADA')->orderBy('carga.load_date', 'desc')->get();

            return $cargas;
    }

    
    
    
}
