<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TimeTrackingController;
use App\Http\Controllers\TimeReportController;
use App\Http\Controllers\TimeLogController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
    Route::resource('users', UserController::class);
    Route::resource('teams', TeamController::class);
    Route::get('/time-logs', [TimeLogController::class, 'index'])->name('admin.time-logs.index');
    Route::get('/time-logs/{id}/edit', [TimeLogController::class, 'edit'])->name('admin.time-logs.edit');
    Route::put('/time-logs/{id}', [TimeLogController::class, 'update'])->name('admin.time-logs.update');
    Route::delete('/time-logs/{id}', [TimeLogController::class, 'destroy'])->name('admin.time-logs.destroy');
    Route::get('/files', [FileUploadController::class, 'index'])->name('files.index');

});

Route::group(['middleware'=> 'role:admin|super-admin|manager'], function () {
    // Block 'user' role from creating or editing projects
    Route::get('projects/create', [ProjectsController::class, 'create'])->middleware('role:admin|super-admin|manager')->name('projects.create');
    Route::get('projects/{project}/edit', [ProjectsController::class, 'edit'])->middleware('role:admin|super-admin|manager')->name('projects.edit');
    Route::get('/reports/time-logs', [TimeReportController::class, 'index'])->name('reports.time_logs');
    Route::get('/reports/export-csv', [TimeReportController::class, 'exportCsv'])->name('reports.export_csv');
    Route::delete('/files/delete', [FileUploadController::class, 'destroy'])->name('files.delete');


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
    Route::patch('/projects/{project}/status', [ProjectsController::class, 'updateStatus'])->middleware('role:admin|super-admin|manager')->name('projects.updateStatus');
    Route::patch('/projects/{project}/assign-users', [ProjectsController::class, 'assignUsers'])
    ->middleware(['auth', 'role:admin|super-admin|manager'])
    ->name('projects.assignUsers');

    // Time tracking for tasks
    Route::post('/tasks/{task}/start-timer', [TimeTrackingController::class, 'startTimer'])->name('tasks.start-timer');
    Route::post('/tasks/{task}/stop-timer', [TimeTrackingController::class, 'stopTimer'])->name('tasks.stop-timer');
    Route::get('/tasks/{task}/total-time', function (Task $task) {
        return response()->json(['total' => $task->totalTrackedTime()]);
    });
    Route::get('/tasks/{task}/is-tracking', [TaskController::class, 'isTracking']);
    Route::get('/tasks/{task}/total-time', [TaskController::class, 'totalTime']);
    Route::post('/tasks/{task}/manual-time', [TaskController::class, 'addManualTime'])->name('tasks.addManualTime');
    Route::patch('/time-entries/{entry}/update', [TaskController::class, 'updateTimeEntry'])->name('tasks.updateTimeEntry');
    Route::delete('/time-entries/{entry}/delete', [TaskController::class, 'deleteTimeEntry'])->name('tasks.deleteTimeEntry');


    // Task resource route will automatically generate the 'create', 'store', 'edit', 'update', etc.
    Route::resource('tasks', TaskController::class);
    Route::resource('roles', RoleController::class);
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::post('/tasks/{task}/comments', [TaskController::class, 'storeComment'])->name('tasks.storeComment');

    // Notifications routes
    Route::post('/notifications/mark-as-read/{id?}', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    // Profile and user routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Image Upload Route
    Route::post('/upload-image', function (Request $request) {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|image|max:10240' // Max 10MB
        ]);

        // Store the file in 'public/uploads'
        $path = $request->file('file')->store('uploads', 'public');

        return response()->json([
            'location' => asset("storage/$path"), // The URL TinyMCE will use
        ]);
    });

    Route::post('/upload-file', [FileUploadController::class, 'store'])->name('upload.file');
});

require __DIR__.'/auth.php';
