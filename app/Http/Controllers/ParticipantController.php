<?php

namespace App\Http\Controllers;

use App\Models\EducationalEntity;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ParticipantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Participant::with('educationalEntity');

        // Filtros
        if ($request->filled('educational_entity_id')) {
            $query->where('educational_entity_id', $request->educational_entity_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $participants = $query->orderBy('created_at', 'desc')->paginate(15);
        $educationalEntities = EducationalEntity::orderBy('name')->get();

        return view('participants.index', compact('participants', 'educationalEntities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $educationalEntities = EducationalEntity::orderBy('name')->get();
        $selectedEntity = $request->educational_entity_id
            ? EducationalEntity::find($request->educational_entity_id)
            : null;

        return view('participants.create', compact('educationalEntities', 'selectedEntity'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'educational_entity_id' => 'required|exists:educational_entities,id',
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'registration_date' => 'nullable|date',
        ]);

        $participant = Participant::create($validated);

        // Registrar en audit log
        \App\Models\AuditLog::log('create', 'Participante creado', [
            'model_type' => 'Participant',
            'model_id' => $participant->id,
            'new_values' => $validated,
        ]);

        return redirect()->route('participants.index')
            ->with('success', 'Participante creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Participant $participant): View
    {
        $participant->load('educationalEntity');
        return view('participants.show', compact('participant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Participant $participant): View
    {
        $educationalEntities = EducationalEntity::orderBy('name')->get();
        return view('participants.edit', compact('participant', 'educationalEntities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Participant $participant): RedirectResponse
    {
        $validated = $request->validate([
            'educational_entity_id' => 'required|exists:educational_entities,id',
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
            'registration_date' => 'nullable|date',
        ]);

        $oldValues = $participant->toArray();
        $participant->update($validated);

        // Registrar en audit log
        \App\Models\AuditLog::log('update', 'Participante actualizado', [
            'model_type' => 'Participant',
            'model_id' => $participant->id,
            'old_values' => $oldValues,
            'new_values' => $validated,
        ]);

        return redirect()->route('participants.index')
            ->with('success', 'Participante actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Participant $participant): RedirectResponse
    {
        $oldValues = $participant->toArray();

        // Registrar en audit log antes de eliminar
        \App\Models\AuditLog::log('delete', 'Participante eliminado', [
            'model_type' => 'Participant',
            'model_id' => $participant->id,
            'old_values' => $oldValues,
        ]);

        $participant->delete();

        return redirect()->route('participants.index')
            ->with('success', 'Participante eliminado exitosamente.');
    }
}
