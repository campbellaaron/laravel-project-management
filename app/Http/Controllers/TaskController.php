<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\TimeEntry;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Comment;
use Carbon\Carbon;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskReassigned;
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
            $tasks = Task::with(['assignedTo', 'project'])->where('assigned_to', auth()->id())->where('completed', false)->get();
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
    // Log the incoming request data to check if all the fields are being submitted correctly
    \Log::info('Request data: ', $request->all());

    // Validate the request
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'assigned_to' => 'required|exists:users,id',
        'due_date' => 'nullable|date',
        'priority' => 'required|in:low,medium,high,urgent',
        'project_id' => 'required|exists:projects,id',
    ]);

    // Create the task
    try {
        $task = Task::create($request->all());

        \Log::info('Task created: ' . $task->title);  // Log after task is created successfully

        // Log the activity for task creation
        Activity::create([
            'user_id' => auth()->id(),
            'description' => auth()->user()->first_name . ' created a new task: ' . $task->title,
        ]);

        \Log::info('Activity logged for task creation: ' . $task->title);  // Log activity creation

        // Continue with other activity logs (task assignment, etc.)
        // Similar to above, log other activity actions:
        $user = User::find($request->assigned_to);

        Activity::create([
            'user_id' => auth()->id(),
            'description' => auth()->user()->first_name . ' assigned task: ' . $task->title . ' to ' . $user->full_name,
        ]);

        \Log::info('Activity logged for task assignment: ' . $task->title . ' to ' . $user->name);

        // Send a notification to the assigned user
        $user->notify(new TaskAssigned($task));

        \Log::info('Notification sent to: ' . $user->name);

        // Redirect back to the task list or show a success message
        return redirect()->route('tasks.show', $task->id)->with('success', 'Task created with ID: ' . $task->task_key . ', and assignee notified.');

    } catch (\Exception $e) {
        \Log::error("Error creating task or logging activity: " . $e->getMessage());  // Log error if something fails
        return redirect()->route('tasks.create')->with('error', 'There was an error creating the task.');
    }
}

// Display the specified task
    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    // Show the form for editing the specified task
    public function edit(Task $task)
    {
        $users = User::all(); // Get all users to reassign the task
        $projects = Project::all();

        return view('tasks.edit', compact('task', 'users', 'projects'));
    }

    // Update the specified task in the database
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date',
            'project_id' => 'required|exists:projects,id',
        ]);

        // Store the old assignee before updating
        $oldAssigneeId = $task->assigned_to;

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to' => $request->assigned_to,
            'due_date' => $request->due_date,
            'project_id' => $request->project_id,
        ]);

        // Log the activity for task creation
        Activity::create([
            'user_id' => auth()->id(),
            'description' => auth()->user()->first_name . ' updated the task: ' . $task->title,
        ]);

        // Check if the task was reassigned
        if ($oldAssigneeId != $request->assigned_to) {
            $newAssignee = User::find($request->assigned_to);
            $oldAssignee = User::find($oldAssigneeId);

            // Notify the new assignee
            $newAssignee->notify(new TaskAssigned($task));

            // Optionally notify the old assignee
            if ($oldAssignee) {
                $oldAssignee->notify(new TaskReassigned($task));
            }

            Activity::create([
                'user_id' => auth()->id(),
                'description' => auth()->user()->first_name . ' reassigned task: ' . $task->title . ' to ' . $newAssignee->full_name,
            ]);
        }

        return redirect()->route('tasks.show', $task->id)->with('success', 'Task updated successfully!');
    }

    public function complete(Task $task)
    {
        $task->completed = !$task->completed;
        $task->save();

        // Log the activity for task creation
        Activity::create([
            'user_id' => auth()->id(),
            'description' => auth()->user()->first_name . ' marked the task ' . $task->title . ' as complete',
        ]);

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

        // Log the activity for task creation
        Activity::create([
            'user_id' => auth()->id(),
            'description' => auth()->user()->first_name . ' commented on ' . $task->title,
        ]);

        return redirect()->route('tasks.show', $task);
    }

    public function isTracking(Task $task)
    {
        $runningEntry = $task->timeEntries()->whereNull('ended_at')->latest()->first();

        return response()->json([
            'is_tracking' => (bool) $runningEntry,
            'started_at' => $runningEntry ? strtotime($runningEntry->started_at) * 1000 : null, // Convert to JS timestamp
        ]);

    }

    public function addManualTime(Request $request, Task $task)
    {
        $request->validate([
            'manual_hours' => 'nullable|integer|min:0',
            'manual_minutes' => 'nullable|integer|min:0|max:59',
        ]);

        // Convert hours & minutes into total seconds
        $totalSeconds = ($request->manual_hours * 3600) + ($request->manual_minutes * 60);

        if ($totalSeconds > 0) {
            $task->timeEntries()->create([
                'user_id' => auth()->id(),
                'started_at' => now()->subSeconds($totalSeconds), // Approximate backdating
                'ended_at' => now(),
                'description' => 'Manual entry',
            ]);
        }

        return redirect()->route('tasks.show', $task->id)->with('success', 'Time added successfully!');
    }

    public function updateTimeEntry(Request $request, TimeEntry $entry)
    {
        // Ensure only the user who logged time or an admin can edit
        if (auth()->user()->id !== $entry->user_id && !auth()->user()->hasAnyRole(['admin', 'super-admin'])) {
            return redirect()->back()->with('error', 'You do not have permission to edit this entry.');
        }

        $request->validate([
            'hours' => 'nullable|integer|min:0',
            'minutes' => 'nullable|integer|min:0|max:59',
        ]);

        // Convert to seconds
        $totalSeconds = ($request->hours * 3600) + ($request->minutes * 60);

        if ($totalSeconds > 0) {
            $entry->started_at = now()->subSeconds($totalSeconds);
            $entry->ended_at = now();
            $entry->save();
        }

        return redirect()->back()->with('success', 'Time entry updated successfully.');
    }

    public function deleteTimeEntry(TimeEntry $entry)
    {
        // Ensure only the user who logged time or an admin can delete
        if (auth()->user()->id !== $entry->user_id && !auth()->user()->hasAnyRole(['admin', 'super-admin'])) {
            return redirect()->back()->with('error', 'You do not have permission to delete this entry.');
        }

        $entry->delete();

        return redirect()->back()->with('success', 'Time entry deleted successfully.');
    }


    public function totalTime(Task $task)
    {
        return response()->json([
            'total' => $task->totalTrackedTime()
        ]);
    }


    // Remove the specified task from the database
    public function destroy(Task $task)
    {
        $task->delete();

        // Log the activity for task creation
        Activity::create([
            'user_id' => auth()->id(),
            'description' => auth()->user()->first_name . ' deleted task: ' . $task->title,
        ]);
        return redirect()->route('tasks.index');
    }
}
