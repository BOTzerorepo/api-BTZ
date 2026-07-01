<?php

namespace App\Http\Controllers;

use App\Models\TarifarioCombo;
use App\Models\TarifarioItem;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class TarifarioController extends Controller
{
    use ApiResponse;

    // ── Items ───────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        try {
            $user = auth('api')->user();
            $query = TarifarioItem::query();

            if (!$user->hasRole('AdminComercial')) {
                $query->where('empresa', $user->empresa);
            }

            if ($request->filled('tipo_servicio')) {
                $query->where('tipo_servicio', $request->tipo_servicio);
            }
            if ($request->filled('origen')) {
                $query->where('origen', 'like', '%' . $request->origen . '%');
            }
            if ($request->filled('destino')) {
                $query->where('destino', 'like', '%' . $request->destino . '%');
            }

            return $this->success($query->orderBy('created_at', 'desc')->get());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $item = TarifarioItem::findOrFail($id);
            return $this->success($item);
        } catch (\Exception $e) {
            return $this->notFound('Item de tarifario no encontrado.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'origen'          => 'required|string|max:255',
                'destino'         => 'required|string|max:255',
                'tipo_servicio'   => 'required|in:EMAR,ETER,IMAR,ITER,FOB,NAC',
                'tipo_cntr'       => 'required|string|max:100',
                'moneda'          => 'required|in:USD,ARS',
                'tarifa'          => 'required|numeric|min:0',
                'descripcion'     => 'nullable|string',
                'vigencia_desde'  => 'required|date',
                'vigencia_hasta'  => 'required|date|after_or_equal:vigencia_desde',
            ]);

            $validated['empresa'] = auth('api')->user()->empresa;
            $item = TarifarioItem::create($validated);

            return $this->created($item, 'Item de tarifario creado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $item = TarifarioItem::findOrFail($id);

            $validated = $request->validate([
                'origen'          => 'sometimes|string|max:255',
                'destino'         => 'sometimes|string|max:255',
                'tipo_servicio'   => 'sometimes|in:EMAR,ETER,IMAR,ITER,FOB,NAC',
                'tipo_cntr'       => 'sometimes|string|max:100',
                'moneda'          => 'sometimes|in:USD,ARS',
                'tarifa'          => 'sometimes|numeric|min:0',
                'descripcion'     => 'nullable|string',
                'vigencia_desde'  => 'sometimes|date',
                'vigencia_hasta'  => 'sometimes|date',
            ]);

            $item->update($validated);

            return $this->success($item, 'Item actualizado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            TarifarioItem::findOrFail($id)->delete();
            return $this->success(null, 'Item eliminado.');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    // ── Combos ──────────────────────────────────────────────────────────────

    public function indexCombos(Request $request)
    {
        try {
            $user = auth('api')->user();
            $query = TarifarioCombo::with('items');

            if (!$user->hasRole('AdminComercial')) {
                $query->where('empresa', $user->empresa);
            }

            return $this->success($query->orderBy('created_at', 'desc')->get());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function showCombo($id)
    {
        try {
            $combo = TarifarioCombo::with('items')->findOrFail($id);
            return $this->success($combo);
        } catch (\Exception $e) {
            return $this->notFound('Combo no encontrado.');
        }
    }

    public function storeCombo(Request $request)
    {
        try {
            $validated = $request->validate([
                'nombre'         => 'required|string|max:255',
                'descripcion'    => 'nullable|string',
                'precio_combo'   => 'required|numeric|min:0',
                'moneda'         => 'required|in:USD,ARS',
                'vigencia_desde' => 'required|date',
                'vigencia_hasta' => 'required|date|after_or_equal:vigencia_desde',
                'items'          => 'nullable|array',
                'items.*'        => 'integer|exists:tarifario_items,id',
            ]);

            $validated['empresa'] = auth('api')->user()->empresa;
            $itemIds = $validated['items'] ?? [];
            unset($validated['items']);

            $combo = TarifarioCombo::create($validated);

            if (!empty($itemIds)) {
                $combo->items()->sync($itemIds);
            }

            return $this->created($combo->load('items'), 'Combo creado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function updateCombo(Request $request, $id)
    {
        try {
            $combo = TarifarioCombo::findOrFail($id);

            $validated = $request->validate([
                'nombre'         => 'sometimes|string|max:255',
                'descripcion'    => 'nullable|string',
                'precio_combo'   => 'sometimes|numeric|min:0',
                'moneda'         => 'sometimes|in:USD,ARS',
                'vigencia_desde' => 'sometimes|date',
                'vigencia_hasta' => 'sometimes|date',
                'items'          => 'nullable|array',
                'items.*'        => 'integer|exists:tarifario_items,id',
            ]);

            $itemIds = $validated['items'] ?? null;
            unset($validated['items']);

            $combo->update($validated);

            if ($itemIds !== null) {
                $combo->items()->sync($itemIds);
            }

            return $this->success($combo->load('items'), 'Combo actualizado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function destroyCombo($id)
    {
        try {
            TarifarioCombo::findOrFail($id)->delete();
            return $this->success(null, 'Combo eliminado.');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }
}
