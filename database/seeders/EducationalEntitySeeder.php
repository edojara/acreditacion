<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EducationalEntity;

class EducationalEntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['universidad', 'instituto', 'colegio', 'centro_educativo', 'otro'];
        $regions = [
            'Metropolitana', 'Valparaíso', 'Biobío', 'Maule', 'Ñuble',
            'Araucanía', 'Los Ríos', 'Los Lagos', 'Aysén', 'Magallanes',
            'Arica y Parinacota', 'Tarapacá', 'Antofagasta', 'Atacama', 'Coquimbo'
        ];

        $cities = [
            'Santiago', 'Valparaíso', 'Concepción', 'La Serena', 'Antofagasta',
            'Iquique', 'Temuco', 'Rancagua', 'Talca', 'Chillán', 'Puerto Montt',
            'Coyhaique', 'Punta Arenas', 'Copiapó', 'Arica', 'Vallenar', 'Calama',
            'Ovalle', 'Linares', 'Curicó', 'Los Ángeles', 'Villarrica', 'Osorno',
            'Castro', 'Quillota', 'San Felipe', 'Los Andes', 'San Antonio', 'Pichilemu'
        ];

        $universityNames = [
            'Universidad de Chile', 'Pontificia Universidad Católica', 'Universidad de Santiago',
            'Universidad Técnica Federico Santa María', 'Universidad Adolfo Ibáñez',
            'Universidad Diego Portales', 'Universidad Andrés Bello', 'Universidad del Desarrollo',
            'Universidad Metropolitana de Ciencias de la Educación', 'Universidad de Valparaíso',
            'Universidad de Concepción', 'Universidad del Bío-Bío', 'Universidad de La Serena',
            'Universidad de Antofagasta', 'Universidad de Tarapacá', 'Universidad de Magallanes',
            'Universidad Austral de Chile', 'Universidad de La Frontera', 'Universidad de Talca',
            'Universidad Católica del Maule', 'Universidad de Los Lagos', 'Universidad Arturo Prat',
            'Universidad de Playa Ancha', 'Universidad Bernardo O\'Higgins', 'Universidad de Aysén'
        ];

        $instituteNames = [
            'Instituto Profesional AIEP', 'Instituto Profesional DUOC UC', 'Instituto Profesional IPG',
            'Instituto Profesional Virginio Gómez', 'Instituto Profesional Los Leones',
            'Instituto Profesional Providencia', 'Instituto Profesional Chileno Británico',
            'Instituto Profesional La Araucana', 'Instituto Profesional Santo Tomás',
            'Instituto Profesional INACAP', 'Instituto Profesional ESUCOMEX'
        ];

        $schoolNames = [
            'Liceo Nacional', 'Colegio San Ignacio', 'Colegio Santa María', 'Colegio Champagnat',
            'Colegio Los Andes', 'Colegio San José', 'Colegio Sagrados Corazones',
            'Colegio Alemán', 'Colegio Francés', 'Colegio Italiano', 'Colegio Hebreo',
            'Colegio Tabancura', 'Colegio Craighouse', 'Colegio Saint George',
            'Colegio San Pedro Nolasco', 'Colegio Seminario Conciliar'
        ];

        $centerNames = [
            'Centro Educativo Municipal', 'Centro de Formación Técnica', 'Centro de Capacitación Laboral',
            'Centro Educativo Técnico', 'Centro de Desarrollo Educativo', 'Centro de Aprendizaje',
            'Centro Educativo Comunitario', 'Centro de Formación Profesional'
        ];

        $nameSources = [
            'universidad' => $universityNames,
            'instituto' => $instituteNames,
            'colegio' => $schoolNames,
            'centro_educativo' => $centerNames,
            'otro' => array_merge($universityNames, $instituteNames, $schoolNames, $centerNames)
        ];

        // Crear 150 entidades educativas aleatorias (evitando duplicados)
        $createdCount = 0;
        $maxAttempts = 200; // Límite de intentos para evitar bucles infinitos
        $attempts = 0;

        while ($createdCount < 150 && $attempts < $maxAttempts) {
            $attempts++;
            $type = $types[array_rand($types)];
            $region = $regions[array_rand($regions)];
            $city = $cities[array_rand($cities)];

            $nameSource = $nameSources[$type];
            $baseName = $nameSource[array_rand($nameSource)];

            // Agregar variaciones aleatorias para evitar duplicados
            $suffixes = [
                ' Centro', ' Norte', ' Sur', ' Oriente', ' Poniente', ' Campus Principal',
                ' Sede ' . chr(65 + rand(0, 25)), // A-Z aleatorio
                ' Unidad ' . rand(1, 10),
                ' ' . rand(100, 999), // Número aleatorio
                ' Regional', ' Local', ' Central'
            ];

            $randomSuffix = $suffixes[array_rand($suffixes)];
            $finalName = $baseName . $randomSuffix;

            // Verificar si el nombre ya existe
            $existingEntity = EducationalEntity::where('name', $finalName)->first();
            if ($existingEntity) {
                continue; // Intentar con otro nombre
            }

            // Generar datos aleatorios
            $phone = '+56 9 ' . rand(10000000, 99999999);
            $email = strtolower(str_replace([' ', '\'', 'á', 'é', 'í', 'ó', 'ú', 'ñ'], ['_', '', 'a', 'e', 'i', 'o', 'u', 'n'], $finalName)) . '@edu.cl';
            $website = 'www.' . strtolower(str_replace([' ', '\'', 'á', 'é', 'í', 'ó', 'ú', 'ñ'], ['', '', 'a', 'e', 'i', 'o', 'u', 'n'], $finalName)) . '.cl';

            EducationalEntity::create([
                'name' => $finalName,
                'type' => $type,
                'address' => 'Dirección ' . ($createdCount + 1) . ', ' . $city,
                'city' => $city,
                'region' => $region,
                'country' => 'Chile',
                'phone' => $phone,
                'email' => $email,
                'website' => $website,
            ]);

            $createdCount++;
        }

        // Mostrar resultado
        echo "Se crearon {$createdCount} entidades educativas nuevas.\n";
        if ($attempts >= $maxAttempts) {
            echo "Se alcanzó el límite máximo de intentos ({$maxAttempts}).\n";
        }
    }
}