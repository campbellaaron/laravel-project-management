@extends('layouts.app')

@section('title', isset($project) ? 'Edit Project' : 'Create New Project')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ isset($project) ? 'Edit Project' : 'Create New Project' }}
            </h2>

            <form action="{{ isset($project) ? route('projects.update', $project) : route('projects.store') }}" id="projects-form" method="POST" enctype="multipart/form-data">
                @csrf
                @isset($project)
                    @method('PUT')
                @endisset

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Name</label>
                    <input type="text" name="name" id="name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        value="{{ old('name', isset($project) ? $project->name : '') }}"
                        required>
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <progress id="upload-progress" value="0" max="100" style="display: none; width: 100%;"></progress>
                    <input type="hidden" name="upload_folder" value="projects">
                    <textarea name="description" id="description" rows="3"
                        class="rte mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>{{ old('description', isset($project) ? $project->description : '') }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="open" {{ isset($project) && $project->status == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in-progress" {{ isset($project) && $project->status == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="completed" {{ isset($project) && $project->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                    <input type="date" name="start_date" id="start_date"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        value="{{ old('start_date', isset($project) && $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '') }}">
                </div>

                <div class="mb-4">
                    <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date</label>
                    <input type="date" name="due_date" id="due_date"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        value="{{ old('due_date', isset($project) && $project->due_date ? \Carbon\Carbon::parse($project->due_date)->format('Y-m-d') : '') }}">
                </div>

                <div class="mb-4">
                    <label for="project_lead" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Project Lead</label>
                    <select name="project_lead_id" id="project_lead" class="w-full border rounded">
                        <option value="">-- Select Project Lead --</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ isset($project) && $project->project_lead_id == $user->id ? 'selected' : '' }}>
                                {{ $user->first_name }} {{ $user->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Team Members & Roles</label>
                    <div class="mt-2 space-y-2">
                        @foreach($users as $user)
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" name="team[]" value="{{ $user->id }}" id="user_{{ $user->id }}"
                                    {{ isset($project) && $project->users->contains($user->id) ? 'checked' : '' }}>
                                <label for="user_{{ $user->id }}" class="text-gray-700 dark:text-gray-300">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </label>

                                <select name="roles[{{ $user->id }}]" class="ml-2 border rounded p-1">
                                    <option value="contributor"
                                        {{ isset($project) && $project->users()->wherePivot('role', 'contributor')->where('users.id', $user->id)->exists() ? 'selected' : '' }}>
                                        Contributor
                                    </option>
                                    <option value="watcher"
                                        {{ isset($project) && $project->users()->wherePivot('role', 'watcher')->where('users.id', $user->id)->exists() ? 'selected' : '' }}>
                                        Watcher
                                    </option>
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4">
                    <label for="attachments" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Attachments</label>
                    <input type="file" name="attachments[]" id="attachments" multiple
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                    @isset($project)
                        Update Project
                    @else
                        Create Project
                    @endisset
                </button>

                <a href="{{ isset($project) ? route('projects.show', $project->id) : route('projects.index') }}">
                    <button class="rounded-md bg-transparent py-2 px-4 border border-red-800 text-center text-base text-white transition-all shadow-md hover:shadow-lg focus:bg-red-700 focus:shadow-none active:bg-red-700 hover:bg-red-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none" type="button">
                        Cancel
                    </button>
                </a>
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

        const form = document.getElementById("projects-form");
        const fileInput = document.getElementById("attachments");
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
