<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cntr = 'TCNU3856444';

$all_columns = ['patente', 'transportOrder', 'equipmentReference', 'carrierBookingReference'];
foreach ($all_columns as $col) {
    $count = \Illuminate\Support\Facades\DB::table('cma_logs_coordinate')->where($col, $cntr)->count();
    echo "Coords ($col) for $cntr: $count\n";
}
