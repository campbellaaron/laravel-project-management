@extends('layouts.app')

@section('title', 'Time Tracking Reports')

@section('content')
<div class="container">
    <h2 class="text-2xl font-bold mb-4">Time Tracking Reports</h2>

    <form method="GET" action="{{ route('reports.time_logs') }}" class="mb-4 flex gap-4 flex-wrap">
        <select name="user_id" class="border p-2">
            <option value="">All Users</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                    {{ $user->full_name }}
                </option>
            @endforeach
        </select>

        <select name="project_id" class="border p-2">
            <option value="">All Projects</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                    {{ $project->name }}
                </option>
            @endforeach
        </select>

        <input type="date" name="start_date" class="border p-2"
            value="{{ request('start_date') }}" placeholder="Start Date">

        <input type="date" name="end_date" class="border p-2"
            value="{{ request('end_date') }}" placeholder="End Date">

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
        <a href="{{ route('reports.export_csv', request()->query()) }}" class="bg-green-500 text-white px-4 py-2 rounded">Export CSV</a>
    </form>

    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border px-4 py-2">User</th>
                <th class="border px-4 py-2">Task</th>
                <th class="border px-4 py-2">Project</th>
                <th class="border px-4 py-2">Start Time</th>
                <th class="border px-4 py-2">End Time</th>
                <th class="border px-4 py-2">Duration</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($timeEntries as $entry)
                <tr class="bg-gray-300 dark:bg-gray-700 text-slate-800 dark:text-slate-300">
                    <td class="border px-4 py-2">{{ $entry->user->full_name }}</td>
                    <td class="border px-4 py-2">{{ $entry->task->title }}</td>
                    <td class="border px-4 py-2">{{ $entry->task->project->name }}</td>
                    <td class="border px-4 py-2">{{ $entry->started_at->timezone(auth()->user()->timezone)->format('F d, Y h:i A') }}</td>
                    <td class="border px-4 py-2">{{ $entry->ended_at->timezone(auth()->user()->timezone)->format('F d, Y h:i A') ?? 'Ongoing' }}</td>
                    <td class="border px-4 py-2">
                        @if ($entry->ended_at)
                            {{ gmdate("H:i:s", $entry->ended_at->diffInSeconds($entry->started_at)) }}
                        @else
                            Running
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
