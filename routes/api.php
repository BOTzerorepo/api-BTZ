<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* Impresion de PDF */

Route::get('/imprimirCarga','App\Http\Controllers\crearpdfController@carga'); 
Route::get('/imprimirVacio','App\Http\Controllers\crearpdfController@vacio');  
Route::get('/imprimirEviarInstrucivo/{cntr}','App\Http\Controllers\crearpdfController@cargaPorMail');  


Route::get('/oceansInstruction/{trip}','App\Http\Controllers\oceansInstructions@generateOceansIntruction'); 


/* Envio de Emails */

Route::get('/mailPrueba','App\Http\Controllers\emailController@prueba');  
Route::get('/mailStatus/{cntr}/{empresa}/{booking}/{user}/{tipo}','App\Http\Controllers\emailController@cambiaStatus');  
Route::get('/cargaAsignada/{id}','App\Http\Controllers\emailController@cargaAsignada');  


// Route::post('/imprimir/create','App\Http\Controllers\crearpdfControllerPDF@store')mostrar todos
// Route::get('/imprimirIns','App\Http\Controllers\imprimirPDF@store'); //mostrar todos
//Route::put('/imprimir/{id}','App\Http\Controllers\imprimirPDF@update');//actualizar
//Route::delete('/imprimir','App\Http\Controllers\imprimirPDF@destroy'); // eliminar

Route::post('/docs/{booking}','App\Http\Controllers\DocumentController@store');
Route::post('/docsAta/{booking}','App\Http\Controllers\DocumentController@storeAta');

Route::get('/docsAtaReed/{booking}/{user}','App\Http\Controllers\DocumentController@index');
Route::get('/docsCntr/{booking}/{user}/{cntr}','App\Http\Controllers\DocumentController@indexCntr');


Route::get('/docsDel','App\Http\Controllers\DocumentController@destroy'); //mostrar todos
 //mostrar todos


 // TRUCK CONTROLLLER 
Route::post('/truck','App\Http\Controllers\TruckController@store');
Route::delete('/truck/{truck}','App\Http\Controllers\TruckController@destroy');
Route::post('/truck/{truck}','App\Http\Controllers\TruckController@update');
Route::get('/trucks/{customer}','App\Http\Controllers\TruckController@index');
Route::get('/truck/{truck}','App\Http\Controllers\TruckController@show');
Route::get('/truckTransport/{truck}','App\Http\Controllers\TruckController@showTransport');




// TRAILER CONTROLLLER 
Route::post('/trailer','App\Http\Controllers\TrailerController@store');
Route::post('/trailer/{trailer}','App\Http\Controllers\TrailerController@update');
Route::delete('/trailer/{trailer}','App\Http\Controllers\TrailerController@destroy');
Route::get('/trailerTransport/{transport_id}','App\Http\Controllers\TrailerController@showTrailer');


// DRIVER CONTROLLLER 

Route::get('/drivers/{transport_id}','App\Http\Controllers\DriverController@showDriver');

Route::get('/user/{user}','App\Http\Controllers\UserController@show');




