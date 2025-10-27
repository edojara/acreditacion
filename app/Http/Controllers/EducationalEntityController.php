<?php

namespace App\Http\Controllers;

use App\Models\EducationalEntity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EducationalEntityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EducationalEntity::with('contacts')
            ->withCount('contacts');

        // Filtros
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        $entities = $query->paginate(15);

        // Obtener tipos únicos para autocompletar
        $existingTypes = EducationalEntity::distinct()->pluck('type')->filter()->values();

        return view('educational-entities.index', compact('entities', 'existingTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('educational-entities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:educational_entities',
            'code' => 'required|string|max:50|unique:educational_entities',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'type' => 'required|in:universidad,instituto,colegio,centro_educativo,otro',
            'status' => 'required|in:activo,inactivo,suspendido',
        ]);

        $entity = EducationalEntity::create($validated);

        // Registrar en audit log
        \App\Models\AuditLog::log('create', 'Entidad educativa creada: ' . $entity->name, [
            'model_type' => 'EducationalEntity',
            'model_id' => $entity->id,
            'new_values' => $validated,
        ]);

        return redirect()->route('educational-entities.index')
                        ->with('success', 'Entidad educativa creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EducationalEntity $educationalEntity)
    {
        $educationalEntity->load(['contacts' => function($query) {
            $query->active()->orderBy('is_primary', 'desc');
        }]);

        return view('educational-entities.show', compact('educationalEntity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EducationalEntity $educationalEntity)
    {
        return view('educational-entities.edit', compact('educationalEntity'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EducationalEntity $educationalEntity)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('educational_entities')->ignore($educationalEntity->id)],
            'code' => ['required', 'string', 'max:50', Rule::unique('educational_entities')->ignore($educationalEntity->id)],
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'type' => 'required|in:universidad,instituto,colegio,centro_educativo,otro',
            'status' => 'required|in:activo,inactivo,suspendido',
        ]);

        $oldValues = $educationalEntity->only(array_keys($validated));

        $educationalEntity->update($validated);

        $newValues = $educationalEntity->only(array_keys($validated));

        // Registrar en audit log
        \App\Models\AuditLog::log('update', 'Entidad educativa actualizada: ' . $educationalEntity->name, [
            'model_type' => 'EducationalEntity',
            'model_id' => $educationalEntity->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);

        return redirect()->route('educational-entities.index')
                        ->with('success', 'Entidad educativa actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EducationalEntity $educationalEntity)
    {
        // Verificar si tiene contactos activos
        if ($educationalEntity->contacts()->active()->exists()) {
            return redirect()->route('educational-entities.index')
                            ->with('error', 'No se puede eliminar una entidad que tiene contactos activos.');
        }

        $entityName = $educationalEntity->name;

        // Registrar eliminación en audit log
        \App\Models\AuditLog::log('delete', 'Entidad educativa eliminada: ' . $entityName, [
            'model_type' => 'EducationalEntity',
            'model_id' => $educationalEntity->id,
            'old_values' => $educationalEntity->toArray(),
        ]);

        $educationalEntity->delete();

        return redirect()->route('educational-entities.index')
                        ->with('success', 'Entidad educativa eliminada exitosamente.');
    }
}
