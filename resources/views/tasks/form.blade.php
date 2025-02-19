@extends('layouts.app')

@section('title', isset($task) ? 'Edit Task' : 'New Task')

@section('content')
    <div class="max-w-3xl mx-auto">
        <!-- Error Alert -->
        @if ($errors->any())
            <div role="alert" class="mb-4 relative flex w-full p-3 text-sm text-white bg-red-600 rounded-md">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button class="absolute top-1.5 right-1.5 flex items-center justify-center w-8 h-8 rounded-md text-white hover:bg-white/10 active:bg-white/10 transition-all"
                        type="button" onclick="this.parentElement.style.display='none'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Task Form -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                {{ isset($task) ? 'Edit Task' : 'Create a New Task' }}
            </h2>

            <form id="task-form" action="{{ isset($task) ? route('tasks.update', $task) : route('tasks.store') }}" method="POST" class="space-y-4" enctype="multipart/form-data">
                @csrf
                @isset($task)
                    @method('PUT')
                @endisset

                <!-- Title -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $task->title ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <progress id="upload-progress" value="0" max="100" style="display: none; width: 100%;"></progress>
                    <input type="hidden" name="upload_folder" value="tasks">
                    <textarea name="description" id="description" rows="4"
                        class="rte mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        >{{ old('description', $task->description ?? '') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Assigned To -->
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assigned To</label>
                    <select name="assigned_to" id="assigned_to"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ isset($task) && $user->id == $task->assigned_to ? 'selected' : '' }}>
                                {{ $user->full_name }} {{ auth()->id() == $user->id ? "(You)" : "" }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Project -->
                <div>
                    <label for="project_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project</label>
                    <select name="project_id" id="project_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}" {{ isset($task) && $task->project_id == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Task Status</label>
                    <select name="status" id="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        @foreach(['Not Started', 'In Progress', 'Under Review', 'Completed', 'On Hold', 'Cancelled'] as $status)
                        <option value="{{ $status }}"
                            {{ old('status', isset($task) ? $task->status : '') == $status ? 'selected' : '' }}>
                            {{ $status }}
                        </option>

                        @endforeach
                    </select>
                </div>

                <!-- Task Priority -->
                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                    <select name="priority" id="priority"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                        required>
                        <option value="low" {{ isset($task) && $task->priority == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ isset($task) && $task->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ isset($task) && $task->priority == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ isset($task) && $task->priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <!-- Due Date -->
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date</label>
                    <input type="datetime-local" name="due_date" id="due_date"
                        value="{{ old('due_date', isset($task) && $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d\TH:i') : '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                </div>

                <!-- Attach files less than or equal to 10MB -->
                <div class="mb-4">
                    <label for="attachments" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Attachments</label>
                    <input type="file" name="attachments[]" id="attachments" multiple
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <!-- Submit Button -->
                <div class="flex justify-between">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md shadow-md transition-all">
                        {{ isset($task) ? 'Update Task' : 'Create Task' }}
                    </button>
                    <a href="{{ isset($task) ? route('tasks.show', $task->id) : route('tasks.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded-md shadow-md transition-all">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.tiny.cloud/1/{{ env('TINYMCE_API_KEY') }}/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
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



    const form = document.getElementById("task-form");
        const fileInput = document.getElementById("attachment");
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes

        if (form && fileInput) { // Ensure both form and file input exist
            form.addEventListener("submit", function (event) {
                if (fileInput.files.length > 0) {
                    let file = fileInput.files[0];
                    let maxSize = 10 * 1024 * 1024; // 10MB in bytes

                    if (file.size > maxSize) {
                        alert("File size exceeds 10MB. Please choose a smaller file.");
                        event.preventDefault(); // Stop form submission
                    }
                }
            });
        }
});
</script>
@endsection
