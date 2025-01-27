@extends('layouts.app')

@section('content')
    <h1>Create Task</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf
        <div>
            <label for="title">Title</label>
            <input type="text" name="title" id="title" required>
        </div>

        <div>
            <label for="description">Description</label>
            <textarea name="description" id="description" required></textarea>
        </div>

        <div>
            <label for="assigned_to">Assigned To</label>
            <select name="assigned_to" id="assigned_to" required>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="project_id">Project</label>
            <select name="project_id" id="project_id" required>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="due_date">Due Date</label>
            <input type="datetime-local" name="due_date" id="due_date">
        </div>

        <button type="submit">Create Task</button>
    </form>
@endsection
