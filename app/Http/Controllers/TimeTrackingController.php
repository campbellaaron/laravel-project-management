<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeEntry;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TimeTrackingController extends Controller
{
    public function startTimer(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        $user = Auth::user();

        // Check if there's an active timer
        $existingEntry = TimeEntry::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->whereNull('ended_at')
            ->first();

        if (!$existingEntry) {
            TimeEntry::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'started_at' => now(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Timer started']);
    }

    public function stopTimer(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        $user = Auth::user();

        $timeEntry = TimeEntry::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->whereNull('ended_at')
            ->first();

        if ($timeEntry) {
            $timeEntry->update(['ended_at' => now()]);
        }

        return response()->json(['success' => true, 'message' => 'Timer stopped']);
    }
}
