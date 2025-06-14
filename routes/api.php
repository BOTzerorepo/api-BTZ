<?php

use App\Http\Controllers\cntrController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FleteroController;
use App\Http\Controllers\AuthController;


Route::post('register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::group(['middleware' => 'auth:api'], function () {
  Route::post('logout', [AuthController::class, 'logout']);
  Route::get('user', [AuthController::class, 'getAuthenticatedUser']);
  Route::get('takeUser', 'App\Http\Controllers\FcmTokenController@takeUser');

});

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

Route::get('/getUsersToken', 'App\Http\Controllers\FcmTokenController@getUsersWithTokens');

Route::get('/notificationMobile/{token}/{title}/{body}', 'App\Http\Controllers\FcmTokenController@sendNotification');
Route::put('/registerToken', 'App\Http\Controllers\FcmTokenController@registerToken');
Route::put('/updatToken', 'App\Http\Controllers\FcmTokenController@updateToken');

Route::get('/notifyUsers', 'App\Http\Controllers\FcmTokenController@notifyUsers');
Route::get('/takeUser', 'App\Http\Controllers\FcmTokenController@takeUser');



Route::get('/ejecutar/{puntoActivoId}/{contenedorId}','App\Http\Controllers\ServiceSatelital@ejecutarAccionEntrada');
Route::get('/points_of_interest','App\Http\Controllers\InterestPointController@index');
Route::post('/points_of_interest','App\Http\Controllers\InterestPointController@store');
Route::put('/points_of_interest/{id}','App\Http\Controllers\InterestPointController@update');
Route::delete('/points_of_interest/{id}','App\Http\Controllers\InterestPointController@destroy');
//CARGA--PUNTO INTERES
Route::post('/points_of_interest_carga/{id}','App\Http\Controllers\InterestPointController@agregarPuntoInteresCarga');
Route::get('/points_of_interest_cntr/{id}','App\Http\Controllers\InterestPointController@puntoInteresCntr');
Route::post('/actualizar_points_of_interest_cntr/{id}','App\Http\Controllers\InterestPointController@actualizarPuntosInteresCntr');
/* 
ooooo   ooooo   .oooooo.   ooo        ooooo oooooooooooo      ooooo ooooo      ooo oooooooooooo   .oooooo.        8 8 8 8 8 8 8 8 8 8 8 8 8 8
`888'   `888'  d8P'  `Y8b  `88.       .888' `888'     `8      `888' `888b.     `8' `888'     `8  d8P'  `Y8b       8 8 8 8 8 8 8 8 8 8 8 8 8 8
 888     888  888      888  888b     d'888   888               888   8 `88b.    8   888         888      888      8 8 8 8 8 8 8 8 8 8 8 8 8 8
 888ooooo888  888      888  8 Y88. .P  888   888oooo8          888   8   `88b.  8   888oooo8    888      888                                 
 888     888  888      888  8  `888'   888   888    "          888   8     `88b.8   888    "    888      888      8 8 8 8 8 8 8 8 8 8 8 8 8 8
 888     888  `88b    d88'  8    Y     888   888       o       888   8       `888   888         `88b    d88'      8 8 8 8 8 8 8 8 8 8 8 8 8 8
o888o   o888o  `Y8bood8P'  o8o        o888o o888ooooood8      o888o o8o        `8  o888o         `Y8bood8P'       8 8 8 8 8 8 8 8 8 8 8 8 8 8
*/

// CARGAS

Route::get('/allCargoThisWeek/{user}','App\Http\Controllers\cargaController@loadThisWeek'); 
Route::get('/allCargoNextWeek/{user}','App\Http\Controllers\cargaController@loadNextWeek'); 
Route::get('/allCargoLastWeek/{user}','App\Http\Controllers\cargaController@loadLastWeek'); 
Route::get('/allCargoFinished/{user}','App\Http\Controllers\cargaController@loadFinished');
Route::get('/carga/{id}/{user}','App\Http\Controllers\cargaController@show');
Route::get('/cargaDomain/{domain}', 'App\Http\Controllers\cargaController@showCargaDomain');

Route::get('/loadFinishedTransport/{transport}', 'App\Http\Controllers\cargaController@loadFinishedTransport');



// STATUS

Route::post('/statusCarga', 'App\Http\Controllers\statusController@updateStatusCarga');
Route::get('/cargasActivas', 'App\Http\Controllers\statusController@indexActive');
Route::get('/cargasActivasTransport/{transport}', 'App\Http\Controllers\statusController@indexTransportActive'); 


