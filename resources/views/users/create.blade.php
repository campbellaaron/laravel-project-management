@extends('layouts.app')

@section('title', 'Create New User')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mt-8 bg-white dark:bg-slate-600 py-8 px-6 shadow rounded-lg sm:px-10">
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
            <form action="{{ route('users.store')}}" class="mb-0 space-y-6" method="post">
                @csrf
                <div>
                    <label for="email" class="block text-md font-bold text-gray-800">Email</label>
                    <div class="mt-1">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full border border-gray-800 px-3 py-2 rounded-lg shadow-sm focus:outline-none focus:border-indigo-700" required>
                        @error('email')
                            <p class="alert alert-danger text-xs">{{$message}}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="first_name" class="block text-md font-bold text-gray-800">First Name</label>
                    <div class="mt-1">
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" class="w-full border border-gray-800 px-3 py-2 rounded-lg shadow-sm focus:outline-none focus:border-indigo-700" required>
                        @error('first_name')
                            <p class="alert alert-danger text-xs">{{$message}}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="last_name" class="block text-md font-bold text-gray-800">First Name</label>
                    <div class="mt-1">
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" class="w-full border border-gray-800 px-3 py-2 rounded-lg shadow-sm focus:outline-none focus:border-indigo-700" required>
                        @error('last_name')
                            <p class="alert alert-danger text-xs">{{$message}}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="password" class="block text-md font-bold text-gray-800">Password</label>
                    <div class="mt-1">
                        <input type="password" name="password" id="password" class="w-full border border-gray-800 px-3 py-2 rounded-lg shadow-sm focus:outline-none focus:border-indigo-700" required>
                        @error('name')
                            <p class="alert alert-danger text-xs">{{$message}}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <label for="role" class="block text-md font-bold text-gray-800">Role</label>
                    <select name="role" id="role" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                        @error('name')
                            <p class="alert alert-danger text-xs">{{$message}}</p>
                        @enderror
                    </select>
                </div>
                <button type="submit" class="rounded-md bg-blue-600 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-blue-700 focus:shadow-none active:bg-blue-700 hover:bg-blue-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2 mx-auto" type="button">
                    Create User
                  </button>
            </form>

        </div>

    </div>

@endsection
