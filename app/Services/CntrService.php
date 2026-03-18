<?php

namespace App\Services;

use App\Models\asign;
use App\Models\cntr;
use App\Models\Carga;
use Illuminate\Support\Facades\DB;

class CntrService
{
    /**
     * Crea un contenedor nuevo y su asignación correspondiente.
     */
    public function store(array $data): array
    {
        $booking      = $data['booking'];
        $cntrNumber   = $data['cntr_number'] ?: $this->generateCntrNumber($booking);

        $cntrModel = new cntr();
        $cntrModel->booking      = $booking;
        $cntrModel->cntr_number  = $cntrNumber;
        $cntrModel->cntr_seal    = $data['cntr_seal'] ?? null;
        $cntrModel->cntr_type    = $data['cntr_type'] ?? null;
        $cntrModel->retiro_place = $data['retiro_place'] ?? null;
        $cntrModel->confirmacion = $data['confirmacion'] ?? 0;
        $cntrModel->user_cntr    = $data['user_cntr'];
        $cntrModel->company      = $data['company'];
        $cntrModel->save();

        $asignModel = new asign();
        $asignModel->cntr_number = $cntrNumber;
        $asignModel->booking     = $booking;
        $asignModel->save();

        $idCarga = DB::table('carga')->where('booking', $booking)->value('id');

        return [
            'success' => true,
            'detail'  => $cntrModel,
            'idCarga' => $idCarga,
        ];
    }

    /**
     * Elimina un contenedor y su asignación por soft-delete.
     */
    public function destroy(int $cntrId): array
    {
        $cntrModel  = cntr::findOrFail($cntrId);
        $asignModel = asign::where('cntr_number', $cntrModel->cntr_number)->first();
        $carga      = Carga::where('booking', $cntrModel->booking)->first();

        $cntrModel->delete();
        $asignModel?->delete();

        return ['id' => $carga->id ?? null];
    }

    /**
     * Genera un número de contenedor basado en el booking + correlativo.
     */
    private function generateCntrNumber(string $booking): string
    {
        $count = DB::table('cntr')->where('booking', $booking)->count();
        return $booking . ($count + 1);
    }
}
