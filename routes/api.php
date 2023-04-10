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
Route::get('/mailCargaNueva/{idCarga}/{user}','App\Http\Controllers\emailController@avisoNuevaCarga'); // pachimanok 
Route::get('/mailPrueba','App\Http\Controllers\emailController@apruebaEmail'); // pachimanok 




/* Envio de Emails */

Route::get('/mailPrueba','App\Http\Controllers\emailController@prueba');  
Route::get('/mailStatus/{cntr}/{empresa}/{booking}/{user}/{tipo}','App\Http\Controllers\emailController@cambiaStatus');  
Route::get('/cargaAsignada/{id}','App\Http\Controllers\emailController@cargaAsignada');  
Route::get('/trasnsporteAsignado/{id}','App\Http\Controllers\emailController@transporteAsignado');  


// Route::post('/imprimir/create','App\Http\Controllers\crearpdfControllerPDF@store')mostrar todos
// Route::get('/imprimirIns','App\Http\Controllers\imprimirPDF@store'); //mostrar todos
//Route::put('/imprimir/{id}','App\Http\Controllers\imprimirPDF@update');//actualizar
//Route::delete('/imprimir','App\Http\Controllers\imprimirPDF@destroy'); // eliminar

////////////////// DOCUMENTS ///////////////////

//////// USUARIOS INTERNOS ////////

Route::post('/docs/{booking}','App\Http\Controllers\DocumentController@store');
Route::get('/docsCntr/{booking}/{user}/{cntr}','App\Http\Controllers\DocumentController@indexCntr');
Route::get('/docsDel','App\Http\Controllers\DocumentController@destroy'); 

//////// USUARIOS EXTERNOS ////////

Route::get('/docsAtaReed/{booking}/{user}','App\Http\Controllers\DocumentController@index');
Route::post('/docsAta/{booking}','App\Http\Controllers\DocumentController@storeAta');
Route::post('/carga','App\Http\Controllers\LoadController@store');

 // TRUCK CONTROLLLER 
Route::post('/truck','App\Http\Controllers\TruckController@store'); // C
Route::get('/trucks/{customer}','App\Http\Controllers\TruckController@index');// R ALL
Route::get('/truck/{truck}','App\Http\Controllers\TruckController@show'); // R ONE
Route::post('/truck/{truck}','App\Http\Controllers\TruckController@update'); // U 
Route::delete('/truck/{truck}','App\Http\Controllers\TruckController@destroy'); // D 
Route::get('/truckTransport/{truck}','App\Http\Controllers\TruckController@showTransport'); // Show For Transport


// TRAILER CONTROLLLER 
Route::post('/trailer','App\Http\Controllers\TrailerController@store'); // C
Route::get('/trailer/{customer}','App\Http\Controllers\TrailerController@index');// R ALL
Route::get('/trailer/{trailer}','App\Http\Controllers\TrailerController@show'); // R ONE
Route::post('/trailer/{trailer}','App\Http\Controllers\TrailerController@update'); // U
Route::delete('/trailer/{trailer}','App\Http\Controllers\TrailerController@destroy'); // D
Route::get('/trailerTransport/{transport_id}','App\Http\Controllers\TrailerController@showTrailer'); // Show for Transport

// ASIGNACIONES
Route::get('/truckAsign/{id}','App\Http\Controllers\TruckController@trailerAsign'); // Show for Transport



Route::get('/user/{user}','App\Http\Controllers\UserController@show');


// EXPORT EXCEL

// Reportes de Cargas por fechas

Route::get('/excelCargasThisWeek','App\Http\Controllers\excelController@thisWeek');
Route::get('/excelCargasUncoming','App\Http\Controllers\excelController@uncoming');
Route::get('/excelCargasPast','App\Http\Controllers\excelController@past');

// Reportes por Status
Route::get('/excelNoAssigned','App\Http\Controllers\excelController@noAssigned');
Route::get('/excelOnBoard','App\Http\Controllers\excelController@onBoard');
Route::get('/excelAnyProblem','App\Http\Controllers\excelController@anyProblem');
Route::get('/excelFinish','App\Http\Controllers\excelController@loadFinish');
Route::get('/excelGoingToLoad','App\Http\Controllers\excelController@goingToLoad');
Route::get('/excelLoading','App\Http\Controllers\excelController@loading');
Route::get('/excelOnCustomPlace','App\Http\Controllers\excelController@onCustomPlace');
Route::get('/excelOnWay','App\Http\Controllers\excelController@onWay');
Route::get('/excelAllLoads','App\Http\Controllers\excelController@allLoads');
Route::get('/excelStacking','App\Http\Controllers\excelController@stacking');
Route::get('/excelAssigned','App\Http\Controllers\excelController@assigned');
Route::get('/excelAssigned','App\Http\Controllers\excelController@assigned');

