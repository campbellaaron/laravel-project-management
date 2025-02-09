@extends('layouts.app')

@section('title', "Task: $task->task_key - $task->title")

@php
    $totalSeconds = max(0, $task->totalTrackedTime()); // Ensure no negatives
    $hours = intdiv($totalSeconds, 3600);
    $minutes = intdiv($totalSeconds % 3600, 60);
    $seconds = $totalSeconds % 60;

    if ($task->priority === "urgent") {
        $priority_class = 'urgent';
    } else if ($task->priority === "high") {
        $priority_class = 'high';
    } else if ($task->priority === "medium") {
        $priority_class = 'medium';
    } else {
        $priority_class = 'low';
    }
@endphp
@section('content')
    <div class="max-w-4xl mx-auto p-6 bg-white shadow-lg rounded-lg dark:bg-gray-800">
        <!-- Task Header -->
        <div class="flex justify-between items-center border-b pb-4 mb-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $task->title }}</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">Assigned to: <span class="font-semibold">{{ $task->assignedTo->full_name }}</span></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Project: <span class="font-semibold">{{ $task->project->name }}</span></p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Priority: <span class="font-semibold uppercase {{$priority_class}}">{{ $task->priority }}</span></p>
            </div>
            <div class="flex items-start space-x-2">
                <a href="{{ route('tasks.edit', $task->id) }}" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md shadow hover:bg-blue-700">Edit Task</a>
                <form action="{{ route('tasks.complete', $task->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm rounded-md shadow hover:bg-green-700">
                        {{ $task->completed ? 'Undo Complete' : 'Mark as Complete' }}
                    </button>
                </form>
            </div>
        </div>
        <div class="flex items-center justify-between p-4 gap-2">
            <!-- Task Timer -->
            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-md flex items-center justify-between mb-4">
                <div>
                    <p class="text-lg font-semibold">Total Time Tracked:
                        <span id="total-time" class="text-blue-600">{{ sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds) }}</span>
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Keep track of the time you spend on this task.</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button id="start-timer" data-task-id="{{ $task->id }}" class="px-4 py-2 bg-green-500 text-white text-sm rounded-md shadow hover:bg-green-600">Start Timer</button>
                    <button id="stop-timer" data-task-id="{{ $task->id }}" class="px-4 py-2 bg-red-500 text-white text-sm rounded-md shadow hidden hover:bg-red-600">Stop Timer</button>
                    <span id="live-timer" class="ml-4 text-lg font-mono text-gray-800 dark:text-gray-100">00:00:00</span>
                </div>
            </div>

            <!-- Manual Time Tracking -->
            <div class="mt-6 p-4 border rounded-md bg-gray-100 dark:bg-gray-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Manually Add Time</h3>
                <form action="{{ route('tasks.addManualTime', $task->id) }}" method="POST" class="flex flex-wrap items-center gap-4">
                    @csrf

                    <!-- Hours Input -->
                    <div class="flex items-center gap-2">
                        <label for="manual_hours" class="text-sm text-gray-700 dark:text-gray-300">Hours</label>
                        <input type="number" name="manual_hours" id="manual_hours" min="0" class="w-16 rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white p-1">
                    </div>

                    <!-- Minutes Input -->
                    <div class="flex items-center gap-2">
                        <label for="manual_minutes" class="text-sm text-gray-700 dark:text-gray-300">Minutes</label>
                        <input type="number" name="manual_minutes" id="manual_minutes" min="0" max="59" class="w-16 rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white p-1">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md shadow-md transition-all">
                        Add Time
                    </button>
                </form>
            </div>
        </div>


        <!-- Task Description -->
        <div class="mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Description</h3>
            <p class="text-gray-700 dark:text-gray-300">{{ $task->description }}</p>
        </div>

        <!-- Task Comments -->
        <div class="border-t pt-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Comments</h3>
            <ul class="mt-2 space-y-2">
                @foreach ($task->comments as $comment)
                    <li class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md text-slate-950 dark:text-slate-200">
                        <div class="flex items-center justify-start gap-2">
                            <span class="flex items-center justify-start gap-2">
                                <img src="{{ $comment->user->avatar }}" alt="{{ $comment->user->full_name }}" class="w-5 h-5 rounded-full" /><strong class="text-lg"> {{ $comment->user->full_name }}</strong>
                            </span>
                        </div>
                        <p>{{ $comment->content }}</p>
                    </li>
                @endforeach
            </ul>

            <form action="{{ route('tasks.storeComment', $task) }}" method="POST" class="mt-4">
                @csrf
                <textarea name="content" required class="w-full p-2 border rounded-md"></textarea>
                <button type="submit" class="mt-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">Add Comment</button>
            </form>
        </div>
        <div class="mt-6 p-4 border rounded-md bg-gray-100 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Time Log</h3>

            <table class="w-full mt-2 border border-gray-300 dark:border-gray-700 rounded-lg">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700">
                        <th class="p-2 text-left text-gray-900 dark:text-white">User</th>
                        <th class="p-2 text-left text-gray-900 dark:text-white">Time Spent</th>
                        <th class="p-2 text-left text-gray-900 dark:text-white">Method</th>
                        <th class="p-2 text-left text-gray-900 dark:text-white">Date Logged</th>
                        <th class="p-2 text-left text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-slate-950 dark:text-slate-200">
                    @foreach ($task->timeEntries as $entry)
                        <tr class="border-t border-gray-300 dark:border-gray-700">
                            <td class="p-2">{{ $entry->user->full_name }}</td>
                            <td class="p-2">
                                @php
                                    $seconds = \Carbon\Carbon::parse($entry->started_at)->diffInSeconds($entry->ended_at);
                                    $hours = intdiv($seconds, 3600);
                                    $minutes = intdiv($seconds % 3600, 60);
                                @endphp
                                {{ sprintf('%02d:%02d', $hours, $minutes) }}
                            </td>
                            <td class="p-2">
                                {{ $entry->description == 'Manual entry' ? 'Manual' : 'Timer' }}
                            </td>
                            <td class="p-2">{{ $entry->created_at->format('M d, Y h:i a') }}</td>
                            <td class="p-2">
                                @if(auth()->user()->id == $entry->user_id || auth()->user()->hasRole('admin'))
                                    <!-- Edit Button -->
                                    <button class="text-blue-500 hover:text-blue-700" onclick="openEditModal({{ $entry->id }}, {{ $hours }}, {{ $minutes }})">
                                        ✏️ Edit
                                    </button>

                                    <!-- Delete Form -->
                                    <form action="{{ route('tasks.deleteTimeEntry', $entry->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 ml-2">
                                            🗑 Delete
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
@endsection
<!-- Edit Modal -->
<div id="editTimeModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md w-96">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Edit Time Entry</h3>

        <form id="editTimeForm" method="POST">
            @csrf
            @method('PATCH')

            <input type="hidden" name="entry_id" id="edit_entry_id">

            <div class="flex items-center gap-2">
                <label for="edit_hours" class="text-sm text-gray-700 dark:text-gray-300">Hours</label>
                <input type="number" name="hours" id="edit_hours" min="0" class="w-16 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-1">
            </div>

            <div class="flex items-center gap-2 mt-2">
                <label for="edit_minutes" class="text-sm text-gray-700 dark:text-gray-300">Minutes</label>
                <input type="number" name="minutes" id="edit_minutes" min="0" max="59" class="w-16 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-1">
            </div>

            <div class="mt-4 flex justify-between">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md shadow-md transition-all">
                    Save Changes
                </button>
                <button type="button" onclick="closeEditModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(entryId, hours, minutes) {
        document.getElementById("edit_entry_id").value = entryId;
        document.getElementById("edit_hours").value = hours;
        document.getElementById("edit_minutes").value = minutes;
        document.getElementById("editTimeModal").classList.remove("hidden");

        let form = document.getElementById("editTimeForm");
        form.action = `/time-entries/${entryId}/update`;
    }

    function closeEditModal() {
        document.getElementById("editTimeModal").classList.add("hidden");
    }
</script>

