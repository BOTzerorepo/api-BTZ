<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$count = \Illuminate\Support\Facades\DB::table('cma_logs_coordinate')->count();
$events = \Illuminate\Support\Facades\DB::table('cma_logs_events')->count();
echo "Coords: $count\n";
echo "Events: $events\n";

$sample = \Illuminate\Support\Facades\DB::table('cma_logs_coordinate')->limit(1)->first();
print_r($sample);
