{{-- resources/views/projects/show.blade.php --}}
@extends('layouts.app')

@section('content')
    <h1>{{ $project->name }}</h1>
    <p>{{ $project->description }}</p>
    <p>Status: {{ $project->status }}</p>

    <a href="{{ route('projects.edit', $project) }}">Edit Project</a>

    <h2>Tasks for this Project</h2>
    <ul>
        @foreach ($tasks as $task)
            <li>
                <strong>{{ $task->title }}</strong> - {{ $task->description }}
                <p>Assigned to: {{ $task->assignedTo->name }}</p>
                <p>Due: {{ $task->due_date }}</p>
            </li>
        @endforeach
    </ul>
    <a href="{{ route('tasks.create') }}">Create a new Task</a>
    <form action="{{ route('projects.destroy', $project) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit">Delete Project</button>
    </form>
@endsection
