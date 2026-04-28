<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cntr = 'TCNU3856444';
$booking = \Illuminate\Support\Facades\DB::table('cma_logs_events')
    ->where('equipment_reference', $cntr)
    ->pluck('carrier_booking_reference')
    ->first();

echo "Booking for $cntr: $booking\n";

if ($booking) {
    $coords = \Illuminate\Support\Facades\DB::table('cma_logs_coordinate')
        ->where('carrierBookingReference', $booking)
        ->count();
    echo "Coords for booking $booking: $coords\n";
    
    $sample = \Illuminate\Support\Facades\DB::table('cma_logs_coordinate')
        ->where('carrierBookingReference', $booking)
        ->first();
    print_r($sample);
}
