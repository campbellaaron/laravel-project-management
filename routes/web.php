<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])  // Ensure the user is authenticated and email is verified
    ->name('dashboard');

Route::middleware('can:create-roles')->group(function () {
    Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
});

Route::middleware(['auth', 'role:admin|super-admin'])->group(function () {
    Route::resource('users', UserController::class); // Only admins and super-admins can access users
});

Route::group(['middleware'=> 'role:admin|super-admin|manager'], function () {
    // Block 'user' role from creating or editing projects
    Route::get('projects/create', [ProjectsController::class, 'create'])->middleware('role:admin|super-admin|manager')->name('projects.create');
    Route::get('projects/{project}/edit', [ProjectsController::class, 'edit'])->middleware('role:admin|super-admin|manager')->name('projects.edit');
    Route::patch('/projects/{project}/status', [ProjectsController::class, 'updateStatus'])->middleware('role:admin|super-admin|manager')->name('projects.updateStatus');

});

Route::middleware('auth')->group(function () {
    // Allow all users to view a project
    Route::get('projects/{project}', [ProjectsController::class, 'show'])->name('projects.show');

    // Restrict other routes to certain roles (only admin, super-admin, or manager)
    Route::middleware('role:admin|super-admin|manager')->group(function () {
        Route::resource('projects', ProjectsController::class)->except('show');
    });

    // Allow 'user' to view projects
    Route::get('projects', [ProjectsController::class, 'index'])->name('projects.index');

    // Task resource route will automatically generate the 'create', 'store', 'edit', 'update', etc.
    Route::resource('tasks', TaskController::class);
    Route::resource('roles', RoleController::class);
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::post('/tasks/{task}/comments', [TaskController::class, 'storeComment'])->name('tasks.storeComment');



    // Notifications routes
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    // Profile and user routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