// Reportes de DB Select

Route::get('/excelAgencies','App\Http\Controllers\excelController@agencies');
Route::get('/excelAtas','App\Http\Controllers\excelController@atas');
Route::get('/excelPaymentMode','App\Http\Controllers\excelController@paymentMode');
Route::get('/excelUsers','App\Http\Controllers\excelController@users');
Route::get('/excelCustomAgents','App\Http\Controllers\excelController@customAgents');
Route::get('/excelCompanies','App\Http\Controllers\excelController@companies');
Route::get('/excelWarehouseContainer','App\Http\Controllers\excelController@warehouseContainer');
Route::get('/excelContainerTypes','App\Http\Controllers\excelController@containerTypes');

// Reportes Flotas
Route::get('/excelDriversFree','App\Http\Controllers\excelController@driversFree');
Route::get('/excelDriversBusy','App\Http\Controllers\excelController@driversBusy');
Route::get('/excelDrivers','App\Http\Controllers\excelController@drivers');
Route::get('/excelTrucks','App\Http\Controllers\excelController@trucks');
Route::get('/excelTrailers','App\Http\Controllers\excelController@trailers');
Route::get('/excelTransports','App\Http\Controllers\excelController@transports');

// SEGUROS

Route::post('/seguro','App\Http\Controllers\seguroController@store');
Route::post('/agencias/{id}','App\Http\Controllers\AgenciaController@update');



// MAPS


Route::get('/lugarDeCarga/{patente}','App\Http\Controllers\lugaresDeCarga@coordenadas');
Route::get('/accionLugarDeCarga/{idTrip}','App\Http\Controllers\lugaresDeCarga@accionLugarDeCarga');
Route::get('/accionLugarAduana/{idTrip}','App\Http\Controllers\lugaresDeCarga@accionLugarAduana');
Route::get('/accionLugarDescarga/{idTrip}','App\Http\Controllers\lugaresDeCarga@accionLugarDescarga');

Route::get('/servicioSatelital','App\Http\Controllers\ServiceSatelital@serviceSatelital');

//JUANI

//Ata
Route::get('/atas','App\Http\Controllers\AtaController@index'); //Busca todos los Agente de transporte
Route::get('/ata/{id}','App\Http\Controllers\AtaController@show'); //Busca un Agente de transporte
Route::post('/ata','App\Http\Controllers\AtaController@store'); //Crea un nuevo Agente de transporte
Route::post('/ata/{id}','App\Http\Controllers\AtaController@update'); //Actualiza los datos de un Agente de transporte
Route::delete('/ata/{id}','App\Http\Controllers\AtaController@destroy'); //Elimina un Agente de transporte


// DRIVER CONTROLLLER trailerAsign

Route::get('/drivers/{transport_id}','App\Http\Controllers\DriverController@showDriver'); 
Route::get('/drivers','App\Http\Controllers\DriverController@index'); 
Route::get('/driver/{id}','App\Http\Controllers\DriverController@show'); 
Route::post('/driver','App\Http\Controllers\DriverController@store'); 
Route::post('/driverStatus/{id}','App\Http\Controllers\DriverController@status'); 
Route::post('/driver/{id}','App\Http\Controllers\DriverController@update'); 
Route::delete('/driver/{id}','App\Http\Controllers\DriverController@destroy'); 

//Transporte
Route::get('/transporteCustomer/{id}','App\Http\Controllers\TransportController@indexTransporteCustomer'); //Busca todos los transportes del customerId
Route::get('/transportes','App\Http\Controllers\TransportController@index'); 
Route::get('/transporte/{id}','App\Http\Controllers\TransportController@show'); 
Route::post('/transporte','App\Http\Controllers\TransportController@store'); 
Route::post('/transporte/{id}','App\Http\Controllers\TransportController@update'); 
Route::delete('/transporte/{id}','App\Http\Controllers\TransportController@destroy'); 

//Agencia
Route::get('/agencias','App\Http\Controllers\AgencyController@index'); //Busca todas las agencias
Route::get('/agencia/{id}','App\Http\Controllers\AgencyController@show'); //Busca una sola agencia
Route::post('/agencia','App\Http\Controllers\AgencyController@store'); //Crea una nueva Agencia
Route::post('/agencia/{id}','App\Http\Controllers\AgencyController@update'); //Actualiza los datos de una Agencia
Route::delete('/agencia/{id}','App\Http\Controllers\AgencyController@destroy'); //Elimina una Agencia
