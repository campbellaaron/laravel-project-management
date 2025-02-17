@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="p-3 flex flex-col items-start justify-center">
    <div class="p-2 flex items-start justify-between gap-4">
        <a href="{{ route('tasks.create') }}" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Create New Task</a>

        <div class="flex space-x-4">
            <!-- Show "All Tasks" button if not already selected -->
            <a href="{{ route('tasks.index', ['filter' => 'all']) }}"
                class="px-4 py-2 rounded-lg text-white font-semibold transition-all shadow-md
                    {{ request('filter') === 'all' ? 'bg-gray-900 dark:bg-gray-800' : 'bg-gray-600 hover:bg-gray-700' }}">
                All Tasks
            </a>

            <!-- Show "My Tasks" button if not already selected -->
            <a href="{{ route('tasks.index', ['filter' => 'my']) }}"
                class="px-4 py-2 rounded-lg text-white font-semibold transition-all shadow-md
                    {{ request('filter') === 'my' || request('filter') === null ? 'bg-gray-900' : 'bg-gray-600 hover:bg-gray-700' }}">
                My Tasks
            </a>
        </div>
    </div>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg text-gray-600 dark:text-gray-400">
        <table class="tasks-table w-full text-sm text-left rtl:text-right border-collapse overflow-x-auto datatable">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Task ID</th>
                    <th scope="col" class="px-6 py-3">Title</th>
                    <th scope="col" class="px-6 py-3">Description</th>
                    <th scope="col" class="px-6 py-3">Project</th>
                    <th scope="col" class="px-6 py-3">Assigned To</th>
                    <th scope="col" class="px-6 py-3">Due Date</th>
                    <th scope="col" class="px-6 py-3">Priority</th>
                    <th scope="col" class="px-6 py-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $task)
                    @php
                        if ($task->priority === "urgent") {
                            $priority_class = 'urgent';
                        } else if ($task->priority === "high") {
                            $priority_class = 'high';
                        } else if ($task->priority === "medium") {
                            $priority_class = 'medium';
                        } else {
                            $priority_class = 'low';
                        }
                    @endphp
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200 hover:bg-gray-300 dark:hover:bg-gray-800">
                        <th scope="row" class="px-6 py-4">{{ $task->task_key }}</th>
                        <th class="px-6 py-4 font-bold text-base lg:text-lg text-gray-900 dark:text-white"><a href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a></th>
                        <td class="px-6 py-4">{{ $task->description }}</td>
                        <td class="px-6 py-4">{{ $task->project->name }}</td>
                        <td class="px-6 py-4 bold">{{ $task->assignedTo->first_name }}</td>
                        <td class="px-6 py-4">{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No due date' }}</td>
                        <td class="px-6 py-4 uppercase text-white"><span class="{{ $priority_class }} rounded-md font-bold">{{ $task->priority }}</span></td>
                        <td class="px-4 py-4">
                            <div class="flex items-center md:items-baseline justify-between flex-col md:flex-row">
                                <a href="{{ route('tasks.edit', $task) }}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Edit</a>
                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900 ms-2">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
