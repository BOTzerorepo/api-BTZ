<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\CotizacionItem;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CotizacionController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        try {
            $user = auth('api')->user();
            $query = Cotizacion::with(['cliente', 'comercial', 'items']);

            if (!$user->hasRole('AdminComercial')) {
                $query->where('comercial_id', $user->id);
            }

            if ($request->filled('estado')) {
                $query->where('estado', $request->estado);
            }
            if ($request->filled('cliente_id')) {
                $query->where('cliente_id', $request->cliente_id);
            }

            return $this->success($query->orderBy('created_at', 'desc')->get());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $cotizacion = Cotizacion::with(['cliente', 'comercial', 'combo', 'items.tarifarioItem'])
                ->findOrFail($id);

            return $this->success($cotizacion);
        } catch (\Exception $e) {
            return $this->notFound('Cotización no encontrada.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'cliente_id'     => 'required|exists:clientes_comercial,id',
                'combo_id'       => 'nullable|exists:tarifario_combos,id',
                'fecha_vigencia' => 'required|date',
                'notas'          => 'nullable|string',
                'items'          => 'required|array|min:1',
                'items.*.tarifario_item_id' => 'required|exists:tarifario_items,id',
                'items.*.descripcion'       => 'nullable|string',
                'items.*.origen'            => 'required|string|max:255',
                'items.*.destino'           => 'required|string|max:255',
                'items.*.tipo_servicio'     => 'required|string|max:50',
                'items.*.tipo_cntr'         => 'required|string|max:100',
                'items.*.moneda'            => 'required|in:USD,ARS',
                'items.*.tarifa'            => 'required|numeric|min:0',
            ]);

            $user = auth('api')->user();

            $cotizacion = DB::transaction(function () use ($validated, $user) {
                $numero = $this->generarNumero();

                $totalUsd = collect($validated['items'])->sum(function ($item) {
                    return $item['moneda'] === 'USD' ? $item['tarifa'] : 0;
                });

                $cotizacion = Cotizacion::create([
                    'numero'         => $numero,
                    'cliente_id'     => $validated['cliente_id'],
                    'comercial_id'   => $user->id,
                    'combo_id'       => $validated['combo_id'] ?? null,
                    'fecha_creacion' => now()->toDateString(),
                    'fecha_vigencia' => $validated['fecha_vigencia'],
                    'estado'         => 'Pendiente',
                    'total_usd'      => $totalUsd,
                    'notas'          => $validated['notas'] ?? null,
                ]);

                foreach ($validated['items'] as $itemData) {
                    $cotizacion->items()->create($itemData);
                }

                return $cotizacion;
            });

            return $this->created($cotizacion->load(['items', 'cliente']), 'Cotización creada.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $cotizacion = Cotizacion::findOrFail($id);

            $validated = $request->validate([
                'combo_id'       => 'nullable|exists:tarifario_combos,id',
                'fecha_vigencia' => 'sometimes|date',
                'notas'          => 'nullable|string',
                'items'          => 'sometimes|array|min:1',
                'items.*.tarifario_item_id' => 'required_with:items|exists:tarifario_items,id',
                'items.*.descripcion'       => 'nullable|string',
                'items.*.origen'            => 'required_with:items|string|max:255',
                'items.*.destino'           => 'required_with:items|string|max:255',
                'items.*.tipo_servicio'     => 'required_with:items|string|max:50',
                'items.*.tipo_cntr'         => 'required_with:items|string|max:100',
                'items.*.moneda'            => 'required_with:items|in:USD,ARS',
                'items.*.tarifa'            => 'required_with:items|numeric|min:0',
            ]);

            DB::transaction(function () use ($cotizacion, $validated) {
                $itemsData = $validated['items'] ?? null;
                unset($validated['items']);

                if ($itemsData !== null) {
                    $validated['total_usd'] = collect($itemsData)->sum(function ($item) {
                        return $item['moneda'] === 'USD' ? $item['tarifa'] : 0;
                    });

                    $cotizacion->items()->delete();
                    foreach ($itemsData as $itemData) {
                        $cotizacion->items()->create($itemData);
                    }
                }

                $cotizacion->update($validated);
            });

            return $this->success($cotizacion->load(['items', 'cliente']), 'Cotización actualizada.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            Cotizacion::findOrFail($id)->delete();
            return $this->success(null, 'Cotización eliminada.');
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    public function cambiarEstado(Request $request, $id)
    {
        try {
            $cotizacion = Cotizacion::findOrFail($id);

            $validated = $request->validate([
                'estado' => 'required|in:Pendiente,Enviada,Aceptada,Rechazada,Expirada',
            ]);

            $cotizacion->update(['estado' => $validated['estado']]);

            return $this->success($cotizacion, 'Estado actualizado.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->error('Error de validación.', 'VALIDATION_ERROR', 422, $e->errors());
        } catch (\Exception $e) {
            return $this->serverError($e->getMessage());
        }
    }

    private function generarNumero(): string
    {
        $year = now()->year;

        $lastNum = Cotizacion::whereYear('created_at', $year)
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(numero, '-', -1) AS UNSIGNED)) as max_num")
            ->value('max_num') ?? 0;

        return 'COT-' . $year . '-' . str_pad($lastNum + 1, 3, '0', STR_PAD_LEFT);
    }
}
