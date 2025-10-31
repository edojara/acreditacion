<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Participant;
use App\Models\EducationalEntity;

class ParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entities = EducationalEntity::all();

        if ($entities->isEmpty()) {
            echo "No hay entidades educativas. Ejecuta primero EducationalEntitySeeder.\n";
            return;
        }

        $positions = [
            'Director',
            'Rector',
            'Decano',
            'Secretario Académico',
            'Jefe de Carrera',
            'Profesor',
            'Coordinador',
            'Asistente Administrativo',
            'Encargado de Admisión',
            'Bibliotecario',
            'Técnico en Educación',
            'Consejero Estudiantil',
            'Psicólogo Educacional',
            'Enfermero',
            'Inspector General',
            'Orientador Educacional'
        ];

        $firstNames = [
            'María', 'José', 'Juan', 'Ana', 'Carlos', 'Luis', 'Carmen', 'Antonio', 'Rosa', 'Francisco',
            'Isabel', 'Manuel', 'Pilar', 'Miguel', 'Dolores', 'Ángel', 'Lucía', 'Fernando', 'Cristina', 'Jesús',
            'Mercedes', 'Rafael', 'Teresa', 'Alberto', 'Elena', 'Diego', 'Silvia', 'Sergio', 'Beatriz', 'Pablo'
        ];

        $lastNames = [
            'González', 'Rodríguez', 'García', 'Fernández', 'López', 'Martínez', 'Sánchez', 'Pérez', 'Martín', 'Ruiz',
            'Hernández', 'Jiménez', 'Díaz', 'Moreno', 'Álvarez', 'Romero', 'Navarro', 'Torres', 'Ramírez', 'Ramos',
            'Gil', 'Vargas', 'Serrano', 'Blanco', 'Molina', 'Morales', 'Ortega', 'Delgado', 'Castro', 'Ortiz'
        ];

        $participants = [];

        // Crear entre 1-5 participantes por entidad
        foreach ($entities as $entity) {
            $numParticipants = rand(1, 5);

            for ($i = 0; $i < $numParticipants; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName1 = $lastNames[array_rand($lastNames)];
                $lastName2 = $lastNames[array_rand($lastNames)];

                $fullName = $firstName . ' ' . $lastName1 . ' ' . $lastName2;
                $position = $positions[array_rand($positions)];
                $phone = '+56 9 ' . rand(10000000, 99999999);

                // Evitar duplicados básicos
                $exists = false;
                foreach ($participants as $p) {
                    if ($p['full_name'] === $fullName && $p['educational_entity_id'] === $entity->id) {
                        $exists = true;
                        break;
                    }
                }

                if (!$exists) {
                    $participants[] = [
                        'educational_entity_id' => $entity->id,
                        'full_name' => $fullName,
                        'position' => $position,
                        'phone' => $phone,
                        'registration_date' => now()->subDays(rand(0, 365)),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Insertar en lotes para mejor rendimiento
        $chunks = array_chunk($participants, 100);
        foreach ($chunks as $chunk) {
            Participant::insert($chunk);
        }

        echo "Se crearon " . count($participants) . " participantes.\n";
    }
}
