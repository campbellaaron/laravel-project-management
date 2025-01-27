@extends('layouts.app')

@section('content')
    <ul>
        @foreach ($users as $user)
            <li value="{{ $user->id }}">{{ $user->name }}</li>
        @endforeach
    </ul>
@endsection
