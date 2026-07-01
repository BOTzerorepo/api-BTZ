<?php

namespace App\Http\Controllers;

use App\Models\AccionDiagnostico;
use App\Models\Diagnostico;
use App\Models\HallazgoDiagnostico;
use App\Models\OportunidadDiagnostico;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class DiagnosticoController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        try {
            $user = auth('api')->user();
            $query = Diagnostico::with('cliente');

            if (!$user->hasRole('AdminComercial')) {
                $query->whereHas('cliente', function ($q) use ($user) {
                    $q->where('empresa', $user->empresa);
                });
            }

            if ($request->filled('cliente_id')) {
                $query->where('cliente_id', $request->cliente_id);
            }

            return $this->success($query->orderBy('fecha', 'desc')->get());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $diagnostico = Diagnostico::with(['cliente', 'hallazgos', 'oportunidades', 'acciones'])
                ->findOrFail($id);

            return $this->success($diagnostico);
        } catch (\Exception $e) {
            return $this->notFound('Diagnóstico no encontrado.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate($this->rules());

            $diagnostico = Diagnostico::create($validated);

            return $this->created($diagnostico->load('cliente'), 'Diagnóstico creado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $diagnostico = Diagnostico::findOrFail($id);

            $rules = $this->rules();
            foreach ($rules as $field => $rule) {
                if (str_starts_with($rule, 'required')) {
                    $rules[$field] = 'sometimes|' . $rule;
                }
            }

            $validated = $request->validate($rules);
            $diagnostico->update($validated);

            return $this->success($diagnostico->load('cliente'), 'Diagnóstico actualizado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            Diagnostico::findOrFail($id)->delete();
            return $this->success(null, 'Diagnóstico eliminado.');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function cambiarEstado(Request $request, $id)
    {
        try {
            $diagnostico = Diagnostico::findOrFail($id);

            $validated = $request->validate([
                'estado' => 'required|in:Preparación,Reunión,Análisis,Implementación',
            ]);

            $diagnostico->update(['estado' => $validated['estado']]);

            return $this->success($diagnostico, 'Estado actualizado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    // ── Hallazgos ─────────────────────────────────────────────────────────────

    public function storeHallazgo(Request $request, $diagnosticoId)
    {
        try {
            Diagnostico::findOrFail($diagnosticoId);

            $validated = $request->validate([
                'descripcion' => 'required|string',
                'impacto'     => 'required|in:Alto,Medio,Bajo',
            ]);

            $validated['diagnostico_id'] = $diagnosticoId;
            $hallazgo = HallazgoDiagnostico::create($validated);

            return $this->created($hallazgo, 'Hallazgo agregado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function destroyHallazgo($diagnosticoId, $hallazgoId)
    {
        try {
            HallazgoDiagnostico::where('diagnostico_id', $diagnosticoId)
                ->findOrFail($hallazgoId)
                ->delete();

            return $this->success(null, 'Hallazgo eliminado.');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    // ── Oportunidades ─────────────────────────────────────────────────────────

    public function storeOportunidad(Request $request, $diagnosticoId)
    {
        try {
            Diagnostico::findOrFail($diagnosticoId);

            $validated = $request->validate([
                'descripcion'   => 'required|string',
                'impacto'       => 'required|in:Alto,Medio,Bajo',
                'funcionalidad' => 'nullable|string|max:255',
            ]);

            $validated['diagnostico_id'] = $diagnosticoId;
            $oportunidad = OportunidadDiagnostico::create($validated);

            return $this->created($oportunidad, 'Oportunidad agregada.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function destroyOportunidad($diagnosticoId, $oportunidadId)
    {
        try {
            OportunidadDiagnostico::where('diagnostico_id', $diagnosticoId)
                ->findOrFail($oportunidadId)
                ->delete();

            return $this->success(null, 'Oportunidad eliminada.');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    // ── Acciones ──────────────────────────────────────────────────────────────

    public function storeAccion(Request $request, $diagnosticoId)
    {
        try {
            Diagnostico::findOrFail($diagnosticoId);

            $validated = $request->validate([
                'descripcion'   => 'required|string',
                'tipo'          => 'required|string|max:100',
                'responsable'   => 'required|string|max:100',
                'fecha_limite'  => 'nullable|date',
                'estado'        => 'nullable|in:Pendiente,En curso,Completada',
                'comentarios'   => 'nullable|string',
            ]);

            $validated['diagnostico_id'] = $diagnosticoId;
            $accion = AccionDiagnostico::create($validated);

            return $this->created($accion, 'Acción agregada.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function updateAccion(Request $request, $diagnosticoId, $accionId)
    {
        try {
            $accion = AccionDiagnostico::where('diagnostico_id', $diagnosticoId)
                ->findOrFail($accionId);

            $validated = $request->validate([
                'descripcion'   => 'sometimes|string',
                'tipo'          => 'sometimes|string|max:100',
                'responsable'   => 'sometimes|string|max:100',
                'fecha_limite'  => 'nullable|date',
                'estado'        => 'sometimes|in:Pendiente,En curso,Completada',
                'comentarios'   => 'nullable|string',
            ]);

            $accion->update($validated);

            return $this->success($accion, 'Acción actualizada.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function destroyAccion($diagnosticoId, $accionId)
    {
        try {
            AccionDiagnostico::where('diagnostico_id', $diagnosticoId)
                ->findOrFail($accionId)
                ->delete();

            return $this->success(null, 'Acción eliminada.');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    private function rules(): array
    {
        return [
            'cliente_id'                    => 'required|exists:clientes_comercial,id',
            'nombre'                        => 'required|string|max:255',
            'fecha'                         => 'required|date',
            'estado'                        => 'nullable|in:Preparación,Reunión,Análisis,Implementación',

            'prep_comercial'                => 'nullable|string|max:255',
            'prep_cant_operaciones'         => 'nullable|integer|min:0',
            'prep_tipo_carga'               => 'nullable|array',
            'prep_modulos_habilitados'      => 'nullable|array',
            'prep_estadisticas_uso'         => 'nullable|string',
            'prep_documentos_disponibles'   => 'nullable|string',
            'prep_problemas_conocidos'      => 'nullable|string',

            'reunion_quien_usa'             => 'nullable|string',
            'reunion_que_info_necesita'     => 'nullable|string',
            'reunion_que_valor_encuentra'   => 'nullable|string',
            'reunion_que_no_usa'            => 'nullable|string',
            'reunion_que_le_falta'          => 'nullable|string',

            'uso_frecuencia'                => 'nullable|integer|min:0|max:3',
            'valores_encontrados'           => 'nullable|array',
            'barreras'                      => 'nullable|array',
            'funcionalidades_pedidas'       => 'nullable|array',
        ];
    }
}
