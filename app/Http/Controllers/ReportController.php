<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Illuminate\Support\Facades\Storage;
use App\Models\Task;
use App\Models\Project;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function export(Request $request)
    {
        $type = $request->input('type');

        if ($type === 'tasks') {
            return $this->exportTasks();
        } elseif ($type === 'projects') {
            return $this->exportProjects();
        } else {
            return back()->with('error', 'Invalid report type.');
        }
    }

    private function exportTasks()
    {
        $fileName = 'tasks_report.csv';
        $filePath = storage_path('app/public/' . $fileName);

        $writer = SimpleExcelWriter::create($filePath)
            ->addHeader(['ID', 'Title', 'Status', 'Priority', 'Due Date', 'Created At']);

        Task::select('id', 'title', 'status', 'priority', 'due_date', 'created_at')
            ->chunk(500, function ($tasks) use ($writer) {
                foreach ($tasks as $task) {
                    $writer->addRow([
                        $task->id,
                        $task->title,
                        $task->status,
                        $task->priority,
                        $task->due_date,
                        $task->created_at
                    ]);
                }
            });

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    private function exportProjects()
    {
        $fileName = 'projects_report.csv';
        $filePath = storage_path('app/public/' . $fileName);

        $writer = SimpleExcelWriter::create($filePath)
            ->addHeader(['ID', 'Name', 'Start Date', 'Due Date', 'Status', 'Created At']);

        Project::select('id', 'name', 'start_date', 'due_date', 'status', 'created_at')
            ->chunk(500, function ($projects) use ($writer) {
                foreach ($projects as $project) {
                    $writer->addRow([
                        $project->id,
                        $project->name,
                        $project->start_date,
                        $project->due_date,
                        $project->status,
                        $project->created_at
                    ]);
                }
            });

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
