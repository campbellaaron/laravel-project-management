<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function __construct()
    {

    }
    public function index()
    {
        $users = User::all();
        return view("users.index", compact("users"));
    }

    public function show(User $user)
    {
        $roles = $user->getRoleNames();
        return view("users.show", compact('user', 'roles'));
    }

    public function showUserRole($id)
    {
        // Fetch the user by ID
        $user = User::findOrFail($id);

        // Get all roles assigned to the user
        $roles = $user->getRoleNames();

        // Return a view with the user and roles
        return view('users.show', compact('user', 'roles'));
    }
    public function create()
    {
        $roles = Role::all();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:8', // Minimum 8 characters
                'regex:/[a-zA-Z]/', // At least one letter
                'regex:/[0-9]/', // At least one number
                'regex:/[@$!%*?&]/', // At least one special character
                'password.regex' => 'The password must contain at least one letter, one number, and one special character.',
            ],
            'role' => 'required|string|exists:roles,name',
        ];

        // If the user is not an admin, enforce password confirmation
        if (!auth()->user()->hasRole('admin')) {
            $rules['password'] = 'required|string|confirmed|min:8';
        }

        $request->validate($rules);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole($request->role);

        \Log::info($request->all());

        // Redirect or return a response
        return redirect()->route('users.show', $user->id)->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|exists:roles,name',
        ]);

        $user->update($request->only(['first_name', 'last_name', 'email']));

        $user->syncRoles($request->role);  // Update user role

        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index');
    }
}
