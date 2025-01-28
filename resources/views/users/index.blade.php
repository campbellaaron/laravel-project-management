@extends('layouts.app')

@section('title', 'List of Users')
@section('content')
    <ul>
        @foreach ($users as $user)
            <li value="{{ $user->id }}">{{ $user->name }}</li>
        @endforeach
    </ul>
@endsection
