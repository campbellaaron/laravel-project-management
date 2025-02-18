<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Notifications\TaskAssigned;
use Illuminate\Http\Request;

class ProjectsController extends Controller
{
    // Display a list of tasks
    public function index()
    {
        $projects = Project::with(['projectLead', 'users', 'tasks', 'tasks.timeEntries'])->withCount('tasks')->get();

        return view('projects.index', compact('projects'));
    }

    // Show the form for creating a new project
    public function create()
    {
        $users = User::all();
        return view('projects.create', compact('users'));
    }

    // Store a newly created project in the database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:open,in-progress,completed',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'project_lead_id' => 'nullable|exists:users,id',
        ]);

        $project = Project::create($validated);

        // Assign Project Lead
        if ($request->has('project_lead_id')) {
            $project->update(['project_lead_id' => $request->project_lead_id]);
        }

        // Attach users with roles
        if ($request->has('team_members')) {
            foreach ($request->team_members as $userId => $data) {
                $role = $data['role'] ?? 'contributor';
                $project->users()->attach($userId, ['role' => $role]);
            }
        }

        // When a project is created
        Activity::create([
            'user_id' => auth()->id(),
            'description' => auth()->user()->first_name . ' created a new project: ' . $project->name,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project created successfully!');
    }

    // Display the specified project
    public function show(Project $project)
    {
        // Load the tasks associated with the project
        $tasks = $project->tasks;  // Eager loading tasks for the project

        return view('projects.show', compact('project', 'tasks'));
    }

    // Show the form for editing the specified project
    public function edit(Project $project)
    {
        $users = User::all(); // Fetch all users
        return view('projects.edit', compact('project', 'users'));
    }

    // Update the specified project in the database
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:open,in-progress,completed',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'project_lead_id' => 'nullable|exists:users,id',
        ]);

        $project->update($validated);

        // Update Project Lead
        $project->update(['project_lead_id' => $request->project_lead_id]);

        // Sync users with roles
        $assignedUsers = [];
        if ($request->has('team_members')) {
            foreach ($request->team_members as $userId => $data) {
                $role = $data['role'] ?? 'contributor';
                $assignedUsers[$userId] = ['role' => $role];
            }
        }
        $project->users()->sync($assignedUsers);

        if ($project->status == 'completed') {
            Activity::create([
                'user_id' => auth()->id(),
                'description' => auth()->user()->first_name . ' completed project: ' . $project->name,
            ]);
        } else {
            // When a project is updated
            Activity::create([
                'user_id' => auth()->id(),
                'description' => auth()->user()->first_name . ' updated project: ' . $project->name,
            ]);
        }

        return redirect()->route('projects.index')->with('success', 'Project updated successfully!');
    }

    public function updateStatus(Request $request, Project $project)
    {
        try {
            $request->validate([
                'status' => 'required|in:open,in-progress,completed',
            ]);

            // Check if the status has changed and update it
            $project->update(['status' => $request->status]);

            // Create an activity log for the status change
            if ($project->status == 'completed') {
                // When a project is completed
                Activity::create([
                    'user_id' => auth()->id(),
                    'description' => auth()->user()->first_name . ' marked project ' . $project->name . ' complete.',
                ]);
            } else {
                // When a project is updated
                Activity::create([
                    'user_id' => auth()->id(),
                    'description' => auth()->user()->first_name . ' changed ' . $project->name . ' status to "<strong>' . $project->formatted_status . '</strong>"',
                ]);
            }

            // Return success response
            return response()->json([
                'status' => $project->formatted_status,
                'message' => 'Project status updated successfully',
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            \Log::error("Error updating project status: " . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while updating the project status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function assignUsers(Request $request, Project $project)
    {
        $request->validate([
            'users' => 'required|array',
            'users.*.id' => 'exists:users,id',
            'users.*.role' => 'in:watcher,contributor'
        ]);

        // Sync users with roles
        $syncData = [];
        foreach ($request->users as $user) {
            $syncData[$user['id']] = ['role' => $user['role']];
        }

        $project->users()->sync($syncData);

        Activity::create([
            'user_id' => auth()->id(),
            'description' => auth()->user()->first_name . ' updated project assignments for ' . $project->name,
        ]);

        return redirect()->back()->with('success', 'Users assigned successfully.');
    }

    // Remove the specified project from the database
    public function destroy(Project $project)
    {
        $project->delete();

        // When a project is deleted
        Activity::create([
            'user_id' => auth()->id(),
            'description' => auth()->user()->first_name . ' deleted project: ' . $project->name,
        ]);
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
    }
}