Route::get('/status','App\Http\Controllers\statusController@index');
Route::get('/ultimoStatus/{id}','App\Http\Controllers\statusController@showLast'); 
Route::get('/historialStatus/{cntr}','App\Http\Controllers\statusController@showHistory'); 

// INSTRUCTIVOS

Route::get('/instructivos/{userTraffic}','App\Http\Controllers\instructivosController@index');
Route::get('/instructivosdelete/{userTraffic}/{id}','App\Http\Controllers\instructivosController@destroy');

Route::get('/instructivosTransport/{transport}', 'App\Http\Controllers\instructivosController@indexTransport'); 



// ASIGNACIONES
Route::get('/truckAsign/{id}', 'App\Http\Controllers\TruckController@trailerAsign'); // Show for Transport no existe.

// SEGUROS

Route::post('/seguro', 'App\Http\Controllers\seguroController@store');

/* 
oooooooooooo ooo        ooooo       .o.       ooooo ooooo         .oooooo..o      8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8
`888'     `8 `88.       .888'      .888.      `888' `888'        d8P'    `Y8      8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8
 888          888b     d'888      .8"888.      888   888         Y88bo.           8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8
 888oooo8     8 Y88. .P  888     .8' `888.     888   888          `"Y8888o.                                                                  
 888    "     8  `888'   888    .88ooo8888.    888   888              `"Y88b      8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8
 888       o  8    Y     888   .8'     `888.   888   888       o oo     .d8P      8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8
o888ooooood8 o8o        o888o o88o     o8888o o888o o888ooooood8 8""88888P'       8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8
*/

Route::get('/imprimirEviarInstrucivo/{cntr}','App\Http\Controllers\crearpdfController@cargaPorMail');  // Llega Correo Ok
Route::get('/mailCargaNueva/{idCarga}/{user}','App\Http\Controllers\emailController@avisoNuevaCarga'); // Llega Correo Ok

/* Envio de Emails */

Route::get('/mailStatus/{cntr}/{empresa}/{booking}/{user}/{tipo}/{statusArchivoPath}','App\Http\Controllers\emailController@cambiaStatus');  // Llega Correo Ok
Route::get('/cargaAsignada/{id}','App\Http\Controllers\emailController@cargaAsignada');  // Llega Correo Ok

//REMPLAZAR  CON LA ASIGNACION DEL TRANSPORTE Y ENVIAR MAIL
Route::post('/transporteAsignado/{id}','App\Http\Controllers\TransportController@transporteAsignado'); 
Route::post('/asignarUnidadTransporte/{id}','App\Http\Controllers\TransportController@asignarUnidadTransporte'); 
Route::post('/confirmarUnidad/{id}','App\Http\Controllers\TransportController@confirmarUnidad'); 
// Route::post('/imprimir/create','App\Http\Controllers\crearpdfControllerPDF@store')mostrar todos
// Route::get('/imprimirIns','App\Http\Controllers\imprimirPDF@store'); //mostrar todos
// Route::put('/imprimir/{id}','App\Http\Controllers\imprimirPDF@update');//actualizar
// Route::delete('/imprimir','App\Http\Controllers\imprimirPDF@destroy'); // eliminar


/* 
oooooooooo.     .oooooo.     .oooooo.    .oooooo..o 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8
`888'   `Y8b   d8P'  `Y8b   d8P'  `Y8b  d8P'    `Y8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8
 888      888 888      888 888          Y88bo.      8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8
 888      888 888      888 888           `"Y8888o.                                                                                           
 888      888 888      888 888               `"Y88b 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8
 888     d88' `88b    d88' `88b    ooo  oo     .d8P 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8
o888bood8P'    `Y8bood8P'   `Y8bood8P'  8""88888P'  8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8
*/

Route::post('/docs/{booking}','App\Http\Controllers\DocumentController@store');
Route::post('/ingresoFormulario', 'App\Http\Controllers\cargaController@guardarFormulario');
Route::put('/carga/{id}', 'App\Http\Controllers\cargaController@update');
Route::get('/docsCntr/{booking}/{user}/{cntr}','App\Http\Controllers\DocumentController@indexCntr');
Route::get('/docsDel','App\Http\Controllers\DocumentController@destroy'); 


//////// USUARIOS EXTERNOS ////////
                                                                                                            //
Route::get('/docsAtaReed/{booking}/{user}','App\Http\Controllers\DocumentController@index');                //
Route::post('/docsAta/{booking}','App\Http\Controllers\DocumentController@storeAta');                       //
Route::post('/carga','App\Http\Controllers\LoadController@store');                                          //
                                                                                                            //

