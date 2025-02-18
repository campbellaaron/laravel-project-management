<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // Fetch latest tasks based on roles
        if ($user->hasAnyRole('admin|super-admin|manager')) {
            $latestTasks = Task::whereNotIn('status', ['Completed'])->latest()->take(5)->get();
        } else {
            $latestTasks = Task::where('assigned_to', $user->id)
                            ->whereNotIn('status', ['Completed'])
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
            $query->where('status', 'Completed');
        }])->get();

        // Count of tasks per status
        $taskStatusCounts = Task::select('status', DB::raw('count(*) as count'))
        ->groupBy('status')
        ->pluck('count', 'status');

        // Count of completed tasks
        $completedTasksCount = Task::where('status', 'Completed')->count();

        // Productivity: Count tasks completed per user
        $userProductivity = User::withCount(['tasks as completed_tasks' => function ($query) {
            $query->where('status', 'Completed');
        }])->get()->pluck('completed_tasks', 'full_name');

        // **New Feature**: Calculate Average Completion Time (in hours)
        $averageCompletionTime = Task::where('status', 'Completed')
        ->whereNotNull('created_at')
        ->whereNotNull('updated_at')
        ->select(DB::raw("AVG(TIMESTAMPDIFF(SECOND, created_at, updated_at)) as avg_time"))
        ->value('avg_time');

        // Pass notifications and other data to the view
        return view('dashboard', compact(
            'notifications',
            'latestTasks',
            'newUsers',
            'recentActivity',
            'completedTasksCount',
            'taskStatusCounts',
            'projects',
            'userProductivity',
            'averageCompletionTime'
        ));
    }
}
