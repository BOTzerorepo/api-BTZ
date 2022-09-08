<?php

namespace App\Http\Controllers;

use App\Exports\agencies;
use App\Exports\allLoads;
use App\Exports\assigned;
use App\Exports\atas;
use App\Exports\companies;
use App\Exports\containerTypes;
use App\Exports\customAgents;
use App\Exports\drivers;
use App\Exports\driversBusy;
use App\Exports\driversFree;
use App\Exports\goingToLoad;
use App\Exports\lastWeek;
use App\Exports\loadAnyProblem;
use App\Exports\loadAsset;
use App\Exports\loadFinish;
use App\Exports\loading;
use App\Exports\loadNoAssigned;
use App\Exports\loadOnBoard;
use App\Exports\onCustomPlace;
use App\Exports\onWay;
use App\Exports\paymentMode;
use App\Exports\stacking;
use App\Exports\thisWeek;
use App\Exports\trailers;
use App\Exports\transports;
use App\Exports\trucks;
use App\Exports\uncoming;
use App\Exports\users;
use App\Exports\warehouseContainer;
use App\Models\truck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class excelController extends Controller
{
    function thisWeek(){

        return Excel::download(new thisWeek, 'cargasSemanaActual.xlsx');
        
    }
    function past(){

        return Excel::download(new lastWeek, 'cargasPasadas.xlsx');

    }
    function uncoming(){

        return Excel::download(new uncoming, 'cargasFuturas.xlsx');

    }
    function noAssigned(){

        return Excel::download(new loadNoAssigned, 'CargasSinAsignar.xls');
        
    }
    function onBoard(){
        
        return Excel::download(new loadOnBoard, 'CargasOnBoard.xls');
        
    }
    function anyProblem(){
        
        return Excel::download(new loadAnyProblem, 'CargasAnyProblem.xls');
        
    }
    function loadAsset(){
        
        return Excel::download(new loadAsset, 'CargasAsset.xls');
        
    }
    function loadFinish(){
        
        return Excel::download(new loadFinish, 'CargasTerminas.xls');
        
    }
    function agencies(){
        
        return Excel::download(new agencies, 'Agencias.xls');
        
    }
    function atas(){
        
        return Excel::download(new atas, 'Atas.xls');
        
    }
    function driversFree(){
        
        return Excel::download(new driversFree, 'ChoferesLibres.xls');
        
    }
    function driversBusy(){
        
        return Excel::download(new driversBusy, 'ChoferesOcupados.xls');
        
    }
    function drivers(){
        
        return Excel::download(new drivers, 'Choferes.xls');
        
    }
    function trucks(){
        
        return Excel::download(new trucks, 'Tractores.xls');
        
    }
    function trailers(){
        
        return Excel::download(new trailers, 'Semiremolques.xls');
        
    }
    function customAgents(){
        
        return Excel::download(new customAgents, 'Despachantes.xls');
        
    }
    function transports(){
        
        return Excel::download(new transports, 'Transportes.xls');
        
    }
    function paymentMode(){
        
        return Excel::download(new paymentMode, 'Metodos de Pago.xls');
        
    }
    function users(){
        
        return Excel::download(new users, 'Usuario.xls');
        
    }
    function goningToLoad(){
        
        return Excel::download(new goingToLoad, 'CargasYendoACargar.xls');
        
    }
    function loading(){
        
        return Excel::download(new loading, 'CargasCargando.xls');
        
    }
    function onCustomPlace(){
        
        return Excel::download(new onCustomPlace, 'CargasEnAduana.xls');
        
    }
    function onWay(){
        
        return Excel::download(new onWay, 'CargasEnViaje.xls');
        
    }
    function allLoads(){
        
        return Excel::download(new allLoads, 'TodasLasCargas.xls');
        
    }
    function stacking(){
        
        return Excel::download(new stacking, 'CargaEnStaking.xls');
        
    }
    function assigned(){
        
        return Excel::download(new assigned, 'CargaAsignadas.xls');
        
    }
    function companies(){
        
        return Excel::download(new companies, 'Empresas.xls');
        
    }
    function warehouseContainer(){
        
        return Excel::download(new warehouseContainer, 'DespositosDeContenedores.xls');
        
    }
    function containerTypes(){
        
        return Excel::download(new containerTypes, 'TiposDeContenedores.xls');
        
    }
    
    
}