/* Impresion de PDF [RailDocs] */

Route::get('imprimirCarga/{cntr_number}', 'App\Http\Controllers\crearpdfController@carga'); // No usa Funcion MAIL
Route::get('/verCarga/{cntr_number}', 'App\Http\Controllers\verpdfController@carga');
Route::get('/imprimirVacio/{id_cntr}', 'App\Http\Controllers\crearpdfController@vacio');  // No usa Funcion MAIL

/* 
ooooo     ooo oooooooooo.  ooooo   .oooooo.      ooooo     ooo ooooo ooooooooooooo oooooo   oooo 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
`888'     `8' `888'   `Y8b `888'  d8P'  `Y8b     `888'     `8' `888' 8'   888   `8  `888.   .8'  8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
 888       8   888     888  888  888      888     888       8   888       888        `888. .8'   8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
 888       8   888oooo888'  888  888      888     888       8   888       888         `888.8'                                                
 888       8   888    `88b  888  888      888     888       8   888       888          `888'     8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
 `88.    .8'   888    .88P  888  `88b    d88b     `88.    .8'   888       888           888      8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
   `YbodP'    o888bood8P'  o888o  `Y8bood8P'Ybd'    `YbodP'    o888o     o888o         o888o     8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
 */

Route::get('/lugarDeCarga/{patente}','App\Http\Controllers\CustomerLoadPlaceController@coordenadas');
Route::get('/accionLugarDeCarga/{idTrip}','App\Http\Controllers\CustomerLoadPlaceController@accionLugarDeCarga'); // LLEGO OK EMAIL
Route::get('/accionLugarAduana/{idTrip}','App\Http\Controllers\CustomerLoadPlaceController@accionLugarAduana');// LLEGO OK EMAIL
Route::get('/accionLugarDescarga/{idTrip}','App\Http\Controllers\CustomerLoadPlaceController@accionLugarDescarga');// LLEGO OK EMAIL
Route::get('/accionFueraLugarDeCarga/{idTrip}','App\Http\Controllers\CustomerLoadPlaceController@accionFueraLugarDeCarga'); // LLEGO OK EMAIL
Route::get('/accionFueraLugarAduana/{idTrip}','App\Http\Controllers\CustomerLoadPlaceController@accionFueraLugarAduana');// LLEGO OK EMAIL
Route::get('/servicioSatelital','App\Http\Controllers\ServiceSatelital@serviceSatelital');
Route::get('/pruebaSatelital','App\Http\Controllers\ServiceSatelital@servicePrueba');
Route::get('/flota','App\Http\Controllers\ServiceSatelital@flota');
Route::get('/flotaId/{id}', 'App\Http\Controllers\ServiceSatelital@flotaID');
Route::get('/flotaTransport/{transport}', 'App\Http\Controllers\ServiceSatelital@flotaTransport');



Route::get('/dominioAker/{dominio}', 'App\Http\Controllers\ServiceSatelital@issetDominio');
Route::get('/revierDomain', 'App\Http\Controllers\ServiceSatelital@reviewDomains');

Route::get('/revisarCoordenadas', 'App\Http\Controllers\ServiceSatelital@revisarCoordenadas');
Route::get('/itinerarios/{id}', 'App\Http\Controllers\ItinerarioController@show');


/* 
oooooo     oooo       .o.       ooooo        ooooo oooooooooo.         .o.         .oooooo.   ooooo   .oooooo.   ooooo      ooo 8 8 8 8 8 8 8
 `888.     .8'       .888.      `888'        `888' `888'   `Y8b       .888.       d8P'  `Y8b  `888'  d8P'  `Y8b  `888b.     `8' 8 8 8 8 8 8 8
  `888.   .8'       .8"888.      888          888   888      888     .8"888.     888           888  888      888  8 `88b.    8  8 8 8 8 8 8 8
   `888. .8'       .8' `888.     888          888   888      888    .8' `888.    888           888  888      888  8   `88b.  8               
    `888.8'       .88ooo8888.    888          888   888      888   .88ooo8888.   888           888  888      888  8     `88b.8  8 8 8 8 8 8 8
     `888'       .8'     `888.   888       o  888   888     d88'  .8'     `888.  `88b    ooo   888  `88b    d88'  8       `888  8 8 8 8 8 8 8
      `8'       o88o     o8888o o888ooooood8 o888o o888bood8P'   o88o     o8888o  `Y8bood8P'  o888o  `Y8bood8P'  o8o        `8  8 8 8 8 8 8 8
 */

