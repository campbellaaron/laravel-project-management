<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('tasks.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

Route::middleware('auth')->group(function () {
    // Task resource route will automatically generate the 'create', 'store', 'edit', 'update', etc.
    Route::resource('tasks', TaskController::class);
    Route::resource('users', UserController::class);
    Route::resource('projects', ProjectsController::class);

    // Notifications routes
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    // Profile and user routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
