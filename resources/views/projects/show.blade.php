{{-- resources/views/projects/show.blade.php --}}
@extends('layouts.app')

@section('content')
    <h1>{{ $project->name }}</h1>
    <p>{{ $project->description }}</p>
    <p>Status: {{ $project->status }}</p>

    <a href="{{ route('projects.edit', $project) }}">Edit Project</a>

    <form action="{{ route('projects.destroy', $project) }}" method="POST">
        @csrf
        @method('DELETE')
        <button type="submit">Delete Project</button>
    </form>
@endsection
