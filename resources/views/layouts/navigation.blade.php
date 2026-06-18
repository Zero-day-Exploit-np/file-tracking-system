<nav x-data="{ open: false }">

    {{-- Desktop Navigation --}}
    <div>

        {{-- Logo --}}
        <a href="{{ route('dashboard') }}">
            LOGO
        </a>

        @auth

        {{-- Common Links (all roles) --}}
        <x-nav-link :href="route('dashboard')">
            Dashboard
        </x-nav-link>

        <x-nav-link :href="route('files.index')">
            Files
        </x-nav-link>

        {{-- SUPER ADMIN --}}
        @if(auth()->user()->role === 'super_admin')

        <x-nav-link :href="route('departments.index')">
            Departments
        </x-nav-link>

        <x-nav-link :href="route('designations.index')">
            Designations
        </x-nav-link>

        <x-nav-link :href="route('users.index')">
            Admin Users
        </x-nav-link>

        <x-nav-link :href="route('users.create')">
            Create Admin
        </x-nav-link>

        {{-- ADMIN --}}
        @elseif(auth()->user()->role === 'admin')

        <x-nav-link :href="route('admin.dashboard')">
            Admin Dashboard
        </x-nav-link>

        <x-nav-link :href="route('admin.users.index')">
            Users
        </x-nav-link>

        <x-nav-link :href="route('admin.files')">
            Department Files
        </x-nav-link>
        <x-nav-link :href="route('designations.index')">
            Designations
        </x-nav-link>

        <x-nav-link :href="route('admin.transfer.requests')">
            Transfer Requests
        </x-nav-link>

        {{-- USER --}}
        @elseif(auth()->user()->role === 'user')

        <x-nav-link :href="route('files.index')">
            My Files
        </x-nav-link>
        @endif

        {{-- Profile & Logout (all roles) --}}
        <x-nav-link :href="route('profile.edit')">
            Profile
        </x-nav-link>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">
                Logout
            </button>
        </form>

        @endauth

    </div>


    {{-- Mobile Menu --}}
    <div x-show="open">

        @auth

        {{-- Common Links (all roles) --}}
        <x-responsive-nav-link :href="route('dashboard')">
            Dashboard
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('files.index')">
            Files
        </x-responsive-nav-link>

        {{-- SUPER ADMIN --}}
        @if(auth()->user()->role === 'super_admin')

        <x-responsive-nav-link :href="route('departments.index')">
            Departments
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('designations.index')">
            Designations
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('users.index')">
            Admin Users
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('users.create')">
            Create Admin
        </x-responsive-nav-link>

        {{-- ADMIN --}}
        @elseif(auth()->user()->role === 'admin')

        <x-responsive-nav-link :href="route('admin.dashboard')">
            Admin Dashboard
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('admin.users.index')">
            Users
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('designations.index')">
            Designations
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('admin.files')">
            Department Files
        </x-responsive-nav-link>

        <x-responsive-nav-link :href="route('admin.transfer.requests')">
            Transfer Requests
        </x-responsive-nav-link>

        {{-- USER --}}
        @elseif(auth()->user()->role === 'user')

        <x-responsive-nav-link :href="route('files.index')">
            My Files
        </x-responsive-nav-link>

        @endif

        {{-- Profile & Logout (all roles) --}}
        <x-responsive-nav-link :href="route('profile.edit')">
            Profile
        </x-responsive-nav-link>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">
                Logout
            </button>
        </form>

        @endauth

    </div>

</nav>