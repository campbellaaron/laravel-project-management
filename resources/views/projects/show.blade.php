{{-- resources/views/projects/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __(':name\'s Dashboard', ['name' => auth()->user()->first_name]) }}
        </h2>
    </x-slot>

    @section('title', 'Project Details')

    @section('content')
        <div class="container">
            <div class="flex justify-between flex-col lg:flex-row items-center px-6 py-3">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-500">Project: {{ $project->name }}</h2>
                    <p class="text-gray-900 dark:text-gray-500">Status: <span id="project-status">{{ $project->formatted_status }}</span></p>
                </div>
                <div class="flex flex-col md:flex-row md:items-start px-3 py-4 hazardzone">
                    <span class="text-gray-900 dark:text-gray-500">Change Project Status: </span>
                    <!-- Status Dropdown for changing the project status -->
                    @if(auth()->user()->hasAnyRole(['admin|super-admin|manager']))
                        <select id="status-select" class="p-2 border rounded">
                            <option value="open" {{ $project->status == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in-progress" {{ $project->status == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    @endif

                    <!-- Project Edit Button for Admins -->
                    @if (auth()->user()->hasAnyRole(['admin','super-admin']))
                    <a href="{{route('projects.edit', $project->id)}}" class="bg-white dark:bg-slate-600 p-6 text-gray-900 dark:text-gray-100 flex items-center rounded-md py-2 px-4 border border-transparent text-center "><x-fluentui-edit-48-o class="h-6 w-6 mr-1.5" /> Edit Project</a>
                    @endif

                    <!-- Project Delete Button for Super Admins -->
                    @if (auth()->user()->hasRole('super-admin'))
                        <form action="{{ route('projects.destroy', $project) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900 ms-2 flex justify-evenly items-center"><x-fluentui-delete-48-o class="w-6 h-6" />Delete Project</button>
                        </form>
                    @endif
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div class="relative flex flex-col my-6 bg-white shadow-sm border border-slate-200 rounded-lg">
                    <div class="mx-3 mb-0 border-b border-slate-200 pt-3 pb-2 px-1">
                        <span class="text-sm text-slate-600 font-medium">
                            Project Summary
                        </span>

                    </div>

                    <div class="p-4">
                        <h5 class="mb-2 text-slate-800 text-xl font-semibold">
                                {{ $project->name }}
                        </h5>
                        <!-- Project Description -->
                        <div class="text-slate-600 leading-normal font-light">{{ $project->description }}</div>
                    </div>
                    <div class="mx-3 border-t border-slate-200 pb-3 pt-2 px-1">
                        <span class="text-sm text-slate-600 font-medium">
                            <strong>Project Lead:</strong> {{ $project->projectLead ? $project->projectLead->full_name : 'Not Assigned' }}
                        </span>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Project Team</h3>
                    <table class="w-full mt-4 border-collapse border border-gray-300 dark:border-gray-600">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                <th class="border p-2">Name</th>
                                <th class="border p-2">Role</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($project->users as $user)
                                <tr class="border text-gray-800 dark:text-gray-300">
                                    <td class="p-2">{{ $user->full_name }}</td>
                                    <td class="p-2">{{ ucfirst($user->pivot->role) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Display success message -->
            <p id="status-message" class="mt-2 text-green-500" style="display:none;"></p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md my-6 md:my-2">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Project Tasks</h3>
            <!-- Create Task for this Project button -->
            <a href="{{ route('tasks.create') }}" class="flex items-center rounded-md bg-amber-600 py-2 px-4 border border-transparent text-center text-sm text-slate-800 transition-all shadow-md hover:shadow-lg focus:bg-amber-700 focus:shadow-none active:bg-amber-700 hover:bg-amber-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2"><x-fluentui-task-list-square-add-24-o class="h-6 w-6 mr-4" />Create a new Task</a>
            <table class="w-full mt-4 border-collapse border border-gray-300 dark:border-gray-600">
                <thead>
                    <tr class="bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <th class="border p-2">Task ID</th>
                        <th class="border p-2">Task Name</th>
                        <th class="border p-2">Status</th>
                        <th class="border p-2">Assignee</th>
                        <th class="border p-2">Priority</th>
                        <th class="border p-2">Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                    @php
                        if ($task->priority === "low") {
                            $priority_class = 'low';
                        } else if ($task->priority === "medium") {
                            $priority_class = 'medium';
                        } else {
                            $priority_class = 'high';
                        }
                    @endphp
                        <tr class="border text-gray-800 dark:text-gray-300">
                            <td class="p-4">WEB-002</td>
                            <td class="p-4"><a href="{{route('tasks.show', $task->id)}}" class="text-md text-bold text-slate-900 dark:text-slate-300"><span>{{ $task->title }}</span></a></td>
                            <td class="p-4">In Progress</td>
                            <td class="p-4">{{ $task->assignedTo->full_name }}</td>
                            <td class="p-4"><span class="{{$priority_class}}">{{$task->priority}}</span></td>
                            <td class="p-4">{{ $task->due_date }}</td>
                        </tr>
                    @endforeach
                    @if ($tasks->isEmpty())
                        <tr>
                            <td colspan="6">No tasks associated with this project.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <script>
            document.getElementById('status-select').addEventListener('change', function() {
                const status = this.value;
                const projectId = {{ $project->id }};

                // Send AJAX request to update the project status
                fetch(`/projects/${projectId}/status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ status: status }),
                })
                .then(response => response.json())
                .then(data => {

                    // Update the UI with the new status
                    document.getElementById('project-status').textContent = data.status;
                    document.getElementById('status-message').textContent = data.message;
                    document.getElementById('status-message').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        </script>
    @endsection
</x-app-layout>
