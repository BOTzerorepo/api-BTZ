<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ActionService;

class ActionController extends Controller
{
    protected $actionService;

    public function __construct(ActionService $actionService)
    {
        $this->actionService = $actionService;
    }

    public function executeAction(Request $request)
    {
        // Validar y obtener el JSON de la solicitud
        $jsonData = $request->validate([
            'json_data' => 'required|json',
        ])['json_data'];

        // Decodificar el JSON
        $data = json_decode($jsonData, true);

        // Llamar al servicio para ejecutar la acción
        $result = $this->actionService->executeActionFromJson($data);

        // Retornar una respuesta adecuada (puede ser JSON)
        return response()->json($result);
    }
}