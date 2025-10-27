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

        // Crear 150 entidades educativas aleatorias
        for ($i = 0; $i < 150; $i++) {
            $type = $types[array_rand($types)];
            $region = $regions[array_rand($regions)];
            $city = $cities[array_rand($cities)];

            $nameSource = $nameSources[$type];
            $baseName = $nameSource[array_rand($nameSource)];

            // Agregar variaciones para evitar duplicados
            $suffixes = ['', ' Centro', ' Norte', ' Sur', ' Oriente', ' Poniente', ' Campus Principal'];
            $baseName .= $suffixes[array_rand($suffixes)];

            // Generar datos aleatorios
            $phone = '+56 9 ' . rand(10000000, 99999999);
            $email = strtolower(str_replace([' ', '\'', 'á', 'é', 'í', 'ó', 'ú', 'ñ'], ['_', '', 'a', 'e', 'i', 'o', 'u', 'n'], $baseName)) . '@edu.cl';
            $website = 'www.' . strtolower(str_replace([' ', '\'', 'á', 'é', 'í', 'ó', 'ú', 'ñ'], ['', '', 'a', 'e', 'i', 'o', 'u', 'n'], $baseName)) . '.cl';

            EducationalEntity::create([
                'name' => $baseName,
                'type' => $type,
                'address' => 'Dirección ' . ($i + 1) . ', ' . $city,
                'city' => $city,
                'region' => $region,
                'country' => 'Chile',
                'phone' => $phone,
                'email' => $email,
                'website' => $website,
            ]);
        }
    }
}