<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$list = \Illuminate\Support\Facades\DB::table('cma_logs_coordinate')
    ->select('equipmentReference')
    ->whereNotNull('equipmentReference')
    ->distinct()
    ->limit(10)
    ->pluck('equipmentReference');

print_r($list);
