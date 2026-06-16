<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- Left Section -->
            <div class="flex">

                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:ms-10 space-x-8">

                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('departments.index')" :active="request()->routeIs('departments.*')">
                        {{ __('Departments') }}
                    </x-nav-link> 

                </div>
            </div>

            <!-- Right Section (Desktop User Menu) -->
            @auth
            <div class=" hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">

                    <!-- Trigger -->
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm text-gray-500 bg-white dark:bg-gray-800 hover:text-gray-700 transition">

                            <!-- Show Department -->
                            <div>
                                {{ Auth::user()?->department?->name ?? 'No Department' }}
                            </div>

                            <div class="ms-1">
                                <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4z" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <!-- Dropdown Content -->
                    <x-slot name="content">

                        <!-- User Info -->
                        <div class="px-4 py-2">
                            <div class="font-medium text-gray-800 dark:text-gray-200">
                                {{ Auth::user()?->name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ Auth::user()?->email }}
                            </div>
                        </div>

                        <x-dropdown-link :href="route('    {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Logout -->
                        <form method=" POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest           </x-slot>
                </x-dropdown>
            </div>
            @endauth

            <!-- Hamburger (Mobile) -->
            <div class=" -me-2 flex items-center sm:hidden">
                                <button @click="open = ! open" class="p-2 text-gray-400 hover:text-gray-500">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24">
                                        <path :class="{'hidden': open, 'inline-flex': ! open}" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 6h16M4 12h16M4 18h16" />

                                        <path :class="{'hidden': ! open, 'inline-flex': open}" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
            </div>

        </div>
    </div>

    <!-- Responsive Navigation -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">

        <!-- Links -->
        <div class="pt-2 pb-3 space-y-1">

            <x-responsive-nav-link :href="route('dashboard')" :activeoard') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('departments.index')"
                    :active="  </x-responsive-nav-link>

        </div>

        <!-- Mobile User Info -->
        @auth
        <div class=" pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">

                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                            {{ Auth::user()?->name }}
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ Auth::user()?->email }}
                        </div>

                        <div class="text-sm text-blue-600">
                            {{ Auth::user()?->department?->name ?? 'No Department' }}
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">

                        <x-responsive-nav-link {{ __('Profile') }}
                            </x-responsive-nav-link>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-responsive-nav-link :href="route('logout')"
                                    onclick="  </form>

            </div>
        </div>
        @endauth

    </div>
</nav>