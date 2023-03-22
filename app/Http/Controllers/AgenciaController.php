<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\agencia;



class AgenciaController extends Controller
{
    
public function update(Request $request, $id){

   /*  $agencia = agencia::find($id);
    $agencia->description = $request['description'];
    $agencia->save();


    $query = "UPDATE `agencias` SET `description` = '$description' ,`razon_social`= '$razon_social' ,`tax_id`= '$tax_id',`puerto`= '$puerto',`contact_name`= '$contact_name',`contact_phone`= '$contact_phone',`contact_mail`= '$contact_mail',`user`='$user',`empresa`='$company',`observation_gral`='$observation_gral' WHERE id = '$id'";
    $result = mysqli_query($conn, $query); */

    return $request['description'];
}
}
