@extends('layouts.app')


@section('content')
    <h2>Create New User</h2>

    <form action="{{ route('users.store')}}" method="post">
        @csrf
        <div>

        </div>
    </form>

@endsection
