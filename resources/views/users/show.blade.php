@extends('layouts.app')

@section('title', $user->name)

@section('content')
    <div>
        <h3>User Roles</h3>
        <ul>
            @foreach($roles as $role)
                <li>{{ $role }}</li>
            @endforeach
        </ul>
        @if(auth()->user()->can('edit-users'))
            <button>Edit User</button>
        @endif

        @if(auth()->user()->hasPermissionTo('delete users'))
            <button>Delete User</button>
        @endif
    </div>
@endsection
