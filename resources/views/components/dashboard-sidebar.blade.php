<div class="h-full p-4">
    <div class="flex">
        <!-- Logo -->
        <div class="shrink-0 flex items-center">
            <a href="{{ route('dashboard') }}" class="">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-600 dark:text-gray-200 size-8" />
            </a>
        </div>
    </div>

    <div class="flex flex-col justify-between">
        <div class="bg-cyan-300 dark:bg-gray-800">
            <ul class="space-y-4 pt-8">
                <li>
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-gray-800 dark:text-gray-300 hover:text-gray-600 dark:hover:text-white inline-flex">
                        <x-solar-home-linear class="w-5 h-5 mr-2"/> {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                </li>
                <li>
                    <x-responsive-nav-link :href="route('projects.index')"  :active="request()->routeIs('projects')"  class="text-gray-800 dark:text-gray-300 hover:text-gray-600 dark:hover:text-white inline-flex">
                        <x-ri-folder-chart-line class="w-5 h-5 mr-2"/> {{ __('Projects') }}
                    </x-responsive-nav-link>
                </li>
                <li>
                    <x-responsive-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks')"  class="text-gray-800 dark:text-gray-300 hover:text-gray-600 dark:hover:text-white inline-flex">
                        <x-fluentui-tasks-app-24-o class="w-5 h-5 mr-2"/> {{ __('Tasks') }}
                    </x-responsive-nav-link>
                </li>
                <li>
                    <x-responsive-nav-link :href="route('calendar.index')" :active="request()->routeIs('calendar')"  class="text-gray-800 dark:text-gray-300 hover:text-gray-600 dark:hover:text-white inline-flex">
                        <x-solar-calendar-line-duotone class="w-5 h-5 mr-2"/> {{ __('Calendar') }}
                    </x-responsive-nav-link>
                </li>
                @if (auth()->user()->hasAnyRole(['super-admin', 'admin', 'manager']))
                <!-- Only show these to non-users -->
                <li>
                    <x-responsive-nav-link :href="route('users.index')"  :active="request()->routeIs('users')"  class="text-gray-800 dark:text-gray-300 hover:text-gray-600 dark:hover:text-white inline-flex">
                        <x-solar-users-group-two-rounded-linear class="w-5 h-5 mr-2"/> {{ __('Users') }}
                    </x-responsive-nav-link>
                </li>
                @endif
                @if (auth()->user()->hasAnyRole(['super-admin', 'admin']))

                <!-- Only show these to admins -->
                <li>
                    <x-responsive-nav-link :href="route('teams.index')"  :active="request()->routeIs('teams')"  class="text-gray-800 dark:text-gray-300 hover:text-gray-600 dark:hover:text-white inline-flex">
                        <x-fluentui-contact-card-group-28-o class="w-5 h-5 mr-2"/> {{ __('Teams') }}
                    </x-responsive-nav-link>
                </li>
                <li>
                    <x-responsive-nav-link :href="route('reports.index')" :active="request()->routeIs('Reports')"  class="text-gray-800 dark:text-gray-300 hover:text-gray-600 dark:hover:text-white inline-flex">
                        <x-solar-chart-linear class="w-5 h-5 mr-2"/> {{ __('Reports') }}
                    </x-responsive-nav-link>
                </li>

                @endif
                <li>
                    <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile')"  class="text-gray-800 dark:text-gray-300 hover:text-gray-600 dark:hover:text-white inline-flex">
                        <x-carbon-user-profile class="w-5 h-5 mr-2"/> {{ __('Profile') }}
                    </x-responsive-nav-link>
                </li>
            </ul>
        </div>
        <div class="size-8 text-inherit flex justify-start items-end mt-8 relative">
            <!-- Display unread notifications count -->
            @if(auth()->user()->unreadNotifications->count() > 0)
            <div class="flex relative">
                <span class="badge">
                    {{ auth()->user()->unreadNotifications->count() }}
                </span>
                Notification&lpar;s&rpar;
            </div>
            @else
                <div class="flex">
                    <x-solar-notification-remove-line-duotone /> No New Notifications
                </div>
            @endif
        </div>
    </div>
</div>
