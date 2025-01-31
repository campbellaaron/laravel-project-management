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
            <h3 class="text-lg font-semibold">Project: {{ $project->name }}</h3>
            <p>Status: <span id="project-status">{{ $project->formatted_status }}</span></p>

            <!-- Status Dropdown for changing the project status -->
            <select id="status-select" class="mt-2 p-2 border rounded">
                <option value="open" {{ $project->status == 'open' ? 'selected' : '' }}>Open</option>
                <option value="in-progress" {{ $project->status == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>

            <!-- Create Task for this Project button -->
            <a href="{{ route('tasks.create') }}">Create a new Task</a>

            <!-- Display success message -->
            <p id="status-message" class="mt-2 text-green-500" style="display:none;"></p>
        </div>

        <!-- Project Delete Button for Admins -->
        <form action="{{ route('projects.destroy', $project) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit">Delete Project</button>
        </form>

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



        <!-- Project Description -->
        <div class="">{{ $project->description }}</div>

        <!-- Display Tasks Related to the Project -->
        <h3 class="mt-4 text-lg font-semibold">Project Tasks</h3>
        <ul class="mt-2">
            @foreach ($tasks as $task)
                <li class="flex justify-between mb-2">
                    <a href="{{route('tasks.show', $task->id)}}" class="text-md text-bold text-slate-900 dark:text-slate-300"><span>{{ $task->title }}</span></a>
                    <span class="text-sm text-gray-500">{{ $task->due_date ? $task->due_date->format('M d, Y') : 'No Due Date' }}</span>
                </li>
            @endforeach
        </ul>
        @if ($tasks->isEmpty())
            <p>No tasks associated with this project.</p>
        @endif

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
