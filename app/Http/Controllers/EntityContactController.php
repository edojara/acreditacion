<?php

namespace App\Http\Controllers;

use App\Models\EducationalEntity;
use App\Models\EntityContact;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EntityContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EntityContact::with('educationalEntity')
            ->orderBy('educational_entity_id')
            ->orderBy('is_primary', 'desc');

        // Filtros
        if ($request->filled('educational_entity_id')) {
            $query->where('educational_entity_id', $request->educational_entity_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $contacts = $query->paginate(15);
        $entities = EducationalEntity::active()->orderBy('name')->get();

        return view('entity-contacts.index', compact('contacts', 'entities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $entities = EducationalEntity::active()->orderBy('name')->get();

        // Si viene de una entidad específica
        $selectedEntity = null;
        if ($request->filled('educational_entity_id')) {
            $selectedEntity = EducationalEntity::find($request->educational_entity_id);
        }

        return view('entity-contacts.create', compact('entities', 'selectedEntity'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'educational_entity_id' => 'required|exists:educational_entities,id',
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('entity_contacts')->where(function ($query) use ($request) {
                    return $query->where('educational_entity_id', $request->educational_entity_id);
                })
            ],
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'type' => 'required|in:principal,academico,administrativo,tecnico,otro',
            'is_primary' => 'boolean',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:activo,inactivo',
        ]);

        $contact = EntityContact::create($validated);

        // Registrar en audit log
        \App\Models\AuditLog::log('create', 'Contacto creado: ' . $contact->name . ' (' . $contact->educationalEntity->name . ')', [
            'model_type' => 'EntityContact',
            'model_id' => $contact->id,
            'new_values' => $validated,
        ]);

        return redirect()->route('entity-contacts.index')
                        ->with('success', 'Contacto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(EntityContact $entityContact)
    {
        $entityContact->load('educationalEntity');
        return view('entity-contacts.show', compact('entityContact'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EntityContact $entityContact)
    {
        $entities = EducationalEntity::active()->orderBy('name')->get();
        return view('entity-contacts.edit', compact('entityContact', 'entities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EntityContact $entityContact)
    {
        $validated = $request->validate([
            'educational_entity_id' => 'required|exists:educational_entities,id',
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('entity_contacts')->where(function ($query) use ($request, $entityContact) {
                    return $query->where('educational_entity_id', $request->educational_entity_id)
                                ->where('id', '!=', $entityContact->id);
                })
            ],
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'type' => 'required|in:principal,academico,administrativo,tecnico,otro',
            'is_primary' => 'boolean',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:activo,inactivo',
        ]);

        $oldValues = $entityContact->only(array_keys($validated));

        $entityContact->update($validated);

        $newValues = $entityContact->only(array_keys($validated));

        // Registrar en audit log
        \App\Models\AuditLog::log('update', 'Contacto actualizado: ' . $entityContact->name, [
            'model_type' => 'EntityContact',
            'model_id' => $entityContact->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);

        return redirect()->route('entity-contacts.index')
                        ->with('success', 'Contacto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntityContact $entityContact)
    {
        $contactName = $entityContact->name;
        $entityName = $entityContact->educationalEntity->name;

        // Registrar eliminación en audit log
        \App\Models\AuditLog::log('delete', 'Contacto eliminado: ' . $contactName . ' (' . $entityName . ')', [
            'model_type' => 'EntityContact',
            'model_id' => $entityContact->id,
            'old_values' => $entityContact->toArray(),
        ]);

        $entityContact->delete();

        return redirect()->route('entity-contacts.index')
                        ->with('success', 'Contacto eliminado exitosamente.');
    }
}
