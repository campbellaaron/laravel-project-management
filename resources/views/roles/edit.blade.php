@extends('layouts.app')

@section('title', 'Edit Role')

@section('content')
    <div class="bg-gray-200 dark:bg-gray-900">
        @if ($errors->any())
            <div role="alert" class="mb-4 relative flex w-full p-3 text-sm text-white bg-red-600 rounded-md">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button class="flex items-center justify-center transition-all w-8 h-8 rounded-md text-white hover:bg-white/10 active:bg-white/10 absolute top-1.5 right-1.5" type="button" onclick="closeAlert()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif
        <form action="{{ route('roles.update', $role->id) }}" method="POST" class="bg-white dark:bg-slate-700 shadow-md rounded px-8 pt-6 pb-8 mb-4 max-w-3xl mx-auto">
            @csrf
            @method('PUT')

            <!-- Role Name Input -->
            <div class="mb-4">
                <label for="role_name" class="block text-sm font-medium text-gray-700 dark:text-indigo-50">Role Name</label>
                <input type="text" name="role_name" id="role_name" value="{{ old('role_name', $role->name) }}" class="mt-1 p-2 block w-full rounded-md border outline-none border-gray-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required>
                @error('role_name')
                    <p class="alert alert-danger text-xs">{{$message}}</p>
                @enderror
            </div>
            <!-- Permissions Checkboxes -->
            <div class="mb-4 p-4 me-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 relative text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-900 shadow-md rounded-xl text-sm md:text-base lg:text-lg bg-clip-border">
                    <div class="w-[50%] p-5">
                        <h3 class="text-xl text-slate-700 dark:text-slate-200 py-4 mb-3 font-bold">Roles</h3>
                        <table class="text-left table-auto min-w-max">
                            <thead>
                                <tr>
                                    <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
                                        <p>{{ __('Name')}}</p>
                                    </th>
                                    <th class="p-4 border-b border-blue-gray-100 bg-blue-gray-50">
                                        <p>{{ __('Permission') }}</p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($role_permissions as $permission)
                                    <tr>
                                        <td class="p-4 border-b border-blue-gray-50">
                                            <p>{{ $permission->name }}</p>
                                        </td>
                                        <td class="styled-checkbox flex justify-center p-5">
                                            <input type="checkbox" name="permission[]" value="{{ $permission->name }}" id="permission_{{ $permission->name }}" class="peer h-5 w-5 cursor-pointer transition-all appearance-none rounded shadow hover:shadow-md border border-slate-300 checked:bg-slate-800 checked:border-slate-800" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                            <label for="permission_{{ $permission->name }}" class="styled-checkmark"></label>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="w-full p-5">
                        <h3 class="text-xl text-slate-100 py-4 mb-3 font-bold">Users</h3>
                        <select id="userlist" name="users[]" class="bg-slate-300  text-slate-700 text-sm md:text-base lg:text-lg border border-slate-200 rounded p-4 transition duration-300 ease focus:outline-none focus:border-slate-400 hover:border-slate-400 shadow-sm focus:shadow-md appearance-none cursor-pointer min-w-[50px]" multiple>
                            @foreach ($users as $user)
                                <option value="{{$user->id}}" class="text-slate-900">{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <button type="submit" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Update Role</button>
        </form>
    </div>
@endsection
