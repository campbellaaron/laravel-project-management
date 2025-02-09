@extends('layouts.app')

@section('title', isset($team) ? 'Edit Team' : 'Create Team')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ isset($team) ? 'Edit Team' : 'Create a New Team' }}</h2>

        <form action="{{ isset($team) ? route('teams.update', $team) : route('teams.store') }}" method="POST" class="space-y-4">
            @csrf
            @isset($team)
                @method('PUT')
            @endisset

            <!-- Team Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Team Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', isset($team) ? $team->name : '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                    required>
            </div>

            <!-- Select Users -->
            <div>
                <label for="users" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Assign Users</label>
                <select name="users[]" id="users" multiple
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ isset($team) && $team->users->contains($user->id) ? 'selected' : '' }}>
                            {{ $user->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md shadow-md transition-all">
                    {{ isset($team) ? 'Update Team' : 'Create Team' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
