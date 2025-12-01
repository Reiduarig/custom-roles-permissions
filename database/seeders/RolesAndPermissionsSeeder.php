<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createPermissions();
        $this->createRoles();
        $this->assignPermissionsToRoles();
    }

    private function createPermissions(): void
    {
        $permissions = [
            ['name' => 'view_users', 'description' => 'Can view users'],
            ['name' => 'create_users', 'description' => 'Can create users'],
            ['name' => 'edit_users', 'description' => 'Can edit users'],
            ['name' => 'delete_users', 'description' => 'Can delete users'],
            ['name' => 'view_roles', 'description' => 'Can view roles'],
            ['name' => 'create_roles', 'description' => 'Can create roles'],
            ['name' => 'edit_roles', 'description' => 'Can edit roles'],
            ['name' => 'delete_roles', 'description' => 'Can delete roles'],
            ['name' => 'view_permissions', 'description' => 'Can view permissions'],
            ['name' => 'create_permissions', 'description' => 'Can create permissions'],
            ['name' => 'edit_permissions', 'description' => 'Can edit permissions'],
            ['name' => 'delete_permissions', 'description' => 'Can delete permissions'],
        ];

        foreach ($permissions as $permission) {
            $permission['created_at'] = now();
            $permission['updated_at'] = now();
        }

        DB::table('permissions')->insert($permissions);
    }

    private function createRoles(): void
    {
        $roles = [
            [
                'slug' => Role::SUPER_ADMIN, 
                'name' => 'Super Administrador', 
                'nivel' => Role::NIVEL_SUPER_ADMIN, 
                'description' => 'Has access to all system features',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => Role::ADMIN, 
                'name' => 'Administrador', 
                'nivel' => Role::NIVEL_ADMIN, 
                'description' => 'Has access to management features',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => Role::USER, 
                'name' => 'User', 
                'nivel' => Role::NIVEL_USER, 
                'description' => 'Has basic access',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('roles')->insert($roles);
    }

    private function assignPermissionsToRoles(): void
    {
       
        $superAdmin = Role::query()->where('slug', Role::SUPER_ADMIN)->firstOrFail();
        $admin = Role::query()->where('slug', Role::ADMIN)->firstOrFail();
        $user = Role::query()->where('slug', Role::USER)->firstOrFail();

        // SuperAdmin tiene todos los permisos
        $superAdmin->permissions()->sync(Permission::query()->pluck('id'));

        // Admin tiene permisos limitados
        $admin->permissions()->sync(
            Permission::query()
                ->whereIn('name', [
                    'view_users', 
                    'create_users', 
                    'edit_users',
                    'delete_users',
                    'view_roles',
                ])->pluck('id')
        );

        // User tiene permisos muy limitados
        $user->permissions()->sync(
            Permission::query()
                ->whereIn('name', [
                    'view_users', 
                ])->pluck('id')
        );
    }
}
