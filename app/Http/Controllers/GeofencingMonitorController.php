<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GeofencingEvent;

class GeofencingMonitorController extends Controller
{
    public function index(Request $request)
    {
        $events = GeofencingEvent::latest()->limit(200)->get();

        return view('admin.geofencing-monitor', compact('events'));
    }
}
