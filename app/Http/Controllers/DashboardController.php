<?php

namespace App\Http\Controllers;

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

        // Fetch the latest incomplete tasks (for the logged-in user, if needed)
        $latestTasks = Task::where('completed', false)->latest()->take(5)->get();

        // Get the newest users (last 5 registered or created)
        $newUsers = User::orderBy('created_at','desc')->take(5)->get();

        // $projects = list of active projects to be added after variables are fixed

        // Pass notifications and other data to the view
        return view('dashboard', compact('notifications', 'latestTasks', 'newUsers'));
    }
}