Route::post('issetBooking', 'App\Http\Controllers\cargaController@issetBooking');
Route::get('issetTransport/{cuit}', 'App\Http\Controllers\TransportController@issetTrasnsport');
Route::get('issetTransportRazon/{razon_social}', 'App\Http\Controllers\TransportController@issetTransportRazon');
Route::get('issetTruck/{domain}', 'App\Http\Controllers\TruckController@issetTruck');
Route::post('issetTrader', 'App\Http\Controllers\cargaController@issetTrader');
Route::post('issetLoadPlace', 'App\Http\Controllers\CustomerLoadPlaceController@issetLugarDeCarga');
Route::post('issetUnloadPlace', 'App\Http\Controllers\CustomerLoadPlaceController@issetLugarDeDescarga');
Route::get('issetCntr/{cntr_number}', 'App\Http\Controllers\cntrController@issetCntr');
Route::get('issetAsignacion/{dominio}', 'App\Http\Controllers\cntrController@issetAsign');
Route::get('issetDriver', 'App\Http\Controllers\DriverController@issetDriver');




/* 
ooo        ooooo       .o.       ooooo      ooo           .o.         .oooooo.    oooooooooooo ooooo      ooo 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
`88.       .888'      .888.      `888b.     `8'          .888.       d8P'  `Y8b   `888'     `8 `888b.     `8' 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
 888b     d'888      .8"888.      8 `88b.    8          .8"888.     888            888          8 `88b.    8  8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
 8 Y88. .P  888     .8' `888.     8   `88b.  8         .8' `888.    888            888oooo8     8   `88b.  8                                  
 8  `888'   888    .88ooo8888.    8     `88b.8        .88ooo8888.   888     ooooo  888    "     8     `88b.8  8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
 8    Y     888   .8'     `888.   8       `888  .o.  .8'     `888.  `88.    .88'   888       o  8       `888  8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
o8o        o888o o88o     o8888o o8o        `8  Y8P o88o     o8888o  `Y8bood8P'   o888ooooood8 o8o        `8  8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 

*/

//CARGA
Route::delete('/carga/{id}', 'App\Http\Controllers\cargaController@destroy')->name('cargas.destroy');

// USER

Route::get('/user/{user}', 'App\Http\Controllers\UserController@show');

// CNTR

Route::resource('/cntr',cntrController::class);
Route::get('/datosConfirmar/{cntrId}', 'App\Http\Controllers\cntrController@datosConfirmar');

// FLETERO CONTROLLER 

Route::resource('fleteros', FleteroController::class);
Route::get('/fleteroAsociados/{id}', [FleteroController::class, 'getFleteroDetails']);

// TRUCK CONTROLLLER 
Route::post('/truck', 'App\Http\Controllers\TruckController@store'); // C
Route::get('/trucks/{customer}', 'App\Http\Controllers\TruckController@index'); // R ALL
Route::get('/trucksTransport/{transport}', 'App\Http\Controllers\TruckController@indexTransport'); // R ALL

Route::get('/truck/{truck}', 'App\Http\Controllers\TruckController@show'); // R ONE
Route::post('/truck/{truck}', 'App\Http\Controllers\TruckController@update'); // U 
Route::delete('/truck/{truck}', 'App\Http\Controllers\TruckController@destroy'); // D 
Route::get('/truckTransport/{truck}', 'App\Http\Controllers\TruckController@showTransport'); // Show For Transport
Route::get('/trucks', 'App\Http\Controllers\TruckController@indexTotal');
// TRAILER CONTROLLLER 
Route::post('/trailer', 'App\Http\Controllers\TrailerController@store'); // C
Route::get('/trailer/{customer}', 'App\Http\Controllers\TrailerController@index'); // R ALL
Route::get('/trailerTransport/{transport}', 'App\Http\Controllers\TrailerController@indexTransport'); // R ALL
Route::get('/trailerTraffic', 'App\Http\Controllers\TrailerController@indexTraffic');
Route::get('/trailer/{trailer}', 'App\Http\Controllers\TrailerController@show'); // R ONE
Route::post('/trailer/{trailer}', 'App\Http\Controllers\TrailerController@update'); // U
Route::delete('/trailer/{trailer}', 'App\Http\Controllers\TrailerController@destroy'); // D
//Route::get('/trailerTransport/{transport_id}', 'App\Http\Controllers\TrailerController@showTrailer'); // Show for Transport

