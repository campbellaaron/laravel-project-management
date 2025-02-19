@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-neutral-300">Task &amp; Project Reports</h2>

    <form method="GET" action="{{ route('reports.index') }}" class="mb-6 flex gap-4">
        <input type="date" name="start_date" class="border rounded px-3 py-2" value="{{ request('start_date') }}">
        <input type="date" name="end_date" class="border rounded px-3 py-2" value="{{ request('end_date') }}">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Task Report -->
        <div class="bg-white shadow-md rounded-lg p-4">
            <h3 class="text-lg font-semibold">Task Reports</h3>
            <p class="text-gray-600">Download all tasks and their statuses.</p>
            <a href="{{ route('reports.export', ['type' => 'tasks']) }}" class="bg-blue-600 text-white px-4 py-2 rounded mt-2 inline-block">Download Task Report</a>
        </div>

        <!-- Project Report -->
        <div class="bg-white shadow-md rounded-lg p-4">
            <h3 class="text-lg font-semibold">Project Reports</h3>
            <p class="text-gray-600">Export all projects with their related tasks.</p>
            <a href="{{ route('reports.export', ['type' => 'projects']) }}" class="bg-green-600 text-white px-4 py-2 rounded mt-2 inline-block">Download Project Report</a>
        </div>
    </div>

</div>
@endsection
