<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="{{ mix('js/app.js') }}" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.js" defer></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex">
            <!-- Sidebar -->
            <div class="w-64 bg-gray-800 text-white">
                @include('components.dashboard-sidebar') <!-- Sidebar component or partial -->
            </div>

            <!-- Main Content -->
            <div class="flex-1">
                @include('layouts.navigation')

                <!-- Page Heading -->
                @isset($header)
                    <header class="bg-white dark:bg-gray-800 shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <h1 class="bg-white dark:bg-gray-800 shadow">@yield('title')</h1>

                <!-- Page Content -->
                <main class="md:w-[85vw] w-[50vw] md:mx-auto mx-2">
                    @if (@session()->has('success'))
                        <div>{{ session('success') }}</div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
