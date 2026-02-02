<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\PuntoInteresEntrada;
use App\Mail\PuntoInteresSalida;

class SendGeoEventNotifications implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $tripId,
        public string $cntrNumber,
        public string $domain,
        public int $poiId,
        public string $action // ENTER|EXIT
    ) {}

    public function handle(): void
    {
        $poi = DB::table('interest_points')->where('id', $this->poiId)->first();
        $cntr = DB::table('cntr')
            ->join('asign', 'cntr.cntr_number', '=', 'asign.cntr_number')
            ->join('carga', 'cntr.booking', '=', 'carga.booking')
            ->where('cntr.id_cntr', $this->tripId)
            ->select('cntr.*','asign.*','carga.*')
            ->first();

        // Mail
        try {
            $mailable = $this->action === 'ENTER'
                ? new PuntoInteresEntrada($cntr, $poi)
                : new PuntoInteresSalida($cntr, $poi);

            // (usa tu misma lógica de TO/CC/BCC si querés; aquí envío simple)
            $to = config('mail.to_default', 'soporte@rail.ar');
            \Log::info('POI mail: sending', [
              'to' => $to,
              'action' => $this->action ?? null,
              'cntr' => $cntr->cntr_number ?? null,
              'poi_id' => $poi->id ?? null,
            ]);
            
            Mail::to($to)->send($mailable);
            
            \Log::info('POI mail: sent', [
              'to' => $to,
              'action' => $this->action ?? null,
              'cntr' => $cntr->cntr_number ?? null,
              'poi_id' => $poi->id ?? null,
            ]);

        } catch (\Throwable $e) {
            Log::error('SendGeoEventNotifications mail error: '.$e->getMessage());
        }

        // Webhook opcional a n8n
        try {
            Http::timeout(8)->post('https://n8n.rail.ar/webhook/reporte-cma', [
                'function'    => __FUNCTION__,
                'trip_id'     => $this->tripId,
                'cntr_number' => $this->cntrNumber,
                'domain'      => $this->domain,
                'poi_id'      => $this->poiId,
                'action'      => $this->action,
            ]);
        } catch (\Throwable $e) {
            Log::warning('SendGeoEventNotifications webhook warn: '.$e->getMessage());
        }
    }
}
