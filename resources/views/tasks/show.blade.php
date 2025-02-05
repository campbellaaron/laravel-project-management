@extends('layouts.app')

@section('title', "Task: $task->task_key - $task->title")

@section('content')
    <div class="container">
        <div class="text-slate-900 dark:text-gray-200 bg-slate-900 dark:bg-slate-700 p-10">

            <div class="task-description">
                <h3>Description:</h3>
                <p>{{$task->description}}</p>
            </div>

            <div class="task-project">
                <h3>Project:</h3>
                <p>{{ $task->project->name }}</p>
            </div>

            <div class="task-assigned-to">
                <h3>Assigned To:</h3>
                <p>{{ $task->assignedTo->full_name }}</p>
            </div>

            <div class="task-due-date">
                <h3>Due Date:</h3>
                <p>{{ $task->due_date ? $task->due_date->format('F d, Y') : 'No due date' }}</p>
            </div>

            <form action="{{ route('tasks.complete', $task->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">
                    {{ $task->completed ? 'Undo Complete' : 'Complete Task' }}
                </button>
                <a href="{{ route('tasks.edit', $task->id) }}" class="bg-white dark:bg-slate-600 p-6 text-gray-900 dark:text-gray-100">Edit This Task</a>
            </form>


            <div class="task-comments">
                <h3>Comments:</h3>
                <ul>
                    @foreach ($task->comments as $comment)
                    <li>
                        <strong>{{ $comment->user->name }}:</strong> {{ $comment->content }}
                    </li>
                    @endforeach
                </ul>
                <form action="{{ route('tasks.storeComment', $task) }}" method="POST">
                    @csrf
                    <textarea name="content" required></textarea>
                    <button type="submit">Add Comment</button>
                </form>
            </div>
        </div>
    </div>


@endsection