//Ata
Route::get('/atas','App\Http\Controllers\AtaController@index'); //Busca todos los Agente de transporte
Route::get('/ata/{id}','App\Http\Controllers\AtaController@show'); //Busca un Agente de transporte
Route::post('/ata','App\Http\Controllers\AtaController@store'); //Crea un nuevo Agente de transporte
Route::post('/ata/{id}','App\Http\Controllers\AtaController@update'); //Actualiza los datos de un Agente de transporte
Route::delete('/ata/{id}','App\Http\Controllers\AtaController@destroy'); //Elimina un Agente de transporte

// DRIVER CONTROLLLER trailerAsign

Route::get('/drivers/{transport_id}','App\Http\Controllers\DriverController@showDriver'); 
Route::get('/drivers','App\Http\Controllers\DriverController@index');
Route::get('/driversTransport/{idTransport}', 'App\Http\Controllers\DriverController@indexTransport'); 

Route::get('/driver/{id}','App\Http\Controllers\DriverController@show'); 
Route::post('/driver','App\Http\Controllers\DriverController@store'); 
Route::post('/driverStatus/{id}','App\Http\Controllers\DriverController@status'); 
Route::post('/driver/{id}','App\Http\Controllers\DriverController@update'); 
Route::delete('/driver/{id}','App\Http\Controllers\DriverController@destroy'); 

//Transporte
Route::get('/transporteCustomer/{id}','App\Http\Controllers\TransportController@indexTransporteCustomer'); //Busca todos los transportes del customerId
Route::get('/transportes','App\Http\Controllers\TransportController@index'); 
Route::get('/transporte/{id}','App\Http\Controllers\TransportController@show'); 
Route::get('/transporteRazonSocial/{razonSocial}','App\Http\Controllers\TransportController@showRazonSocial'); 
Route::post('/transporte','App\Http\Controllers\TransportController@store'); 
Route::post('/transporte/{id}','App\Http\Controllers\TransportController@update'); 
Route::delete('/transporte/{id}','App\Http\Controllers\TransportController@destroy'); 
Route::get('/transportesUsuario/{id}','App\Http\Controllers\TransportController@transportesUsuario'); 
Route::post('/transportesAsignEditar/{id}','App\Http\Controllers\TransportController@transportesAsignEditar'); 
//Agencia
Route::get('/agencias','App\Http\Controllers\AgencyController@index'); //Busca todas las agencias
Route::get('/agencia/{id}','App\Http\Controllers\AgencyController@show'); //Busca una sola agencia
Route::post('/agencia','App\Http\Controllers\AgencyController@store'); //Crea una nueva Agencia
Route::post('/agencia/{id}','App\Http\Controllers\AgencyController@update'); //Actualiza los datos de una Agencia
Route::delete('/agencia/{id}','App\Http\Controllers\AgencyController@destroy'); //Elimina una Agencia
Route::post('/agencias/{id}', 'App\Http\Controllers\AgencyController@update');

//Empresas=Cliente=Company
Route::get('/empresas','App\Http\Controllers\CompanyController@index'); //Busca todas las empresas
Route::get('/empresa/{id}','App\Http\Controllers\CompanyController@show'); //Busca una empresa por el id
Route::post('/empresa','App\Http\Controllers\CompanyController@store'); //Crea un cliente
Route::post('/empresa/{id}','App\Http\Controllers\CompanyController@update');//Actualizar datos de un cliente
Route::delete('/empresa/{id}','App\Http\Controllers\CompanyController@destroy');//Eliminar un cliente

//Customer Cnee
Route::get('/customersCnee','App\Http\Controllers\CustomerCneeController@index'); //Busca todos los Customer Cnee
Route::get('/customerCneeCompany/{company}','App\Http\Controllers\CustomerCneeController@indexCompany'); //Busca todos los Customer Cnee de una compania
Route::get('/customerCnee/{id}','App\Http\Controllers\CustomerCneeController@show'); //Busca un Customer Cnee
Route::post('/customerCnee','App\Http\Controllers\CustomerCneeController@store'); //Crea un nuevo Customer Cnee
Route::post('/customerCnee/{id}','App\Http\Controllers\CustomerCneeController@update'); //Actualiza los datos de un Customer Cnee
Route::delete('/customerCnee/{id}','App\Http\Controllers\CustomerCneeController@destroy'); //Elimina un Customer Cnee

//Deposito de Retiro
Route::get('/depositoRetiros','App\Http\Controllers\DepositoRetiroController@index'); //Busca todos los depositos de retiro
Route::get('/depositoRetiro/{id}','App\Http\Controllers\DepositoRetiroController@show'); //Busca un deposito de retiro
Route::post('/depositoRetiro','App\Http\Controllers\DepositoRetiroController@store');  //Crea un nuevo deposito de retiro
Route::post('/depositoRetiro/{id}','App\Http\Controllers\DepositoRetiroController@update'); //Actualiza los datos de un deposito de retiro
Route::delete('/depositoRetiro/{id}','App\Http\Controllers\DepositoRetiroController@destroy'); //Elimina un deposito de retiro

