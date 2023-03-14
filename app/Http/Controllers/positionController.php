<?php

namespace App\Http\Controllers;

use App\Models\position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class positionController extends Controller
{
  function position($domain){

    $historico = DB::table('positions')->where('dominio','=',$domain)->get();

    return $historico;
  }
    
}
