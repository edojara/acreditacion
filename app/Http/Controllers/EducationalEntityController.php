<?php

namespace App\Http\Controllers;

use App\Models\EducationalEntity;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EducationalEntityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = EducationalEntity::with('contacts')
            ->withCount('contacts');

        // Determinar cantidad de registros por página
        $perPage = $request->get('per_page', 15);
        if ($perPage === 'all') {
            $perPage = 1000; // Un número alto para mostrar todos
        } elseif (!is_numeric($perPage) || $perPage < 1) {
            $perPage = 15;
        }

        // Filtros
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }


        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Ordenamiento
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');

        // Validar campos de ordenamiento permitidos
        $allowedSortFields = ['name', 'type', 'city', 'region', 'phone', 'email', 'contacts_count', 'created_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'name';
        }

        // Validar dirección de ordenamiento
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Aplicar ordenamiento
        if ($sortBy === 'contacts_count') {
            $query->orderBy('contacts_count', $sortDirection)
                  ->orderBy('name', 'asc'); // Orden secundario por nombre
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        $entities = $query->paginate($perPage)->appends($request->query());

        // Obtener tipos únicos para autocompletar
        $existingTypes = EducationalEntity::distinct()->pluck('type')->filter()->values();

        return view('educational-entities.index', compact('entities', 'existingTypes', 'sortBy', 'sortDirection'));
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
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'type' => 'required|in:universidad,instituto,colegio,centro_educativo,otro',
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
            $query->orderBy('is_primary', 'desc');
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
        // Convertir el campo 'type' a minúscula para evitar problemas de case sensitivity
        $request->merge(['type' => strtolower($request->type)]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('educational_entities')->ignore($educationalEntity->id)],
            'code' => 'nullable|string|max:50', // Campo eliminado pero incluido para detectar cambios
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string', // Removida validación restrictiva - permite cualquier texto
            'type' => 'required|in:universidad,instituto,colegio,centro_educativo,otro',
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
        // Verificar si tiene contactos
        if ($educationalEntity->contacts()->exists()) {
            return redirect()->route('educational-entities.index')
                            ->with('error', 'No se puede eliminar una entidad que tiene contactos.');
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

    /**
     * Show the import form.
     */
    public function showImportForm(): View
    {
        return view('educational-entities.import');
    }

    /**
     * Import educational entities from CSV file.
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

            $message = "Importación completada. {$results['imported']} instituciones importadas, {$results['errors']} errores.";

            if ($results['errors'] > 0) {
                $message .= " Revisa los errores en el archivo de log.";
            }

            return redirect()->route('educational-entities.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            // Limpiar archivo temporal en caso de error
            Storage::delete($path);

            return redirect()->route('educational-entities.import')
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
     * Process CSV data and create educational entities.
     */
    private function processCsvData(array $data): array
    {
        $imported = 0;
        $errors = 0;

        $validTypes = ['universidad', 'instituto', 'colegio', 'centro_educativo', 'otro'];

        foreach ($data as $rowIndex => $row) {
            try {
                // Mapear campos del CSV
                $entityData = $this->mapCsvRow($row, $validTypes);

                // Validar datos
                $validator = Validator::make($entityData, [
                    'name' => 'required|string|max:255|unique:educational_entities,name',
                    'address' => 'nullable|string|max:255',
                    'city' => 'nullable|string|max:100',
                    'region' => 'nullable|string|max:100',
                    'country' => 'nullable|string|max:100',
                    'phone' => 'nullable|string|max:20',
                    'email' => 'nullable|email|max:255',
                    'website' => 'nullable|string|max:255',
                    'type' => 'required|in:universidad,instituto,colegio,centro_educativo,otro',
                ]);

                if ($validator->fails()) {
                    $errors++;
                    \Log::warning("Error en fila " . ($rowIndex + 2) . ": " . implode(', ', $validator->errors()->all()));
                    continue;
                }

                // Crear entidad educativa
                $entity = EducationalEntity::create($validator->validated());

                // Registrar en audit log
                \App\Models\AuditLog::log('import', 'Institución educativa importada desde CSV', [
                    'model_type' => 'EducationalEntity',
                    'model_id' => $entity->id,
                    'new_values' => $entityData,
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
     * Map CSV row to educational entity data.
     */
    private function mapCsvRow(array $row, array $validTypes): array
    {
        $data = [];

        // Mapear campos con nombres alternativos
        $data['name'] = $row['nombre'] ?? $row['name'] ?? $row['institucion'] ?? $row['institución'] ?? '';
        $data['address'] = $row['direccion'] ?? $row['address'] ?? $row['dirección'] ?? '';
        $data['city'] = $row['ciudad'] ?? $row['city'] ?? '';
        $data['region'] = $row['region'] ?? $row['región'] ?? '';
        $data['country'] = $row['pais'] ?? $row['country'] ?? $row['país'] ?? 'Chile';
        $data['phone'] = $row['telefono'] ?? $row['phone'] ?? $row['teléfono'] ?? '';
        $data['email'] = $row['email'] ?? $row['correo'] ?? '';
        $data['website'] = $row['sitio_web'] ?? $row['website'] ?? $row['web'] ?? '';

        // Mapear tipo con validación
        $typeInput = $row['tipo'] ?? $row['type'] ?? '';
        $typeInput = strtolower(trim($typeInput));

        // Buscar coincidencia en tipos válidos
        $matchedType = null;
        foreach ($validTypes as $validType) {
            if (stripos($validType, $typeInput) !== false || stripos($typeInput, $validType) !== false) {
                $matchedType = $validType;
                break;
            }
        }

        // Si no hay coincidencia, intentar mapear términos comunes
        if (!$matchedType) {
            $typeMappings = [
                'universidad' => ['uni', 'university', 'u'],
                'instituto' => ['inst', 'institute', 'i'],
                'colegio' => ['col', 'school', 'colegio', 'c'],
                'centro_educativo' => ['centro', 'center', 'educativo', 'ce'],
            ];

            foreach ($typeMappings as $mappedType => $keywords) {
                foreach ($keywords as $keyword) {
                    if (stripos($typeInput, $keyword) !== false) {
                        $matchedType = $mappedType;
                        break 2;
                    }
                }
            }
        }

        $data['type'] = $matchedType ?: 'otro';

        // Limpiar datos
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            }
        }

        return $data;
    }
}
