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

                        <!-- Notifications Card -->
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
                                                <!-- View Task Button (Marks as Read & Redirects) -->
                                                <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="redirect_to" value="{{ route('tasks.show', $notification->data['task_id']) }}">
                                                    <button type="submit" class="text-blue-500 underline">View Task</button>
                                                </form>

                                                <!-- Mark as Read Button -->
                                                <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-red-500" data-id="{{ $notification->id }}">Mark as Read</button>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <!-- Mark All as Read -->
                                    <button id="mark-all-read" class="mt-3 text-red-500 underline">Mark All as Read</button>
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
                            <a href="{{ route('tasks.index', ['filter' => 'all']) }}" class="text-blue-500 text-sm mt-2 block">View all tasks</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Mark Single Notification as Read
            document.querySelectorAll(".mark-as-read").forEach(button => {
                button.addEventListener("click", function (event) {
                    event.preventDefault();

                    const notificationId = this.dataset.id;
                    const listItem = document.getElementById(`notification-${notificationId}`);

                    fetch(`/notifications/mark-as-read/${notificationId}`, {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                            "Content-Type": "application/json"
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            listItem.remove(); // Remove the notification from the UI
                        }
                    })
                    .catch(error => console.error("Error:", error));
                });
            });

            // Mark All Notifications as Read
            document.getElementById("mark-all-read").addEventListener("click", function (event) {
                event.preventDefault();

                fetch(`/notifications/mark-as-read`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Content-Type": "application/json"
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById("notification-list").innerHTML = "<p>No unread notifications</p>";
                    }
                })
                .catch(error => console.error("Error:", error));
            });
        });
    </script>
    @endsection

</x-app-layout>
