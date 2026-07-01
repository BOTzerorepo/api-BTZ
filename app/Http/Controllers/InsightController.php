<?php

namespace App\Http\Controllers;

use App\Models\Insight;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class InsightController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        try {
            $user = auth('api')->user();
            $query = Insight::with('cliente');

            if (!$user->hasRole('AdminComercial')) {
                $query->where('empresa', $user->empresa);
            }

            if ($request->filled('cliente_id')) {
                $query->where('cliente_id', $request->cliente_id);
            }
            if ($request->filled('tipo')) {
                $query->where('tipo', $request->tipo);
            }

            return $this->success($query->orderBy('created_at', 'desc')->get());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'cliente_id'       => 'nullable|exists:clientes_comercial,id',
                'tipo'             => 'required|in:Dolor,Oportunidad,Feedback,Funcionalidad',
                'descripcion'      => 'required|string',
                'impacto'          => 'required|in:Alto,Medio,Bajo',
                'repetido_por'     => 'nullable|integer|min:1',
                'relacionado_con'  => 'nullable|string|max:255',
            ]);

            $validated['empresa'] = auth('api')->user()->empresa;
            $insight = Insight::create($validated);

            return $this->created($insight->load('cliente'), 'Insight creado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $insight = Insight::findOrFail($id);

            $validated = $request->validate([
                'cliente_id'       => 'nullable|exists:clientes_comercial,id',
                'tipo'             => 'sometimes|in:Dolor,Oportunidad,Feedback,Funcionalidad',
                'descripcion'      => 'sometimes|string',
                'impacto'          => 'sometimes|in:Alto,Medio,Bajo',
                'repetido_por'     => 'nullable|integer|min:1',
                'relacionado_con'  => 'nullable|string|max:255',
            ]);

            $insight->update($validated);

            return $this->success($insight->load('cliente'), 'Insight actualizado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            Insight::findOrFail($id)->delete();
            return $this->success(null, 'Insight eliminado.');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }
}
