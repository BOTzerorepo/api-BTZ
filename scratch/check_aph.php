<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cntr = 'APHU6723929';
$coords = \Illuminate\Support\Facades\DB::table('cma_logs_coordinate')
    ->where('equipmentReference', $cntr)
    ->count();

echo "Coords for $cntr: $coords\n";
