<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Task;
use App\Models\Project;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Get unread notifications for the authenticated user
        $notifications = $user->unreadNotifications;

        // Fetch the latest incomplete tasks assigned to the current user or all tasks if the user is an admin
        if ($user->hasAnyRole('admin|super-admin|manager')) {
            // If the user is an admin or super-admin, show all incomplete tasks
            $latestTasks = Task::where('completed', false)->latest()->take(5)->get();
        } else {
            // If the user is not an admin, only show the tasks assigned to them
            $latestTasks = Task::where('assigned_to', $user->id)
                            ->where('completed', false)
                            ->latest()
                            ->take(5)
                            ->get();
        }

        // Get the newest users (last 5 registered or created)
        $newUsers = User::orderBy('created_at','desc')->take(5)->get();

        // Get recent activity for the user (for example, tasks they've been involved with)
        $recentActivity = Activity::latest()->take(5)->get(); // Fetch the latest 5 activities

        // Currently Open/In-Progress Projects
        $projects = Project::withCount(['tasks' => function ($query) {
            $query->where('completed', true);
        }])->get();

        // Completed Task stats
        $completedTasksCount = Task::where('completed', true)->count();

        // Pass notifications and other data to the view
        return view('dashboard', compact('notifications', 'latestTasks', 'newUsers', 'recentActivity', 'completedTasksCount', 'projects'));
    }
}
