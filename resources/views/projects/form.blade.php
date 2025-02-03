@extends('layouts.app')

@section('title', isset($project) ? 'Edit Project' : 'Create New Project')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                {{ isset($project) ? 'Edit Project' : 'Create New Project' }}
            </h2>

            <form action="{{ isset($project) ? route('projects.update', $project) : route('projects.store') }}" method="POST">
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
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>{{ old('description', isset($project) ? $project->description : '') }}</textarea>
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
                    <label for="team" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Team Members</label>
                    <select name="team[]" id="team" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                        @endforeach
                    </select>
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

@endsection
