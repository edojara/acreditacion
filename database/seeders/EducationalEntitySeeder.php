<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EducationalEntity;
use App\Models\EntityContact;

class EducationalEntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entities = [
            [
                'name' => 'Pontificia Universidad Católica de Chile',
                'type' => 'Universidad',
                'address' => 'Av. Libertador Bernardo O\'Higgins 340, Santiago',
                'email' => 'informaciones@puc.cl',
                'phone' => '+56 2 2354 2000',
                'contact_name' => 'María González',
            ],
            [
                'name' => 'Universidad de Chile',
                'type' => 'Universidad',
                'address' => 'Av. Francisco Bilbao 1307, Santiago',
                'email' => 'informaciones@uchile.cl',
                'phone' => '+56 2 2978 2000',
                'contact_name' => 'Carlos Rodríguez',
            ],
            [
                'name' => 'Universidad Adolfo Ibáñez',
                'type' => 'Universidad',
                'address' => 'Diagonal Las Torres 2640, Peñalolén, Santiago',
                'email' => 'admisión@uai.cl',
                'phone' => '+56 2 2331 1000',
                'contact_name' => 'Ana Martínez',
            ],
            [
                'name' => 'Instituto Profesional AIEP',
                'type' => 'Instituto',
                'address' => 'Av. Los Leones 695, Providencia, Santiago',
                'email' => 'informaciones@aiep.cl',
                'phone' => '+56 2 2827 2000',
                'contact_name' => 'Pedro Sánchez',
            ],
            [
                'name' => 'Duoc UC',
                'type' => 'Instituto',
                'address' => 'Av. Vicuña Mackenna 4860, Macul, Santiago',
                'email' => 'informaciones@duoc.cl',
                'phone' => '+56 2 2382 2000',
                'contact_name' => 'Laura Jiménez',
            ],
            [
                'name' => 'Colegio San Ignacio',
                'type' => 'Colegio',
                'address' => 'Av. Alonso de Córdova 8500, Vitacura, Santiago',
                'email' => 'informaciones@sanignacio.cl',
                'phone' => '+56 2 2483 2000',
                'contact_name' => 'José Fernández',
            ],
            [
                'name' => 'Colegio Alemán de Santiago',
                'type' => 'Colegio',
                'address' => 'Calle Alonso de Córdova 2800, Vitacura, Santiago',
                'email' => 'info@ds.cl',
                'phone' => '+56 2 2483 3000',
                'contact_name' => 'Elena Vargas',
            ],
            [
                'name' => 'Centro Educativo Manquehue',
                'type' => 'Centro Educativo',
                'address' => 'Av. Manquehue Norte 1707, Vitacura, Santiago',
                'email' => 'informaciones@manquehue.cl',
                'phone' => '+56 2 2483 4000',
                'contact_name' => 'Roberto Díaz',
            ],
            [
                'name' => 'Instituto Nacional José Miguel Carrera',
                'type' => 'Instituto',
                'address' => 'Av. Recoleta 177, Santiago',
                'email' => 'informaciones@injc.cl',
                'phone' => '+56 2 2639 2000',
                'contact_name' => 'Patricia López',
            ],
            [
                'name' => 'Colegio Santa Úrsula',
                'type' => 'Colegio',
                'address' => 'Av. Santa María 1200, Providencia, Santiago',
                'email' => 'informaciones@santaursula.cl',
                'phone' => '+56 2 2233 2000',
                'contact_name' => 'Miguel Torres',
            ],
        ];

        foreach ($entities as $entityData) {
            $entity = EducationalEntity::create([
                'name' => $entityData['name'],
                'code' => $this->generateCode($entityData['name']),
                'type' => $this->normalizeType($entityData['type']),
                'address' => $entityData['address'],
                'city' => 'Santiago',
                'region' => 'Metropolitana',
                'email' => $entityData['email'],
                'phone' => $entityData['phone'],
                'status' => 'activo',
            ]);

            // Crear 1-3 contactos adicionales por entidad (ficticios)
            $numContacts = rand(1, 3);
            for ($i = 0; $i < $numContacts; $i++) {
                EntityContact::create([
                    'educational_entity_id' => $entity->id,
                    'name' => $this->getRandomContactName(),
                    'position' => $this->getRandomPosition(),
                    'email' => $this->getRandomEmail(),
                    'phone' => $this->getRandomPhone(),
                ]);
            }
        }
    }

    private function getRandomContactName(): string
    {
        $firstNames = ['Juan', 'María', 'Pedro', 'Ana', 'Carlos', 'Laura', 'José', 'Patricia', 'Miguel', 'Elena'];
        $lastNames = ['González', 'Rodríguez', 'Martínez', 'López', 'Fernández', 'Jiménez', 'Torres', 'Vargas', 'Díaz', 'Sánchez'];

        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    private function getRandomPosition(): string
    {
        $positions = [
            'Director Académico',
            'Coordinador de Admisiones',
            'Jefe de Carrera',
            'Secretario Académico',
            'Coordinador de Alumnos',
            'Profesor',
            'Asistente Administrativo',
            'Coordinador de Prácticas',
            'Jefe de Departamento',
            'Orientador Educacional'
        ];

        return $positions[array_rand($positions)];
    }

    private function getRandomEmail(): string
    {
        $domains = ['gmail.com', 'hotmail.com', 'yahoo.com', 'outlook.com'];
        $prefixes = ['contacto', 'info', 'admision', 'coordinacion', 'secretaria'];

        return $prefixes[array_rand($prefixes)] . rand(1, 999) . '@' . $domains[array_rand($domains)];
    }

    private function getRandomPhone(): string
    {
        return '+56 9 ' . rand(1000, 9999) . ' ' . rand(1000, 9999);
    }

    private function generateCode(string $name): string
    {
        // Generar código único basado en el nombre
        $words = explode(' ', $name);
        $code = '';

        foreach ($words as $word) {
            $code .= strtoupper(substr($word, 0, 1));
        }

        // Agregar números aleatorios si es necesario para asegurar unicidad
        $code .= rand(10, 99);

        return $code;
    }

    private function normalizeType(string $type): string
    {
        // Normalizar tipos para que coincidan con el enum de la migración
        $typeMap = [
            'Universidad' => 'universidad',
            'Instituto' => 'instituto',
            'Colegio' => 'colegio',
            'Centro Educativo' => 'centro_educativo',
            'Otro' => 'otro',
        ];

        return $typeMap[$type] ?? 'universidad';
    }
}