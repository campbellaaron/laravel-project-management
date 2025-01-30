@extends('layouts.app')

@section('title', 'Create New User')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mt-8 bg-white dark:bg-slate-600 py-8 px-6 shadow rounded-lg sm:px-10">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
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
                    <label for="name" class="block text-md font-bold text-gray-800">Name</label>
                    <div class="mt-1">
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full border border-gray-800 px-3 py-2 rounded-lg shadow-sm focus:outline-none focus:border-indigo-700" required>
                        @error('name')
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
