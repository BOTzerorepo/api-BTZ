<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cntr = 'TCNU3856444';

$events = \Illuminate\Support\Facades\DB::table('cma_logs_events')->where('equipment_reference', $cntr)->count();
$coords = \Illuminate\Support\Facades\DB::table('cma_logs_coordinate')->where('equipmentReference', $cntr)->count();

echo "Events for $cntr: $events\n";
echo "Coords for $cntr: $coords\n";

$coords_alt = \Illuminate\Support\Facades\DB::table('cma_logs_coordinate')->where('equipmentReference', 'LIKE', "%$cntr%")->count();
echo "Coords (LIKE) for $cntr: $coords_alt\n";
