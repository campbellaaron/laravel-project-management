@extends('layouts.app')

@section('title', 'File Manager')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">File Manager</h2>
    <table class="min-w-full bg-white dark:bg-gray-800 shadow-md rounded-lg mt-4">
        <thead>
            <tr class="bg-gray-200 dark:bg-gray-700">
                <th class="px-4 py-2">File Name</th>
                <th class="px-4 py-2">Type</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($files as $file)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ basename($file) }}</td>
                    <td class="px-4 py-2">{{ pathinfo($file, PATHINFO_EXTENSION) }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ Storage::url($file) }}" target="_blank" class="text-blue-500">View</a>
                        <form action="{{ route('files.delete', ['file' => $file]) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 ml-2">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
