{{-- resources/views/projects/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <h1>Edit Project</h1>

    <form action="{{ route('projects.update', $project) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Project Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}" required>
        </div>

        <div>
            <label for="description">Description</label>
            <textarea name="description" id="description" required>{{ old('description', $project->description) }}</textarea>
        </div>

        <div>
            <label for="status">Status</label>
            <select name="status" id="status" required>
                <option value="open" {{ $project->status == 'open' ? 'selected' : '' }}>Open</option>
                <option value="in-progress" {{ $project->status == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        <button type="submit">Update Project</button>
    </form>
@endsection
