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
        // Create the super-admin user */
        $superAdminFactoryUser = [
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@lickbeansinteractive.com',
            'password' => bcrypt('superadminpassword'),
            'email_verified_at' => now(),
            'name' => 'SuperAdmin',
        ];

        $user = User::create($superAdminFactoryUser);
        $user->assignRole('super-admin');
        $testUser = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testemail@example.com',
            'password' => bcrypt('normaluserpassword'),
            'email_verified_at' => now(),
            'name' => 'Test User', // Still figuring out if this is required or not 0_o
        ]);
        $testUser->assignRole('user');
    }
}