//Lugar de carga
Route::get('/lugarCargas','App\Http\Controllers\CustomerLoadPlaceController@index'); //Busca todos los depositos de retiro
Route::get('/lugarCarga/{id}','App\Http\Controllers\CustomerLoadPlaceController@show'); //Busca un deposito de retiro
Route::post('/lugarCarga','App\Http\Controllers\CustomerLoadPlaceController@store');  //Crea un nuevo deposito de retiro
Route::post('/lugarCarga/{id}','App\Http\Controllers\CustomerLoadPlaceController@update'); //Actualiza los datos de un deposito de retiro
Route::delete('/lugarCarga/{id}','App\Http\Controllers\CustomerLoadPlaceController@destroy'); //Elimina un deposito de retiro

//Lugar de Descarga
Route::get('/lugarDescargas','App\Http\Controllers\CustomerUnloadPlaceController@index'); //Busca todos los depositos de retiro
Route::get('/lugarDescarga/{id}','App\Http\Controllers\CustomerUnloadPlaceController@show'); //Busca un deposito de retiro
Route::post('/lugarDescarga','App\Http\Controllers\CustomerUnloadPlaceController@store');  //Crea un nuevo deposito de retiro
Route::post('/lugarDescarga/{id}','App\Http\Controllers\CustomerUnloadPlaceController@update'); //Actualiza los datos de un deposito de retiro
Route::delete('/lugarDescarga/{id}','App\Http\Controllers\CustomerUnloadPlaceController@destroy'); //Elimina un deposito de retiro

//Type CNTR
Route::get('/tiposCntr','App\Http\Controllers\CntrTypeController@index'); //Busca todos los tipos de cntr
Route::get('/tipoCntr/{id}','App\Http\Controllers\CntrTypeController@show'); //Busca un tipo de cntr
Route::post('/tipoCntr','App\Http\Controllers\CntrTypeController@store'); //Crea un nuevo tipo de cntr
Route::post('/tipoCntr/{id}','App\Http\Controllers\CntrTypeController@update'); //Actualiza los datos de un tipo de cntr
Route::delete('/tipoCntr/{id}','App\Http\Controllers\CntrTypeController@destroy'); //Elimina un tipo de cntr

//Modos de Pago
Route::get('/modoPagos','App\Http\Controllers\PayModeController@index'); //Busca todos los modos de pago
Route::get('/modoPago/{id}','App\Http\Controllers\PayModeController@show'); //Busca un modo de pago
Route::post('/modoPago','App\Http\Controllers\PayModeController@store'); //Crea un nuevo modo de pago
Route::post('/modoPago/{id}','App\Http\Controllers\PayModeController@update'); //Actualiza los datos de un modo de pago
Route::delete('/modoPago/{id}','App\Http\Controllers\PayModeController@destroy'); //Elimina un modo de pago

//Plazo de Pago
Route::get('/plazoPagos','App\Http\Controllers\PayTimeController@index'); //Busca todos los plazos de pago
Route::get('/plazoPago/{id}','App\Http\Controllers\PayTimeController@show'); //Busca un plazo de pago
Route::post('/plazoPago','App\Http\Controllers\PayTimeController@store'); //Crea un nuevo plazo de pago
Route::post('/plazoPago/{id}','App\Http\Controllers\PayTimeController@update'); //Actualiza los datos de un plazo de pago
Route::delete('/plazoPago/{id}','App\Http\Controllers\PayTimeController@destroy'); //Elimina un plazo de pago

//Customer agent
Route::get('/customerAgents','App\Http\Controllers\CustomerAgentController@index'); //Busca todos los Customer Shipper
Route::get('/customerAgentEmpresa/{empresa}','App\Http\Controllers\CustomerAgentController@indexCompany'); //Busca todos los Customer Shipper de una compania
Route::get('/customerAgent/{id}','App\Http\Controllers\CustomerAgentController@show'); //Busca un Customer Shipper de una compania
Route::post('/customerAgent','App\Http\Controllers\CustomerAgentController@store'); //Crea un nuevo Customer Shipper
Route::post('/customerAgent/{id}','App\Http\Controllers\CustomerAgentController@update'); //Actualiza los datos de un Customer Shipper
Route::delete('/customerAgent/{id}','App\Http\Controllers\CustomerAgentController@destroy'); //Elimina un Customer Shipper

