<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __(':name\'s Dashboard', ['name' => auth()->user()->first_name]) }}
        </h2>
        <div class="p-1 text-gray-900 dark:text-gray-100 text-sm">
            {{ __("You're logged in!") }}
        </div>
    </x-slot>
    @section('content')
        <div class="container">
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                        <!-- Notifications Card, Spans the whole grid width -->
                        <div class=" bg-white dark:bg-slate-600 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 text-gray-900 dark:text-gray-100">
                                <h3 class="text-lg font-semibold">Unread Notifications</h3>
                                @if($notifications->isEmpty())
                                    <p>No unread notifications</p>
                                @else
                                    <ul>
                                        @foreach(auth()->user()->unreadNotifications as $notification)
                                            <li class="mb-4">
                                                <p>{{ $notification->data['message'] }}</p>
                                                <a href="{{ route('tasks.show', $notification->data['task_id']) }}" class="text-blue-500">View Task</a>
                                                <a href="{{ route('notifications.markAsRead', $notification->id) }}">Mark as Read</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>

                        <!-- Recent Activity Card -->
                        <div class="bg-white p-4 shadow-sm rounded-lg">
                            <h3 class="text-lg font-semibold">Recent Activity</h3>
                            <ul class="mt-2">
                                @foreach ($recentActivity as $activity)
                                    <li class="mb-2">
                                        <p>{{ $activity->description }} <span class="text-sm text-gray-500">({{ $activity->created_at->diffForHumans() }})</span></p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Show admin content -->
                        @if(auth()->user()->hasAnyRole(['super-admin', 'admin', 'manager']))
                            <!-- Newest Users Card -->
                            <div class="bg-white p-4 shadow-sm rounded-lg">
                                <h3 class="text-lg font-semibold">Newest Users</h3>
                                <div class="">
                                    <ul class="mt-2">
                                        @foreach ($newUsers as $user)
                                            <li class="flex justify-between mb-2">
                                                <span>{{ $user->name }}</span>
                                                <span class="text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <a href="{{ route('users.index') }}" class="text-blue-500 text-sm mt-2 block">View all users</a>
                                </div>
                            </div>

                            <!-- Completed Tasks Statistics Card -->
                            <div class="bg-white p-4 shadow-sm rounded-lg">
                                <h3 class="text-lg font-semibold">Completed Tasks</h3>
                                <p class="mt-2 text-2xl">{{ $completedTasksCount }}</p>
                                <a href="{{ route('tasks.index') }}" class="text-blue-500 text-sm mt-2 block">View all completed tasks</a>
                            </div>

                            <!-- Project Progress Card -->
                            <div class="bg-white p-4 shadow-sm rounded-lg">
                                <h3 class="text-lg font-semibold">Project Progress</h3>
                                <ul class="mt-2">
                                    @foreach ($projects as $project)
                                        <li class="mb-2">
                                            <p>{{ $project->name }} - {{ $project->tasks_count }} tasks completed out of {{ $project->tasks->count() }} tasks</p>
                                            <div class="w-full bg-gray-200 rounded-full">
                                                <div class="bg-blue-500 text-xs leading-none py-1 text-center text-white rounded-full" style="width: {{ $project->tasks->count() > 0 ? ($project->tasks_count / $project->tasks->count() * 100) : 0 }}%"></div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Latest User's Incomplete Tasks Card -->
                        <div class="bg-white p-4 shadow-sm rounded-lg">
                            <h3 class="text-lg font-semibold">Latest Incomplete Tasks</h3>
                            <ul class="mt-2">
                                @foreach ($latestTasks as $task)
                                    <li class="flex justify-between mb-2">
                                        <span>{{ $task->title }}</span>
                                        <span class="text-sm text-gray-500">{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No Due Date' }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('tasks.index') }}" class="text-blue-500 text-sm mt-2 block">View all tasks</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endsection
</x-app-layout>
