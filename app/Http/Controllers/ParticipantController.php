<?php

namespace App\Http\Controllers;

use App\Models\EducationalEntity;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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

    /**
     * Show the import form.
     */
    public function showImportForm(): View
    {
        return view('participants.import');
    }

    /**
     * Import participants from CSV file.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $path = $file->store('temp');

        try {
            $data = $this->parseCsvFile(Storage::path($path));
            $results = $this->processCsvData($data);

            // Limpiar archivo temporal
            Storage::delete($path);

            $message = "Importación completada. {$results['imported']} participantes importados, {$results['errors']} errores.";

            if ($results['errors'] > 0) {
                $message .= " Revisa los errores en el archivo de log.";
            }

            return redirect()->route('participants.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            // Limpiar archivo temporal en caso de error
            Storage::delete($path);

            return redirect()->route('participants.import')
                ->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Parse CSV file and return data array.
     */
    private function parseCsvFile(string $filePath): array
    {
        $data = [];
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            throw new \Exception('No se pudo abrir el archivo CSV');
        }

        // Leer encabezados
        $headers = fgetcsv($handle, 1000, ',');

        if (!$headers) {
            throw new \Exception('El archivo CSV está vacío o no tiene encabezados válidos');
        }

        // Normalizar encabezados
        $headers = array_map('trim', $headers);
        $headers = array_map('strtolower', $headers);

        // Leer filas de datos
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            if (count($row) === count($headers)) {
                $data[] = array_combine($headers, $row);
            }
        }

        fclose($handle);

        if (empty($data)) {
            throw new \Exception('El archivo CSV no contiene datos válidos');
        }

        return $data;
    }

    /**
     * Process CSV data and create participants.
     */
    private function processCsvData(array $data): array
    {
        $imported = 0;
        $errors = 0;
        $educationalEntities = EducationalEntity::pluck('id', 'name')->toArray();

        foreach ($data as $rowIndex => $row) {
            try {
                // Mapear campos del CSV (flexible)
                $participantData = $this->mapCsvRow($row, $educationalEntities);

                // Validar datos
                $validator = Validator::make($participantData, [
                    'educational_entity_id' => 'required|exists:educational_entities,id',
                    'full_name' => 'required|string|max:255',
                    'phone' => 'nullable|string|max:20',
                    'position' => 'nullable|string|max:255',
                    'registration_date' => 'nullable|date',
                ]);

                if ($validator->fails()) {
                    $errors++;
                    \Log::warning("Error en fila " . ($rowIndex + 2) . ": " . implode(', ', $validator->errors()->all()));
                    continue;
                }

                // Crear participante
                $participant = Participant::create($validator->validated());

                // Registrar en audit log
                \App\Models\AuditLog::log('import', 'Participante importado desde CSV', [
                    'model_type' => 'Participant',
                    'model_id' => $participant->id,
                    'new_values' => $participantData,
                ]);

                $imported++;

            } catch (\Exception $e) {
                $errors++;
                \Log::error("Error en fila " . ($rowIndex + 2) . ": " . $e->getMessage());
            }
        }

        return ['imported' => $imported, 'errors' => $errors];
    }

    /**
     * Map CSV row to participant data.
     */
    private function mapCsvRow(array $row, array $educationalEntities): array
    {
        $data = [];

        // Buscar institución educativa por nombre
        $entityName = $row['institucion'] ?? $row['institución'] ?? $row['educational_entity'] ?? $row['entidad_educativa'] ?? '';
        $entityId = null;

        if (!empty($entityName)) {
            // Buscar coincidencia exacta primero
            $entityId = array_search($entityName, array_flip($educationalEntities));

            // Si no encuentra, buscar coincidencia parcial
            if (!$entityId) {
                foreach ($educationalEntities as $name => $id) {
                    if (stripos($name, $entityName) !== false || stripos($entityName, $name) !== false) {
                        $entityId = $id;
                        break;
                    }
                }
            }
        }

        $data['educational_entity_id'] = $entityId;
        $data['full_name'] = $row['nombre_completo'] ?? $row['full_name'] ?? $row['nombre'] ?? '';
        $data['phone'] = $row['telefono'] ?? $row['celular'] ?? $row['phone'] ?? $row['mobile'] ?? '';
        $data['position'] = $row['cargo'] ?? $row['position'] ?? $row['puesto'] ?? '';
        $data['registration_date'] = $row['fecha_registro'] ?? $row['registration_date'] ?? $row['fecha'] ?? null;

        // Limpiar datos
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }

        return $data;
    }
}
