@extends('layouts.app')

@section('content')
    <h1>All Projects</h1>

    <a href="{{ route('projects.create') }}">Create New Project</a>

    <ul>
        @foreach ($projects as $project)
            <li>
                <a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a>
            </li>
        @endforeach
    </ul>
@endsection
