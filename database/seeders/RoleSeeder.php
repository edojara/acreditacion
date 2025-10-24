<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrador',
                'slug' => 'admin',
                'description' => 'Acceso completo al sistema',
                'permissions' => [
                    'manage_users',
                    'manage_roles',
                    'view_reports',
                    'manage_accreditations',
                    'system_settings'
                ]
            ],
            [
                'name' => 'Solo Lectura',
                'slug' => 'solo-lectura',
                'description' => 'Vista de solo lectura de datos',
                'permissions' => [
                    'view_data',
                    'view_reports'
                ]
            ],
            [
                'name' => 'Informe',
                'slug' => 'informe',
                'description' => 'Acceso a reportes y estadísticas',
                'permissions' => [
                    'view_reports',
                    'export_reports',
                    'generate_charts'
                ]
            ],
            [
                'name' => 'Enrolador',
                'slug' => 'enrolador',
                'description' => 'Gestión de usuarios y procesos de acreditación',
                'permissions' => [
                    'manage_users',
                    'manage_accreditations',
                    'view_reports'
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }

        // Crear usuario administrador por defecto
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@acreditacion.com',
                'password' => bcrypt('Abcd.1234'),
                'role_id' => $adminRole->id,
                'must_change_password' => true,
                'email_verified_at' => now(),
            ]);
        }
    }
}