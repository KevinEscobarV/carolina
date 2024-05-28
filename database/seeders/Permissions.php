<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Permissions extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'Administrador']);

        // Create permissions
        $permissions = [
            [
                'name' => 'view.users',
                'description' => 'Ver usuarios',
            ],
            [
                'name' => 'create.users',
                'description' => 'Crear nuevos usuarios',
            ],
            [
                'name' => 'edit.users',
                'description' => 'Editar usuarios',
            ],
            [
                'name' => 'delete.users',
                'description' => 'Eliminar usuarios',
            ],
            [
                'name' => 'view.roles',
                'description' => 'Ver roles',
            ],
            [
                'name' => 'create.roles',
                'description' => 'Crear nuevos roles',
            ],
            [
                'name' => 'edit.roles',
                'description' => 'Editar roles',
            ],
            [
                'name' => 'delete.roles',
                'description' => 'Eliminar roles',
            ],
            [
                'name' => 'view.buyers',
                'description' => 'Ver compradores',
            ],
            [
                'name' => 'create.buyers',
                'description' => 'Crear nuevos compradores',
            ],
            [
                'name' => 'edit.buyers',
                'description' => 'Editar compradores',
            ],
            [
                'name' => 'delete.buyers',
                'description' => 'Eliminar compradores',
            ],
            [
                'name' => 'view.payments',
                'description' => 'Ver pagos',
            ],
            [
                'name' => 'create.payments',
                'description' => 'Crear nuevos pagos',
            ],
            [
                'name' => 'edit.payments',
                'description' => 'Editar pagos',
            ],
            [
                'name' => 'delete.payments',
                'description' => 'Eliminar pagos',
            ],
            [
                'name' => 'view.promises',
                'description' => 'Ver promesas',
            ],
            [
                'name' => 'create.promises',
                'description' => 'Crear nuevas promesas',
            ],
            [
                'name' => 'edit.promises',
                'description' => 'Editar promesas',
            ],
            [
                'name' => 'delete.promises',
                'description' => 'Eliminar promesas',
            ],
            [
                'name' => 'view.blocks',
                'description' => 'Ver manzanas',
            ],
            [
                'name' => 'create.blocks',
                'description' => 'Crear nuevas manzanas',
            ],
            [
                'name' => 'edit.blocks',
                'description' => 'Editar manzanas',
            ],
            [
                'name' => 'delete.blocks',
                'description' => 'Eliminar manzanas',
            ],
            [
                'name' => 'view.parcels',
                'description' => 'Ver lotes',
            ],
            [
                'name' => 'create.parcels',
                'description' => 'Crear nuevos lotes',
            ],
            [
                'name' => 'edit.parcels',
                'description' => 'Editar lotes',
            ],
            [
                'name' => 'delete.parcels',
                'description' => 'Eliminar lotes',
            ],
            [
                'name' => 'view.deeds',
                'description' => 'Ver escrituras',
            ],
            [
                'name' => 'create.deeds',
                'description' => 'Crear nuevas escrituras',
            ],
            [
                'name' => 'edit.deeds',
                'description' => 'Editar escrituras',
            ],
            [
                'name' => 'delete.deeds',
                'description' => 'Eliminar escrituras',
            ],
            [
                'name' => 'view.categories',
                'description' => 'Ver campañas',
            ],
            [
                'name' => 'create.categories',
                'description' => 'Crear nuevas campañas',
            ],
            [
                'name' => 'edit.categories',
                'description' => 'Editar campañas',
            ],
            [
                'name' => 'delete.categories',
                'description' => 'Eliminar campañas',
            ],
            [
                'name' => 'send.messages',
                'description' => 'Enviar mensajes',
            ],
            [
                'name' => 'send.messages.personalized',
                'description' => 'Enviar mensajes personalizados',
            ],
            [
                'name' => 'edit.settings',
                'description' => 'Editar configuraciones',
            ],
            [
                'name' => 'change.category',
                'description' => 'Cambiar de campaña',
            ]
        ];


        foreach ($permissions as $permission) {

            $permission['guard_name'] = 'web';

            Permission::firstOrCreate($permission);
        }

        $this->command->info('Permissions created successfully');
    }
}
