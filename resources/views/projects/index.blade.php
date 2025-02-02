@extends('layouts.app')

@section('title', 'All Projects')

@section('content')

    <div class="container">
        <a href="{{ route('projects.create') }}" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Create New Project</a>
        <div class="p-6 px-0">
            <table class="mt-4 w-full min-w-max table-auto text-left">
              <thead class="bg-slate-700 dark:bg-slate-600 text-slate-950 dark:text-slate-100">
                <tr>
                  <th class="cursor-pointer border-y border-blue-gray-100 p-4 transition-colors hover:bg-gray-500">
                    <p class="antialiased font-sans text-sm text-blue-gray-900 flex items-center justify-between gap-2 font-normal leading-none opacity-70">Project <x-fluentui-chevron-up-down-24 class="h-4 w-4"/></p>
                  </th>
                  <th class="cursor-pointer border-y border-blue-gray-100 p-4 transition-colors hover:bg-gray-500">
                    <p class="antialiased font-sans text-sm text-blue-gray-900 flex items-center justify-between gap-2 font-normal leading-none opacity-70">Team Lead <x-fluentui-chevron-up-down-24 class="h-4 w-4"/></p>
                  </th>
                  <th class="cursor-pointer border-y border-blue-gray-100 p-4 transition-colors hover:bg-gray-500">
                    <p class="antialiased font-sans text-sm text-blue-gray-900 flex items-center justify-between gap-2 font-normal leading-none opacity-70">Function <x-fluentui-chevron-up-down-24 class="h-4 w-4"/></p>
                  </th>
                  <th class="cursor-pointer border-y border-blue-gray-100 p-4 transition-colors hover:bg-gray-500">
                    <p class="antialiased font-sans text-sm text-blue-gray-900 flex items-center justify-between gap-2 font-normal leading-none opacity-70">Status <x-fluentui-chevron-up-down-24 class="h-4 w-4"/></p>
                  </th>
                  <th class="cursor-pointer border-y border-blue-gray-100 p-4 transition-colors hover:bg-gray-500">
                    <p class="antialiased font-sans text-sm text-blue-gray-900 flex items-center justify-between gap-2 font-normal leading-none opacity-70">Deadline <x-fluentui-chevron-up-down-24 class="h-4 w-4"/></p>
                  </th>
                  <th class="cursor-pointer border-y border-blue-gray-100 p-4 transition-colors hover:bg-gray-500">
                    <p class="antialiased font-sans text-sm text-blue-gray-900 flex items-center justify-between gap-2 font-normal leading-none opacity-70">Actions</p>
                  </th>
                </tr>
              </thead>
              <tbody class="bg-slate-600 dark:bg-slate-800 text-gray-800 dark:text-slate-300">
                @foreach ($projects as $project)
                    <tr>
                        <td class="p-4 border-b border-blue-gray-50">
                            <div class="flex items-center gap-3">
                              <div class="flex flex-col">
                                <p class="block antialiased font-sans text-sm leading-normal text-blue-gray-900 font-normal">{{ $project->name }}</p>
                                <p class="block antialiased font-sans text-sm leading-normal text-blue-gray-900 font-normal opacity-70">Start date: 10 Dec 2023</p>
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
                            <div class="flex flex-col">
                              <p class="block antialiased font-sans text-sm leading-normal text-blue-gray-900 font-normal">Manager</p>
                              <p class="block antialiased font-sans text-sm leading-normal text-blue-gray-900 font-normal opacity-70">Organization</p>
                            </div>
                        </td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <div class="w-max">
                              <div class="relative grid items-center font-sans font-bold uppercase whitespace-nowrap select-none project-table-status bg-green-500/20 text-green-600 py-1 px-2 text-xs rounded-md" style="opacity: 1;">
                                <span>{{ ucwords(str_replace('-', ' ', $project->status)) }}</span>
                              </div>
                            </div>
                        </td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <p class="block antialiased font-sans text-sm leading-normal text-blue-gray-900 font-normal">23/04/18</p>
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
