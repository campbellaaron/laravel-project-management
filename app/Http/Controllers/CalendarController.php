<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    public function fetchEvents()
    {
        $tasks = Task::whereNotNull('due_date')->get();
        $projects = Project::whereNotNull('due_date')->get();

        $events = [];

        foreach ($tasks as $task) {
            $events[] = [
                'id' => 'task_' . $task->id,
                'title' => '[Task] ' . $task->title,
                'start' => Carbon::parse($task->due_date)->toIso8601String(),
                'url' => route('tasks.show', $task->id),
                'backgroundColor' => 'blue',
            ];
        }

        foreach ($projects as $project) {
            $events[] = [
                'id' => 'project_' . $project->id,
                'title' => '[Project] ' . $project->name,
                'start' => Carbon::parse($project->start_date)->toIso8601String(),
                'end' => Carbon::parse($project->due_date)->addDay()->toIso8601String(),
                'url' => route('projects.show', $project->id),
                'backgroundColor' => 'orange',
                'display' => 'block',
            ];
        }

        return response()->json($events);
    }
}
