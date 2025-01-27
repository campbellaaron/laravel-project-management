<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // Display a list of tasks
    public function index()
    {
        // Check if the user is a super admin or a normal user
        if (auth()->user()->is_admin) {
            // If super admin, show all tasks and eager load the 'assignedTo' and 'project' relationships
            $tasks = Task::with(['assignedTo', 'project'])->get();
        } else {
            // If normal user, show only tasks assigned to the logged-in user
            $tasks = Task::with(['assignedTo', 'project'])->where('assigned_to', auth()->id())->get();
        }
        return view('tasks.index', compact('tasks'));
    }

    // Show the form for creating a new task
    public function create()
    {
        // Fetch all users and projects to assign the task
        $users = User::all();
        $projects = Project::all();

        // Return the view to create a task
        return view('tasks.create', compact('users', 'projects'));
    }


    // Store a newly created task in the database
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
            'project_id' => 'required|exists:projects,id',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'due_date' => $request->due_date,
            'project_id' => $request->project_id,
        ]);


        // Find the user who was assigned the task
        $user = User::find($request->assigned_to);

        // Send a notification to the assigned user
        $user->notify(new TaskAssigned($task));

        // Redirect back to the task list or show a success message
        return redirect()->route('tasks.index')->with('success', 'Task created and user notified.');
    }

    // Display the specified task
    public function show(Task $task)
    {
        // Check if the user is authenticated
        if (auth()->check()) {
            // Mark unread notifications as read
            auth()->user()->unreadNotifications->markAsRead();
        }

        return view('tasks.show', compact('task'));
    }

    // Show the form for editing the specified task
    public function edit(Task $task)
    {
        $users = User::all(); // Get all users to reassign the task
        return view('tasks.edit', compact('task', 'users'));
    }

    // Update the specified task in the database
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('tasks.show', $task->id)->with('success', 'Task updated successfully!');
    }

    public function complete(Task $task)
    {
        $task->completed = !$task->completed;
        $task->save();

         // Redirect back to the task page
        return redirect()->route('tasks.show', $task->id)->with('success', 'Task status updated successfully.');
    }


    public function storeComment(Request $request, Task $task)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $task->comments()->create([
            'content' => $request->content,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('tasks.show', $task);
    }

    // Remove the specified task from the database
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index');
    }
}
