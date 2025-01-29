@extends('layouts.app')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
        @csrf
        @method('PUT') <!-- or @method('PATCH') -->

        <!-- Title -->
        <div>
            <label for="title">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title', $task->title) }}" required>
        </div>

        <!-- Description -->
        <div>
            <label for="description">Description</label>
            <textarea rows="20" name="description" id="description" required>{{ old('description', $task->description) }}</textarea>
        </div>

        <!-- Assigned To -->
        <div>
            <label for="assigned_to">Assigned To</label>
            <select name="assigned_to" id="assigned_to" required>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ $user->id == old('assigned_to', $task->assigned_to) ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Due Date -->
        <div>
            <label for="due_date">Due Date</label>
            <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date', $task->due_date ? $task->due_date->format('Y-m-d\TH:i') : '') }}">
        </div>

        <div class="flex justify-start px-3">
            <div class="flex flex-row justify-evenly">
                <button type="submit" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Update Task</button>
                <a href="{{ route("tasks.show", $task->id)}}" class="text-red-700 hover:text-white border border-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-red-500 dark:text-red-500 dark:hover:text-white dark:hover:bg-red-600 dark:focus:ring-red-900">Cancel</a>
            </div>
        </div>
    </form>
@endsection
