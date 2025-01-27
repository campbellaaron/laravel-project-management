{{-- resources/views/projects/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <h1>Create New Project</h1>

    <form action="{{ route('projects.store') }}" method="POST">
        @csrf
        <div>
            <label for="name">Project Name</label>
            <input type="text" name="name" id="name" required>
        </div>

        <div>
            <label for="description">Description</label>
            <textarea name="description" id="description" required></textarea>
        </div>

        <div>
            <label for="status">Status</label>
            <select name="status" id="status" required>
                <option value="open">Open</option>
                <option value="in-progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
        </div>

        <button type="submit">Create Project</button>
    </form>
@endsection
