<?php
// app/Http/Controllers/ParticularSoftConfigurationController.php
namespace App\Http\Controllers;

use App\Models\ParticularSoftConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParticularSoftConfigurationController extends Controller
{
    // Listado general
    public function index()
    {
        return ParticularSoftConfiguration::orderBy('id', 'desc')->get();
    }

    // Configuración actual (la última o única)
    public function current()
    {
        return ParticularSoftConfiguration::latest('id')->first();
    }

    // Mostrar una configuración específica
    public function show($id)
    {
        return ParticularSoftConfiguration::findOrFail($id);
    }

    // Crear nueva configuración
    public function store(Request $request)
    {
        $data = $request->all();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('psc', 'public');
        }

        if ($request->hasFile('imgLogin')) {
            $data['imgLogin'] = $request->file('imgLogin')->store('psc', 'public');
        }

        $psc = ParticularSoftConfiguration::create($data);

        return response()->json($psc, 201);
    }

    // Actualizar configuración existente
    public function update(Request $request, $id)
    {
        $psc = ParticularSoftConfiguration::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('logo')) {
            if ($psc->logo) {
                Storage::disk('public')->delete($psc->logo);
            }
            $data['logo'] = $request->file('logo')->store('psc', 'public');
        }

        if ($request->hasFile('imgLogin')) {
            if ($psc->imgLogin) {
                Storage::disk('public')->delete($psc->imgLogin);
            }
            $data['imgLogin'] = $request->file('imgLogin')->store('psc', 'public');
        }

        $psc->update($data);

        return response()->json($psc);
    }

    // Eliminar configuración
    public function destroy($id)
    {
        $psc = ParticularSoftConfiguration::findOrFail($id);

        if ($psc->logo) Storage::disk('public')->delete($psc->logo);
        if ($psc->imgLogin) Storage::disk('public')->delete($psc->imgLogin);

        $psc->delete();

        return response()->noContent();
    }
}
