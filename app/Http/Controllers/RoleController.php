<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;


class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $role_permissions = Permission::all();
        $users = User::select('id','name')->get();
        return view('roles.create', compact('role_permissions','users'));
    }

    public function edit(Role $role)
    {
        $role_permissions = Permission::all();
        $users = User::select('id','name')->get();
        return view('roles.edit', compact('role', 'role_permissions', 'users'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'role_name' => 'required|string|unique:roles,name,' . $role->id . '|max:255',
        ]);

        // Update the role with new data
        $role->update([
            'name' => $request->role_name,
        ]);

        // Assign new permissions if provided
        if ($request->has('permission')) {
            $role->syncPermissions($request->permission);  // Sync selected permissions with the role
        }

        return redirect()->route('roles.index')->with('success', 'Role has been updated successfully');
    }

    public function addPermissions(Request $request)
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
            'complete tasks',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }

    public function store(Request $request)
    {
        // Validate the role name
        $request->validate([
            'role_name' => 'required|string|unique:roles,name|max:255',
        ]);

        // Check permission using Gate
        if (Gate::denies('create-roles')) {
            abort(403, 'Unauthorized action.');
        }

        // Create the new role
        $role = Role::create(['name' => $request->role_name]);
        foreach($request->permission as $permission)
        {
            $role->givePermissionTo($permission);
        }

        foreach($request->users as $userId)
        {
            $user = User::find($userId);
            if ($user) {
                $user->assignRole($role->name);
            }
        }

        \Log::info($request->all());

        // Redirect to a list of roles or success page
        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }
}
