@extends('layouts.app')

@section('title', 'List of Users')
@section('content')
    <ul>
        @foreach ($users as $user)
            <li value="{{ $user->id }}">
                <a href="{{route('users.show', $user)}}" class="text-md text-slate-400">{{ $user->name }}</a>
            </li>
        @endforeach
    </ul>
@endsection
