@extends('layouts.app')

@section('title', 'All Projects')

@section('content')

    <div class="container">
        <a href="{{ route('projects.create') }}" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Create New Project</a>
        <div class="p-6 px-0 text-gray-800 dark:text-slate-300">
            <table class="projects-table mt-4 w-full min-w-max table-auto text-left datatable">
              <thead class="bg-slate-700 dark:bg-slate-600 text-slate-950 dark:text-slate-100">
                <tr>
                  <th class="cursor-pointer border-y border-blue-gray-100 p-4 transition-colors hover:bg-gray-500">
                    <p class="antialiased font-sans text-sm text-blue-gray-900 flex items-center justify-between gap-2 font-normal leading-none opacity-70">Project <x-fluentui-chevron-up-down-24 class="h-4 w-4"/></p>
                  </th>
                  <th class="cursor-pointer border-y border-blue-gray-100 p-4 transition-colors hover:bg-gray-500">
                    <p class="antialiased font-sans text-sm text-blue-gray-900 flex items-center justify-between gap-2 font-normal leading-none opacity-70">Project Lead <x-fluentui-chevron-up-down-24 class="h-4 w-4"/></p>
                  </th>
                  <th class="cursor-pointer border-y border-blue-gray-100 p-4 transition-colors hover:bg-gray-500">
                    <p class="antialiased font-sans text-sm text-blue-gray-900 flex items-center justify-between gap-2 font-normal leading-none opacity-70">Project Members <x-fluentui-chevron-up-down-24 class="h-4 w-4"/></p>
                  </th>
                  <th class="cursor-pointer border-y border-blue-gray-100 p-4 transition-colors hover:bg-gray-500">
                    <p class="antialiased font-sans text-sm text-blue-gray-900 flex items-center justify-between gap-2 font-normal leading-none opacity-70">Number of Tasks <x-fluentui-chevron-up-down-24 class="h-4 w-4"/></p>
                  </th>
                  <th class="cursor-pointer border-y border-blue-gray-100 p-4 transition-colors hover:bg-gray-500">
                    <p class="antialiased font-sans text-sm text-blue-gray-900 flex items-center justify-between gap-2 font-normal leading-none opacity-70">Total Time Tracked <x-fluentui-chevron-up-down-24 class="h-4 w-4"/></p>
                  </th>
                  <th class="cursor-pointer border-y border-blue-gray-100 p-4 transition-colors hover:bg-gray-500">
                    <p class="antialiased font-sans text-sm text-blue-gray-900 flex items-center justify-between gap-2 font-normal leading-none opacity-70">Status <x-fluentui-chevron-up-down-24 class="h-4 w-4"/></p>
                  </th>
                  <th class="cursor-pointer border-y border-blue-gray-100 p-4 transition-colors hover:bg-gray-500">
                    <p class="antialiased font-sans text-sm text-blue-gray-900 flex items-center justify-between gap-2 font-normal leading-none opacity-70">Deadline <x-fluentui-chevron-up-down-24 class="h-4 w-4"/></p>
                  </th>
                  <th class="border-y border-blue-gray-100 p-4">
                    <p class="antialiased font-sans text-sm text-blue-gray-900 flex items-center justify-between gap-2 font-normal leading-none opacity-70">Actions</p>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-slate-600 dark:bg-slate-800 text-gray-800 dark:text-slate-300">
                @foreach ($projects as $project)
                @php
                    if ($project->status === "open") {
                        $project_status = "open";
                    } else if ($project->status === "in-progress") {
                        $project_status = "in-progress";
                    } else {
                        $project_status = "project-table-status";
                    }
                @endphp
                    <tr>
                        <td class="p-4 border-b border-blue-gray-50">
                            <div class="flex items-center gap-3">
                              <div class="flex flex-col">
                                <p class="block antialiased font-sans text-sm leading-normal text-blue-gray-900 font-normal">{{ $project->name }}</p>
                                <p class="block antialiased font-sans text-sm leading-normal text-blue-gray-900 font-normal opacity-70">Start date: {{ $project->start_date->format("M d, Y") }}</p>
                              </div>
                            </div>
                        </td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <div class="flex items-center gap-3">
                              <div class="flex flex-col">
                                <p class="block antialiased font-sans text-sm leading-normal text-blue-gray-900 font-normal">{{ $project->projectLead ? $project->projectLead->full_name : 'Not Assigned' }}</p>
                                <p class="block antialiased font-sans text-sm leading-normal text-blue-gray-900 font-normal opacity-70">{{ $project->projectLead ? $project->projectLead->email : '' }}</p>
                              </div>
                            </div>
                        </td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <div class="flex items-center gap-0">
                                @if ($project->users->count() > 4)
                                    @foreach ($project->users->take(4) as $user)
                                        <span><img src="{{ $user->avatar }}" alt="{{ $user->full_name }}" class="w-4 h-4 rounded-full" /></span>
                                    @endforeach
                                    <span>+{{ $project->users->count() - 4 }} more</span>
                                @else
                                    @foreach ($project->users as $user)
                                        <span><img src="{{ $user->avatar }}" alt="{{ $user->full_name }}" class="w-6 h-6 rounded-full" title="{{ $user->full_name }}" /></span>
                                    @endforeach
                                @endif
                            </div>
                        </td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <div class="flex flex-col">
                              <p class="block antialiased font-sans text-sm text-center leading-normal text-blue-gray-900 font-normal">{{ $project->tasks_count }}</p>
                            </div>
                        </td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <div class="flex flex-col">
                                <p class="block antialiased font-sans text-sm text-center leading-normal text-blue-gray-900 font-normal">
                                    @php
                                        $totalSeconds = max(0, $task->totalTrackedTime()); // Ensure no negatives
                                        $hours = intdiv($totalSeconds, 3600);
                                        $minutes = intdiv($totalSeconds % 3600, 60);
                                        $seconds = $totalSeconds % 60;
                                    @endphp
                                    {{ sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds) }}
                                </p>
                            </div>
                        </td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <div class="w-max">
                              <div class="relative grid items-center font-sans font-bold uppercase whitespace-nowrap select-none {{$project_status}} py-1 px-2 text-xs rounded-md" style="opacity: 1;">
                                <span>{{ ucwords(str_replace('-', ' ', $project->status)) }}</span>
                              </div>
                            </div>
                        </td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <p class="block antialiased font-sans text-sm leading-normal text-blue-gray-900 font-normal">Deadline: {{ $project->due_date ? $project->due_date->format("M d, Y") : "None" }}</p>
                        </td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <div class="flex justify-evenly items-end">
                                <a href="{{ route('projects.show', $project->id) }}" class="rounded-md bg-blue-600 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-blue-700 focus:shadow-none active:bg-blue-700 hover:bg-blue-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2" title="View Project"><x-fluentui-task-list-square-rtl-24-o class="h-6 w-6"/></a>
                            </div>
                        </td>
                    </tr>
                @endforeach
              </tbody>
            </table>
          </div>
    </div>

@endsection
