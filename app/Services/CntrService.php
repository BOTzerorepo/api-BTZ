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

        $cntrModel = cntr::withTrashed()->where('cntr_number', $cntrNumber)->first() ?: new cntr();
        if (method_exists($cntrModel, 'trashed') && $cntrModel->trashed()) {
            $cntrModel->restore();
        }
        $cntrModel->booking      = $booking;
        $cntrModel->cntr_number  = $cntrNumber;
        $cntrModel->cntr_seal    = $data['cntr_seal'] ?? null;
        $cntrModel->cntr_type    = $data['cntr_type'] ?? null;
        $cntrModel->retiro_place = $data['retiro_place'] ?? null;
        $cntrModel->confirmacion = $data['confirmacion'] ?? 0;
        $cntrModel->user_cntr    = $data['user_cntr'];
        $cntrModel->company      = $data['company'];
        $cntrModel->save();

        $asignModel = asign::withTrashed()->where('cntr_number', $cntrNumber)->first() ?: new asign();
        if (method_exists($asignModel, 'trashed') && $asignModel->trashed()) {
            $asignModel->restore();
        }
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
 * Borra FÍSICAMENTE un contenedor y todo lo que cuelga de él
 * (asignación, historial de status y puntos de interés del pivote),
 * dentro de una transacción. Idempotente: no deja huérfanos que
 * interfieran al recrear una carga/contenedor con los mismos datos.
 */
    public function purge(int $idCntr): array
    {
        return DB::transaction(function () use ($idCntr) {
            $cntr = DB::table('cntr')->where('id_cntr', $idCntr)->first();
            if (!$cntr) {
                return ['deleted' => false, 'reason' => 'not_found'];
            }

            // Hijos primero (por si hay FKs con RESTRICT)
            DB::table('cntr_interest_point')->where('cntr_id_cntr', $cntr->id_cntr)->delete(); // pivote PI
            DB::table('status')->where('cntr_number', $cntr->cntr_number)->delete();           // historial
            DB::table('asign')->where('cntr_number', $cntr->cntr_number)->delete();            // asignación

            // El contenedor al final
            DB::table('cntr')->where('id_cntr', $cntr->id_cntr)->delete();

            return [
                'deleted'     => true,
                'cntr_number' => $cntr->cntr_number,
                'booking'     => $cntr->booking,
            ];
        });
    }

    /**
     * Elimina un contenedor y su asignación sin soft-delete
     */
    public function destroy(int $cntrId): array
    {
        $cntrModel = cntr::findOrFail($cntrId);
        $idCarga   = DB::table('carga')->where('booking', $cntrModel->booking)->value('id');

        $this->purge((int) $cntrModel->id_cntr);

        return ['id' => $idCarga];
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
