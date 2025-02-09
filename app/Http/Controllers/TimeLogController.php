<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TimeEntry;
use App\Models\Task;
use App\Models\User;

class TimeLogController extends Controller
{
    public function index()
    {
        $timeEntries = TimeEntry::with(['task', 'user'])->latest()->get();
        return view('admin.time_logs.index', compact('timeEntries'));
    }

    public function edit($id)
    {
        $timeEntry = TimeEntry::findOrFail($id);
        return view('admin.time_logs.edit', compact('timeEntry'));
    }

    public function update(Request $request, $id)
    {
        $timeEntry = TimeEntry::findOrFail($id);
        $timeEntry->update([
            'started_at' => $request->started_at,
            'ended_at' => $request->ended_at,
        ]);

        return redirect()->route('admin.time-logs.index')->with('success', 'Time log updated successfully.');
    }

    public function destroy($id)
    {
        $timeEntry = TimeEntry::findOrFail($id);
        $timeEntry->delete();

        return redirect()->route('admin.time-logs.index')->with('success', 'Time log deleted.');
    }

}
