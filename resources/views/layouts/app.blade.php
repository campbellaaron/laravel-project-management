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

                <h1 class="bg-white dark:bg-gray-800 shadow text-4xl p-5 mb-6 font-extrabold text-slate-900 dark:text-slate-200">@yield('title')</h1>

                <!-- Page Content -->
                <main class="md:w-[85vw] w-[50vw] md:mx-auto mx-2">
                    @if (@session()->has('success'))
                        <div>
                            <div role="alert" id="alert" class="mb-4 relative flex w-full p-3 text-sm text-white bg-green-600 rounded-md">
                                {{ session('success') }}
                            <button class="flex items-center justify-center transition-all w-8 h-8 rounded-md text-white hover:bg-white/10 active:bg-white/10 absolute top-1.5 right-1.5" type="button" onclick="closeAlert()">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                          </div>
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
        <script>
            function closeAlert() {
              const alertElement = document.getElementById('alert');
              alertElement.style.display = 'none'; // Hides the alert
            }
          </script>
    </body>
</html>