// Aduanas 

Route::get('/aduanas', 'App\Http\Controllers\AduanasController@index'); // Ver todas las Aduanas
Route::get('/aduana/{id}', 'App\Http\Controllers\AduanasController@show'); //Busca una Aduana
Route::post('/aduana', 'App\Http\Controllers\AduanasController@store'); //Crea un nueva Aduana
Route::post('/aduana/{id}', 'App\Http\Controllers\AduanasController@update'); //Actualiza los datos de una Aduana
Route::delete('/aduana/{id}', 'App\Http\Controllers\AduanasController@destroy'); //Elimina una Aduana

// Commodities 

Route::get('/commodities', 'App\Http\Controllers\commoditiesController@index'); // Ver todas las Commodities
Route::get('/commodity/{id}', 'App\Http\Controllers\commoditiesController@show'); //Busca una Commodity
Route::post('/commodity', 'App\Http\Controllers\commoditiesController@store'); //Crea un nueva Commodity
Route::post('/commodity/{id}', 'App\Http\Controllers\commoditiesController@update'); //Actualiza los datos de una Commodity
Route::delete('/commodity/{id}', 'App\Http\Controllers\commoditiesController@destroy'); //Elimina una Commodity


//Customer ntfy
Route::get('/customersNtfy','App\Http\Controllers\CustomerNtfyController@index'); //Busca todos los Customer Ntfy
Route::get('/customerNtfyCompany/{company}','App\Http\Controllers\CustomerNtfyController@indexCompany'); //Busca todos los Customer Ntfy de una compania
Route::get('/customerNtfy/{id}','App\Http\Controllers\CustomerNtfyController@show'); //Busca un Customer Ntfy
Route::post('/customerNtfy','App\Http\Controllers\CustomerNtfyController@store'); //Crea un nuevo Customer Ntfy
Route::post('/customerNtfy/{id}','App\Http\Controllers\CustomerNtfyController@update'); //Actualiza los datos de un Customer Ntfy
Route::delete('/customerNtfy/{id}','App\Http\Controllers\CustomerNtfyController@destroy'); //Elimina un Customer Ntfy

//Customer shipper
Route::get('/customersShipper','App\Http\Controllers\CustomerShipperController@index'); //Busca todos los Customer Shipper
Route::get('/customerShipperCompany/{company}','App\Http\Controllers\CustomerShipperController@indexCompany'); //Busca todos los Customer Shipper de una compania
Route::get('/customerShipper/{id}','App\Http\Controllers\CustomerShipperController@show'); //Busca un Customer Shipper de una compania
Route::post('/customerShipper','App\Http\Controllers\CustomerShipperController@store'); //Crea un nuevo Customer Shipper
Route::post('/customerShipper/{id}','App\Http\Controllers\CustomerShipperController@update'); //Actualiza los datos de un Customer Shipper
Route::delete('/customerShipper/{id}','App\Http\Controllers\CustomerShipperController@destroy'); //Elimina un Customer Shipper

//Customer trader
Route::get('/customers','App\Http\Controllers\CustomerController@index'); //Busca todos los Customer trader
Route::get('/customerCompany/{company}','App\Http\Controllers\CustomerController@indexCompany'); //Busca todos los Customer trader de una compania
Route::get('/customer/{id}','App\Http\Controllers\CustomerController@show'); //Busca un Customer trader de una compania
Route::get('/customer/{name}','App\Http\Controllers\CustomerController@showName'); //Busca un Customer trader de una compania
Route::post('/customer','App\Http\Controllers\CustomerController@store'); //Crea un nuevo Customer trader
Route::post('/customer/{id}','App\Http\Controllers\CustomerController@update'); //Actualiza los datos de un Customer trader
Route::delete('/customer/{id}','App\Http\Controllers\CustomerController@destroy'); //Elimina un Customer trader

//Customer Final Point

Route::get('/finalPoints','App\Http\Controllers\finalPointController@index'); //Busca todos los final Points
Route::get('/finalPoints/{id}','App\Http\Controllers\finalPointController@show'); //Busca un final Points 
Route::post('/finalPoints','App\Http\Controllers\finalPointController@store'); //Crea un nuevo final Points
Route::post('/finalPoints/{id}','App\Http\Controllers\finalPointController@update'); //Actualiza los datos de un final Points
Route::delete('/finalPoints/{id}','App\Http\Controllers\finalPointController@destroy'); //Elimina un final Points

