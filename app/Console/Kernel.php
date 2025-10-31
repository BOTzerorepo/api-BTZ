<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    
    protected function scheduleTimezone()
    {
        return 'America/Argentina/Buenos_Aires';
    }
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('revisar:coordenadas')->everyTenMinutes();

        $schedule->call(function () {
            $tripIds = \Illuminate\Support\Facades\DB::table('cntr')
                ->join('asign','cntr.cntr_number','=','asign.cntr_number')
                ->where('cntr.main_status','!=','TERMINADA')
                ->whereNotNull('asign.truck')
                ->pluck('cntr.id_cntr')
                ->unique();
    
            foreach ($tripIds as $tid) {
                dispatch(new \App\Jobs\ProcessTripGeofencing((int)$tid))
                    ->onQueue('geofencing');
            }
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
