<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
        try {
            // Validación
            $validator = Validator::make($request->all(), [
                'razon_social' => 'required|string|max:255',
                'CUIT' => 'required|string|max:20',
                'IIBB' => 'nullable|string|max:50',
                'mail_admin' => 'nullable|email|max:255',
                'mail_logistic' => 'nullable|email|max:255',
                'name_admin' => 'nullable|string|max:255',
                'name_logistic' => 'nullable|string|max:255',
                'cel_admin' => 'nullable|string|max:20',
                'cel_logistic' => 'nullable|string|max:20',
                'direccion' => 'nullable|string|max:255',
                'user' => 'required|string|max:255',
                'empresa' => 'required|string|max:255',
                'pais' => 'nullable|string|max:100',
                'provincia' => 'nullable|string|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Guardar en base de datos
            $company = Company::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Empresa creada exitosamente.',
                'data' => $company
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la empresa.',
                'error' => $e->getMessage()
            ], 500);
        }
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
    public function destroy($id)
    {
        try {
            $deleted = Company::destroy($id);

            if ($deleted) {
                return response()->json([
                    'message' => 'La compañía fue eliminada correctamente.'
                ], 200);
            } else {
                return response()->json([
                    'message' => 'No se encontró la compañía o no se pudo eliminar.'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ocurrió un error al intentar eliminar la compañía.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
