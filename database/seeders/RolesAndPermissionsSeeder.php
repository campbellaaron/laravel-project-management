<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Permissions
        $permissions = [
            'create users',
            'edit users',
            'delete users',
            'assign roles',
            'create roles',
            'edit roles',
            'delete roles',
            'create projects',
            'edit projects',
            'delete projects',
            'create tasks',
            'edit tasks',
            'delete tasks',
            'complete tasks', // Example permission
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles
        $superAdminRole = Role::create(['name' => 'super-admin']);
        $adminRole = Role::create(['name' => 'admin']);
        $managerRole = Role::create(['name'=> 'manager']);
        $userRole = Role::create(['name' => 'user']);

        // Assign Permissions to Roles
        $superAdminRole->givePermissionTo(Permission::all());  // Super admin gets all permissions
        $adminRole->givePermissionTo(['create users', 'edit users', 'create projects', 'edit projects', 'create tasks', 'edit tasks', 'create roles']); // Admin has more permissions but not delete or assign roles
        $managerRole->givePermissionTo(['create projects', 'edit projects', 'create tasks', 'edit tasks']);
        $userRole->givePermissionTo(['create tasks', 'edit tasks']); // Basic user permissions for tasks

        // Optionally, you can assign a super-admin role to a user (example)
        $superAdminFactoryUser = [1, "Super Admin", "admin@lickbeansinteractive.com", 'Password1!'];
        User::create($superAdminFactoryUser);
        $superAdmin = User::find(1); // Assuming user ID 1 is the super admin
        $superAdmin->assignRole('super-admin');
    }
}
