@extends('layouts.app')

@section('title', 'All Projects')

@section('content')

    <div class="container">
        <a href="{{ route('projects.create') }}">Create New Project</a>

        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th>Project Name</th>
                    <th>Status</th>
                    <th>Project Lead</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($projects as $project)
                    <tr>
                        <td>{{ $project->name }}</td>
                        <td>{{ ucwords(str_replace('-', ' ', $project->status)) }}</td>
                        <td>
                            {{ $project->projectLead ? $project->projectLead->first_name . ' ' . $project->projectLead->last_name : 'Not Assigned' }}
                        </td>
                        <td>
                            <a href="{{ route('projects.show', $project->id) }}" class="text-blue-500">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection
