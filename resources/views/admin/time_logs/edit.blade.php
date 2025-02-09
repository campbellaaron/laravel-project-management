@extends('layouts.app')

@section('title', 'Edit Time Log')

@section('content')
<div class="container">
    <h2 class="text-2xl font-bold mb-4">Edit Time Log</h2>

    <form action="{{ route('admin.time-logs.update', $timeEntry->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="started_at" class="block">Start Time</label>
            <input type="datetime-local" name="started_at" id="started_at" value="{{ $timeEntry->started_at->format('Y-m-d\TH:i') }}" class="border p-2 w-full">
        </div>

        <div class="mb-4">
            <label for="ended_at" class="block">End Time</label>
            <input type="datetime-local" name="ended_at" id="ended_at" value="{{ $timeEntry->ended_at ? $timeEntry->ended_at->format('Y-m-d\TH:i') : '' }}" class="border p-2 w-full">
        </div>

        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Update</button>
    </form>
</div>
@endsection
