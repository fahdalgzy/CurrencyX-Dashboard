<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permissions
        $this->command->warn(PHP_EOL . 'Creating set of permission for roles...');
        Artisan::call('permissions:sync -C -Y');
        $this->command->info('Sets of permissions has been created.');

        // Roles
        /* Admin Role */
        $this->command->warn(PHP_EOL . 'Creating admin role...');
        $role = Role::create(['name' => 'Admin']);
        $role->givePermissionTo(Permission::all());
        $this->command->info('Admin role has been created.');

        $superadmin  = User::create([
            'name' => 'Admin',
            'email' => 'admin@database.com',
            'password' => Hash::make('123456'),
        ]);
        $superadmin->assignRole('Admin');
        $superadmin->givePermissionTo(Permission::all());

        /* Normal User Role */
        $this->command->warn(PHP_EOL . 'Creating normal user role...');
        $role = Role::create(['name' => 'Normal User']);
        $permissions = Permission::query();
        $excludedPermission = ['User'];
        foreach ($excludedPermission as $value) {
            $permissions = $permissions->where('name', 'like', '%' . $value);
        }

        $role->givePermissionTo($permissions->get('name')->toArray());
        $this->command->info('Normal User role has been created.');


        $useradmin  = User::create([
            'name' => 'Normal User',
            'email' => 'user@database.com',
            'password' => Hash::make('123456'),
        ]);
        $useradmin->assignRole('Normal User');
        $useradmin->givePermissionTo($permissions->get('name')->toArray());
    }
}
