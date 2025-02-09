@extends('layouts.app')

@section('title', 'Time Logs')

@section('content')
<div class="container">
    <h2 class="text-2xl font-bold mb-4">Time Logs</h2>

    <table class="table-auto w-full border-collapse border border-gray-300">
        <thead>
            <tr class="bg-gray-200">
                <th class="border px-4 py-2">User</th>
                <th class="border px-4 py-2">Task</th>
                <th class="border px-4 py-2">Start Time</th>
                <th class="border px-4 py-2">End Time</th>
                <th class="border px-4 py-2">Duration</th>
                <th class="border px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($timeEntries as $entry)
                <tr>
                    <td class="border px-4 py-2">{{ $entry->user->full_name }}</td>
                    <td class="border px-4 py-2">{{ $entry->task->title }}</td>
                    <td class="border px-4 py-2">{{ $entry->started_at }}</td>
                    <td class="border px-4 py-2">{{ $entry->ended_at ?? 'Ongoing' }}</td>
                    <td class="border px-4 py-2">
                        @if ($entry->ended_at)
                            {{ gmdate("H:i:s", $entry->ended_at->diffInSeconds($entry->started_at)) }}
                        @else
                            Running
                        @endif
                    </td>
                    <td class="border px-4 py-2">
                        <a href="{{ route('admin.time-logs.edit', $entry->id) }}" class="text-blue-500">Edit</a>
                        <form action="{{ route('admin.time-logs.destroy', $entry->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
