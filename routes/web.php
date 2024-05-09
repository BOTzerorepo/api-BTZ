<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ActionController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    $varibles = DB::table('variables')->get();
    return $varibles;
/*     return view('welcome'); */
});

Route::get('/seguros', function () {
    return view('seguro');
});
Route::get('/itinerarios', function () {
    return view('itinerarios');
});
Route::get('/itinerario', function () {
    return view('itinerarioShow');
});
Route::post('/itinerarios', 'App\Http\Controllers\ItinerarioController@guardarFormulario');


Route::resource('actions', ActionController::class);


