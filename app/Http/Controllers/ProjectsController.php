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
        $projects = Project::all();
        return view('projects.index', compact('projects'));
    }

    // Show the form for creating a new project
    public function create()
    {
        return view('projects.create');
    }

    // Store a newly created project in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:open,in-progress,completed',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

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
        return view('projects.edit', compact('project'));
    }

    // Update the specified project in the database
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:open,in-progress,completed',
        ]);

        $project->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        // When a project is updated
        Activity::create([
            'user_id' => auth()->id(),
            'description' => auth()->user()->first_name . ' updated project: ' . $project->name,
        ]);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully!');
    }

    public function updateStatus(Request $request, Project $project)
    {
        $request->validate([
            'status'=> ['required|in:open,in-progress,completed'],
        ]);

        $project->update(['status' => $request->status]);

        Activity::create([
            'user_id' => auth()->id(),
            'description'=> auth()->user()->first_name . ' changed the status of '. $project->name . ' to ' . $project->status,
        ]);

        return response()->json([
            'status' => $project->status,
            'message' => 'Project status updated successfully',
        ]);
    }

    // Remove the specified project from the database
    public function destroy(Project $project)
    {
        $project->delete();

        // When a project is deleted
        Activity::create([
            'user_id' => auth()->id(),
            'description' => 'Deleted project: ' . $project->name,
        ]);
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
    }
}
