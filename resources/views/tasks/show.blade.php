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
                <p class="text-sm text-gray-600 dark:text-gray-400 my-2">Status:
                    <span class="p-2 rounded-sm text-xs font-semibold
                        @if($task->status === 'Not Started') bg-gray-500 text-white
                        @elseif($task->status === 'In Progress') bg-blue-500 text-white
                        @elseif($task->status === 'Under Review') bg-yellow-500 text-white
                        @elseif($task->status === 'Completed') bg-green-500 text-white
                        @elseif($task->status === 'On Hold') bg-red-500 text-white
                        @elseif($task->status === 'Cancelled') bg-gray-700 text-white
                        @endif">
                        {{ $task->status }}
                    </span>
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Assigned to: <span class="font-semibold">{{ $task->assignedTo->full_name }}</span></p>
                <p class="text-sm text-gray-600 dark:text-gray-400">Project: <span class="font-semibold">{{ $task->project->name }}</span></p>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Priority: <span class="font-semibold uppercase {{$priority_class}} text-neutral-200">{{ $task->priority }}</span></p>
            </div>
            <div class="flex items-start space-x-2">
                <a href="{{ route('tasks.edit', $task->id) }}" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md shadow hover:bg-blue-700">Edit Task</a>
                <form action="{{ route('tasks.complete', $task->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 {{ $task->status === "Completed" ? 'text-gray-300 bg-green-400 border border-green-600' : 'bg-green-600' }} text-white text-sm rounded-md shadow hover:bg-green-700">
                        {{ $task->status === "Completed" ? 'Undo Complete' : 'Mark as Complete' }}
                    </button>
                </form>
            </div>
        </div>
        <div class="flex items-center justify-between p-4 gap-2">
            <!-- Task Timer -->
            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-md flex items-center justify-between mb-4">
                <div>
                    <p class="text-lg font-semibold text text-neutral-800 dark:text-neutral-400">Total Time Tracked:
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
            <div class="text-gray-700 dark:text-gray-300">
                {!! preg_replace('/<a(.*?)href="(.*?)"(.*?)>/', '<a$1href="$2"$3 target="_blank">', strip_tags($task->description, '<p><br><ul><li><strong><em><img><a>')) !!}
            </div>
        </div>

        <!-- Display Attachments -->
        @if ($task->attachments->count())
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Attachments:</h3>
            <ul>
                @foreach ($task->attachments as $attachment)
                    <li>
                        <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank" class="text-blue-500 hover:underline dark:text-cyan-300">
                            {{ basename($attachment->path) }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif

        <!-- Task Comments -->
        <div class="border-t pt-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Comments</h3>
            <ul class="mt-2 space-y-2">
                @foreach ($task->comments as $comment)
                    <li class="p-2 bg-gray-50 dark:bg-gray-700 rounded-md text-slate-950 dark:text-slate-200">
                        <div class="flex items-center justify-start gap-2 border-b-2 border-gray-500 p-2 w-[40%] mb-2">
                            <span class="flex items-center justify-start gap-2">
                                <img src="{{ $comment->user->avatar }}" alt="{{ $comment->user->full_name }}" class="w-5 h-5 rounded-full" /><strong class="text-lg"> {{ $comment->user->full_name }}</strong>
                            </span>
                        </div>
                        <div>{!! strip_tags($comment->content, '<p><br><ul><li><strong><em><img>') !!}</div>
                    </li>
                @endforeach
            </ul>

            <form action="{{ route('tasks.storeComment', $task) }}" method="POST" class="mt-4" onsubmit="syncTinyMCE()>
                @csrf
                <progress id="upload-progress" value="0" max="100" style="display: none; width: 100%;"></progress>
                <input type="hidden" name="upload_folder" value="comments">
                <textarea name="content" class="rte w-full p-2 border rounded-md"></textarea>
                <button type="submit" class="mt-2 px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">Add Comment</button>
            </form>
        </div>

        <!-- Time Tracking Logs -->
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
                            <td class="p-2">{{ $entry->created_at->timezone(auth()->user()->timezone)->format('F d, Y h:i A') }}</td>
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
        document.addEventListener("DOMContentLoaded", () => {
            tinymce.init({
                selector: 'textarea.rte',
                skin: document.documentElement.classList.contains('dark') ? 'oxide-dark' : 'oxide',
                content_css: document.documentElement.classList.contains('dark') ? 'dark' : 'default',
                menubar: false,
                plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                branding: false,
                height: 400,
                images_upload_url: '/upload-image',
                a11y_advanced_options: true,
                automatic_uploads: true,
                image_title: true,
                images_file_types: 'jpg,svg,webp,png,gif',
                // ✅ Use File Picker for Local Image Selection (Base64)
                file_picker_types: 'image',
                file_picker_callback: (cb, value, meta) => {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');

                    input.addEventListener('change', (e) => {
                        const file = e.target.files[0];
                        const reader = new FileReader();

                        reader.onload = () => {
                            const id = 'blobid' + (new Date()).getTime();
                            const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                            const base64 = reader.result.split(',')[1];
                            const blobInfo = blobCache.create(id, file, base64);
                            blobCache.add(blobInfo);

                            cb(blobInfo.blobUri(), { title: file.name });
                        };

                        reader.readAsDataURL(file);
                    });

                    input.click();
                },

                // ✅ Server-Side Upload for Persistent Storage (With Promise)
                images_upload_url: '/upload-image', // Laravel Upload Route
                automatic_uploads: true,
                images_upload_handler: function (blobInfo) {
                    return new Promise((resolve, reject) => {
                        let xhr = new XMLHttpRequest();
                        xhr.open('POST', '/upload-image', true);
                        xhr.setRequestHeader("X-CSRF-TOKEN", document.querySelector('meta[name="csrf-token"]').content);

                        xhr.onload = function () {
                            let response;
                            try {
                                response = JSON.parse(xhr.responseText);
                            } catch (e) {
                                reject('Invalid JSON response from server');
                                return;
                            }

                            if (xhr.status === 200 && response.location) {
                                resolve(response.location);
                            } else {
                                reject(response.error || 'Image upload failed');
                            }
                        };

                        xhr.onerror = function () {
                            reject('Image upload failed due to network error.');
                        };

                        let formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());

                        xhr.send(formData);
                    });
                }

            });
        });
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

        function syncTinyMCE() {
            tinymce.triggerSave(); // Ensure content is saved before submit
        }
    </script>
@endsection

