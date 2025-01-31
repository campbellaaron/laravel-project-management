{{-- resources/views/projects/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
    <form id="assignUsersForm" action="{{ route('projects.assignUsers', $project->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-lg">
            <h2 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">Assign Users to Project</h2>

            <!-- Searchable Multi-Select Dropdown -->
            <div x-data="{ open: false, search: '', selectedUsers: [] }" class="relative">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Select Users:</label>
                <input
                    x-model="search"
                    type="text"
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="Search users..."
                    @focus="open = true"
                    @click.away="open = false"
                />

                <div x-show="open" class="absolute bg-white dark:bg-gray-800 shadow-lg rounded-lg w-full mt-1 max-h-40 overflow-y-auto z-50">
                    @foreach ($users as $user)
                        <div
                            class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer"
                            @click="selectedUsers.push({ id: '{{ $user->id }}', name: '{{ $user->first_name }} {{ $user->last_name }}' }); open = false; search = ''"
                        >
                            {{ $user->first_name }} {{ $user->last_name }}
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Display Selected Users with Role Selection -->
            <div class="mt-4 space-y-2">
                <template x-for="user in selectedUsers" :key="user.id">
                    <div class="flex justify-between bg-gray-200 dark:bg-gray-800 px-4 py-2 rounded-lg">
                        <span class="text-gray-900 dark:text-gray-100" x-text="user.name"></span>
                        <select :name="'users[' + user.id + '][role]'" class="border px-2 py-1 rounded">
                            <option value="contributor">Contributor</option>
                            <option value="watcher">Watcher</option>
                        </select>
                        <button type="button" class="text-red-500" @click="selectedUsers = selectedUsers.filter(u => u.id !== user.id)">✖</button>
                    </div>
                </template>
            </div>

            <button type="submit" class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                Assign Users
            </button>
        </div>
    </form>

    <form action="{{ route('projects.update', $project) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Project Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}" required>
        </div>

        <div>
            <label for="project_lead">Project Lead</label>
            <select name="project_lead_id" id="project_lead" class="w-full border rounded">
                <option value="">-- Select Project Lead --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ $project->project_lead_id == $user->id ? 'selected' : '' }}>
                        {{ $user->first_name }} {{ $user->last_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="description">Description</label>
            <textarea name="description" id="description" required>{{ old('description', $project->description) }}</textarea>
        </div>

        <div>
            <label for="status">Status</label>
            <select name="status" id="status" required>
                <option value="open" {{ $project->status == 'open' ? 'selected' : '' }}>Open</option>
                <option value="in-progress" {{ $project->status == 'in-progress' ? 'selected' : '' }}>In Progress</option>
                <option value="completed" {{ $project->status == 'completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>

        <button type="submit">Update Project</button>
    </form>
    <script>
        document.getElementById('assignUsersForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch("{{ route('projects.assignUsers', $project->id) }}", {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Users assigned successfully!");
                    location.reload(); // Refresh to update UI
                } else {
                    alert("Error assigning users.");
                }
            })
            .catch(error => console.error('Error:', error));
        });
        </script>

@endsection
