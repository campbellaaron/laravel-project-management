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
                        required>{{ old('description', $task->description ?? '') }}</textarea>
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
    <script src="https://cdn.tiny.cloud/1/{{ env('TINYMCE_API_KEY') }}/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>

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
            automatic_uploads: true,
            file_picker_types: 'image file media',
            images_file_types: 'jpg,svg,webp,png,gif',
            images_upload_handler: function (blobInfo, success, failure) {
                let formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                fetch('/upload-image', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    }
                });
            }
        });



    const form = document.getElementById("task-form");
        const fileInput = document.getElementById("attachment");
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes

        form.addEventListener("submit", function (event) {
            if (fileInput.files.length > 0) {
                let file = fileInput.files[0];
                if (file.size > maxSize) {
                    alert("File size exceeds 10MB. Please choose a smaller file.");
                    event.preventDefault(); // Stop form submission
                }
            }
        });
});
</script>
@endsection
