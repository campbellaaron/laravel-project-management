@extends('layouts.app')

@section('title', 'Teams')

@section('content')
    <div class="my-4 py-3">
        <a href="{{ route('teams.create') }}" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Create New Team</a>
    </div>
    <div class="text-gray-800 dark:text-slate-300">
        <table class="datatable teams-table w-full mt-4 text-left">
            <thead class="bg-slate-700 dark:bg-slate-600 text-slate-950 dark:text-slate-100">
                <tr>
                    <th scope="col" class="cursor-pointer border-y border-blue-gray-100 p-4 transition-colors hover:bg-gray-500"><p class="flex items-center justify-between gap-2">Team Name <x-fluentui-chevron-up-down-24 class="h-4 w-4"/></p></th>
                    <th scope="col" class="cursor-pointer border-y border-blue-gray-100 p-4 transition-colors hover:bg-gray-500"><p class="flex items-center justify-between gap-2">Members <x-fluentui-chevron-up-down-24 class="h-4 w-4"/></p></th>
                    <th scope="col" class="cursor-pointer border-y border-blue-gray-100 p-4 transition-colors hover:bg-gray-500"><p class="flex items-center justify-between gap-2">Created <x-fluentui-chevron-up-down-24 class="h-4 w-4"/></p></th>
                    <th scope="col" class="border-y border-blue-gray-100 p-4">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-slate-600 dark:bg-slate-800 text-gray-800 dark:text-slate-300">
                @foreach ($teams as $team)
                    <tr>
                        <!-- Team Name -->
                        <td class="p-4 border-b border-blue-gray-50">
                            <p class="block font-sans text-sm text-blue-gray-900">{{ $team->name }}</p>
                        </td>

                        <!-- Members Column -->
                        <td class="p-4 border-b border-blue-gray-50">
                            <div class="flex items-center gap-2">
                                @php
                                    $members = $team->users->take(4); // Get up to 4 members
                                    $remaining = $team->users->count() - 4; // Get remaining count
                                @endphp

                                @foreach ($members as $member)
                                    <span class="bg-blue-500 text-white text-xs font-bold rounded-full px-2 py-1">
                                        {{ $member->first_name }}
                                    </span>
                                @endforeach

                                @if ($remaining > 0)
                                    <span class="bg-gray-500 text-white text-xs font-bold rounded-full px-2 py-1">
                                        +{{ $remaining }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Created Date -->
                        <td class="p-4 border-b border-blue-gray-50">
                            <p class="block font-sans text-sm text-blue-gray-900">
                                {{ $team->created_at->format('M d, Y') }}
                            </p>
                        </td>

                        <!-- Actions -->
                        <td class="p-4 border-b border-blue-gray-50">
                            <a href="{{ route('teams.show', $team->id) }}" class="text-blue-500">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
