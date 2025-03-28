<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $companies = Company::all();
            return response()->json([
                'data' => $companies,
                'success' => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error interno del servidor',
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $company = new Company();
        $company->razon_social = $request['razon_social'];
        $company->CUIT = $request['CUIT'];
        $company->IIBB = $request['IIBB'];
        $company->mail_admin = $request['mail_admin'];
        $company->mail_logistic = $request['mail_logistic'];
        $company->name_admin = $request['name_admin'];
        $company->name_logistic = $request['name_logistic'];
        $company->cel_admin = $request['cel_admin'];
        $company->cel_logistic = $request['cel_logistic'];
        $company->direccion = $request['direccion'];
        $company->user = $request['user'];
        $company->empresa = $request['empresa'];
        $company->pais = $request['pais'];
        $company->provincia = $request['provincia'];
        $company->save();

        return $company;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $company = Company::findOrFail($id);
        $company->razon_social = $request['razon_social'];
        $company->CUIT = $request['CUIT'];
        $company->IIBB = $request['IIBB'];
        $company->mail_admin = $request['mail_admin'];
        $company->mail_logistic = $request['mail_logistic'];
        $company->name_admin = $request['name_admin'];
        $company->name_logistic = $request['name_logistic'];
        $company->cel_admin = $request['cel_admin'];
        $company->cel_logistic = $request['cel_logistic'];
        $company->direccion = $request['direccion'];
        $company->user = $request['user'];
        $company->empresa = $request['empresa'];
        $company->pais = $request['pais'];
        $company->provincia = $request['provincia'];
        $company->save();

        return $company;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy( $id)
    {
        Company::destroy($id);

        $existe = Company::find($id);
        if($existe){
            return 'No se elimino la Company';
        }else{
            return 'Se elimino la Company';
        };
    }
}