Route::get('razonSocialTransport/{transport}', 'App\Http\Controllers\RazonSocialController@indexTransport');
Route::get('razonesSociales', 'App\Http\Controllers\RazonSocialController@index');
Route::get('razonSocial/{razonSocial}', 'App\Http\Controllers\RazonSocialController@show');
Route::post('razonSocialTransport', 'App\Http\Controllers\RazonSocialController@store');
Route::post('razonSocialTransport/{transport}', 'App\Http\Controllers\RazonSocialController@update');
Route::delete('razonSocialTransport/{transport}', 'App\Http\Controllers\RazonSocialController@destroy');





/*                                                                
oooooooooooo ooooooo  ooooo   .oooooo.   oooooooooooo ooooo             8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
`888'     `8  `8888    d8'   d8P'  `Y8b  `888'     `8 `888'             8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
 888            Y888..8P    888           888          888              8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
 888oooo8        `8888'     888           888oooo8     888                                                                              
 888    "       .8PY888.    888           888    "     888              8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
 888       o   d8'  `888b   `88b    ooo   888       o  888       o      8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
o888ooooood8 o888o  o88888o  `Y8bood8P'  o888ooooood8 o888ooooood8      8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 8 
*/

// Reportes de Cargas por fechas

Route::get('/excelCargasThisWeek', 'App\Http\Controllers\excelController@thisWeek');
Route::get('/excelCargasUncoming', 'App\Http\Controllers\excelController@uncoming');
Route::get('/excelCargasPast', 'App\Http\Controllers\excelController@past');

// Reportes por Status
Route::get('/excelNoAssigned', 'App\Http\Controllers\excelController@noAssigned');
Route::get('/excelOnBoard', 'App\Http\Controllers\excelController@onBoard');
Route::get('/excelAnyProblem', 'App\Http\Controllers\excelController@anyProblem');
Route::get('/excelFinish', 'App\Http\Controllers\excelController@loadFinish');
Route::get('/excelGoingToLoad', 'App\Http\Controllers\excelController@goingToLoad');
Route::get('/excelLoading', 'App\Http\Controllers\excelController@loading');
Route::get('/excelOnCustomPlace', 'App\Http\Controllers\excelController@onCustomPlace');
Route::get('/excelOnWay', 'App\Http\Controllers\excelController@onWay');
Route::get('/excelAllLoads', 'App\Http\Controllers\excelController@allLoads');
Route::get('/excelStacking', 'App\Http\Controllers\excelController@stacking');
Route::get('/excelAssigned', 'App\Http\Controllers\excelController@assigned');
Route::get('/excelAssigned', 'App\Http\Controllers\excelController@assigned');

// Reportes de DB Select

Route::get('/excelAgencies', 'App\Http\Controllers\excelController@agencies');
Route::get('/excelAtas', 'App\Http\Controllers\excelController@atas');
Route::get('/excelPaymentMode', 'App\Http\Controllers\excelController@paymentMode');
Route::get('/excelUsers', 'App\Http\Controllers\excelController@users');
Route::get('/excelCustomAgents', 'App\Http\Controllers\excelController@customAgents');
Route::get('/excelCompanies', 'App\Http\Controllers\excelController@companies');
Route::get('/excelWarehouseContainer', 'App\Http\Controllers\excelController@warehouseContainer');
Route::get('/excelContainerTypes', 'App\Http\Controllers\excelController@containerTypes');
Route::get('/excelMisTrader', 'App\Http\Controllers\excelController@customer');
Route::get('/excelMisShipper', 'App\Http\Controllers\excelController@customerShipper');
Route::get('/excelMisConsignee', 'App\Http\Controllers\excelController@customerConsignee');
Route::get('/excelLoadPlace', 'App\Http\Controllers\excelController@LoadPlace');
Route::get('/excelUnloadPlace', 'App\Http\Controllers\excelController@UnloadPlace');

// Reportes Flotas
Route::get('/excelDriversFree', 'App\Http\Controllers\excelController@driversFree');
Route::get('/excelDriversBusy', 'App\Http\Controllers\excelController@driversBusy');
Route::get('/excelDrivers', 'App\Http\Controllers\excelController@drivers');
Route::get('/excelTrucks', 'App\Http\Controllers\excelController@trucks');
Route::get('/excelTrailers', 'App\Http\Controllers\excelController@trailers');
Route::get('/excelTransports', 'App\Http\Controllers\excelController@transports');



// - GESTION AKER ---------------------

Route::get('transportesAker','App\Http\Controllers\AkerTransportController@index');
Route::get('truckAker', 'App\Http\Controllers\AkerTruckController@index');
