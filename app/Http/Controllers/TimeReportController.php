<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\Project;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class TimeReportController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all();
        $projects = Project::all();

        $query = TimeEntry::with(['task', 'user'])->orderBy('started_at', 'desc');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->project_id) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->where('project_id', $request->project_id);
            });
        }
        if ($request->start_date) {
            $query->whereDate('started_at', '>=', $request->start_date);
        }
        if ($request->end_date) {
            $query->whereDate('started_at', '<=', $request->end_date);
        }

        $timeEntries = $query->get();

        return view('reports.time_logs', compact('timeEntries', 'users', 'projects'));
    }

    public function exportCsv(Request $request)
    {
        $query = TimeEntry::with(['task', 'user'])->orderBy('started_at', 'desc');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->project_id) {
            $query->whereHas('task', function ($q) use ($request) {
                $q->where('project_id', $request->project_id);
            });
        }

        $timeEntries = $query->get();

        $csvData = "User,Task,Project,Start Time,End Time,Duration\n";

        foreach ($timeEntries as $entry) {
            $duration = $entry->ended_at ? gmdate("H:i:s", $entry->ended_at->diffInSeconds($entry->started_at)) : 'Running';
            $csvData .= "{$entry->user->full_name},{$entry->task->title},{$entry->task->project->name},{$entry->started_at},{$entry->ended_at},{$duration}\n";
        }

        $fileName = 'time_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        Storage::put('public/reports/' . $fileName, $csvData);


        return Response::download(storage_path("app/public/reports/{$fileName}"), $fileName);
    }
}
