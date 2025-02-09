@extends('layouts.app')

@section('title', 'List of Users')
@section('content')
<div class="relative flex flex-col w-full h-full text-gray-700 bg-white shadow-md rounded-xl px-4 py-2 bg-clip-border">
    <div class="relative mx-4 mt-4 overflow-hidden text-gray-700 bg-white rounded-none bg-clip-border">
        <div class="flex items-center justify-between gap-8 mb-8">
            <a href="{{route('users.create')}}"
                class="flex select-none items-center gap-3 rounded-lg bg-gray-900 py-2 px-4 text-center align-middle font-sans text-xs font-bold uppercase text-white shadow-md shadow-gray-900/10 transition-all hover:shadow-lg hover:shadow-gray-900/20 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                role="button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"
                    stroke-width="2" class="w-4 h-4">
                    <path
                        d="M6.25 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM3.25 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM19.75 7.5a.75.75 0 00-1.5 0v2.25H16a.75.75 0 000 1.5h2.25v2.25a.75.75 0 001.5 0v-2.25H22a.75.75 0 000-1.5h-2.25V7.5z">
                    </path>
                </svg>
                Add member
            </a>
        </div>
        <div class="flex flex-col items-center justify-between gap-4 md:flex-row">
            <div class="block w-full overflow-hidden md:w-max">

            </div>
            <div class="w-full md:w-72">

            </div>
        </div>
    </div>
    <div class="p-6 px-0 overflow-scroll">
        <table class="users-table w-full mt-4 text-left table-auto min-w-max datatable">
            <thead>
                <tr>
                    <th class="cursor-pointer p-4 border-y border-blue-gray-100 bg-blue-gray-50/50 transition-colors hover:bg-gray-300">
                        <p
                            class="flex items-center justify-between gap-2 block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                            Member <x-fluentui-chevron-up-down-24 class="h-4 w-4"/>
                        </p>
                    </th>
                    <th class="cursor-pointer p-4 border-y border-blue-gray-100 bg-blue-gray-50/50 transition-colors hover:bg-gray-300">
                        <p
                            class="flex items-center justify-between gap-2 block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                            Team <x-fluentui-chevron-up-down-24 class="h-4 w-4"/>
                        </p>
                    </th>
                    <th class="cursor-pointer p-4 border-y border-blue-gray-100 bg-blue-gray-50/50 transition-colors hover:bg-gray-300">
                        <p
                            class="flex items-center justify-between gap-2 block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                            Status <x-fluentui-chevron-up-down-24 class="h-4 w-4"/>
                        </p>
                    </th>
                    <th class="cursor-pointer p-4 border-y border-blue-gray-100 bg-blue-gray-50/50 transition-colors hover:bg-gray-300">
                        <p
                            class="flex items-center justify-between gap-2 block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                            Hire Date <x-fluentui-chevron-up-down-24 class="h-4 w-4"/>
                        </p>
                    </th>
                    <th class="p-4 border-y border-blue-gray-100 bg-blue-gray-50/50">
                        <p
                            class="flex items-center justify-between gap-2 block font-sans text-sm antialiased font-normal leading-none text-blue-gray-900 opacity-70">
                        </p>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td class="p-4 border-b border-blue-gray-50">
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->avatar }}"
                                    alt="{{$user->full_name}}"
                                    class="relative inline-block h-9 w-9 !rounded-full object-cover object-center" />
                                <div class="flex flex-col">
                                    <p
                                        class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                                        {{$user->full_name}}
                                    </p>
                                    <p
                                        class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900 opacity-70">
                                        {{$user->email}}
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <div class="flex flex-col">
                                <p
                                    class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                                    {{ $user->team_names ?: 'No Team' }}
                                </p>
                                @if (auth()->user()->hasRole('super-admin'))
                                    <p
                                        class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900 opacity-70">
                                        {{ $user->getRoleNames()->join(', ') }}
                                    </p>
                                @endif
                            </div>
                        </td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <div class="w-max">
                                <div
                                    class="relative grid items-center px-2 py-1 font-sans text-xs font-bold text-green-900 uppercase rounded-md select-none whitespace-nowrap bg-green-500/20">
                                    <span class="">online</span>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
                                {{$user->created_at->format('F d, Y')}}
                            </p>
                        </td>
                        <td class="p-4 border-b border-blue-gray-50">
                            <a href="{{route('users.show', $user)}}"
                                class="text-md select-none rounded-lg text-center align-middle font-sans text-xs font-medium uppercase text-gray-900 dark:text-slate-400 transition-all hover:bg-gray-900/10 active:bg-gray-900/20 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"><x-fluentui-person-edit-20-o
                                    class="h-6 w-6" /></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="flex items-center justify-between p-4 border-t border-blue-gray-50">
        <p class="block font-sans text-sm antialiased font-normal leading-normal text-blue-gray-900">
            Page 1 of 10
        </p>
        <div class="flex gap-2">
            <button
                class="select-none rounded-lg border border-gray-900 py-2 px-4 text-center align-middle font-sans text-xs font-bold uppercase text-gray-900 transition-all hover:opacity-75 focus:ring focus:ring-gray-300 active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                type="button">
                Previous
            </button>
            <button
                class="select-none rounded-lg border border-gray-900 py-2 px-4 text-center align-middle font-sans text-xs font-bold uppercase text-gray-900 transition-all hover:opacity-75 focus:ring focus:ring-gray-300 active:opacity-[0.85] disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                type="button">
                Next
            </button>
        </div>
    </div>
</div>
@endsection
