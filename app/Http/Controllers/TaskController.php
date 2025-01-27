<?php

namespace App\Http\Controllers;

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
        $tasks = Task::with('assignedTo')->get();
        return view('tasks.index', compact('tasks'));
    }

    // Show the form for creating a new task
    public function create()
    {
        // Fetch all users to assign the task
        $users = User::all();

        // Return the view to create a task
        return view('tasks.create', compact('users'));
    }


    // Store a newly created task in the database
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|dateTime',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'due_date' => $request->due_date,
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

        return redirect()->route('tasks.index');
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
