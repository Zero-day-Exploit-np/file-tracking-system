<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QFh8VgI21PFS/XKLQYJq5aRl88VktB+CRfXdf7PcXr2E9aw3n86ksallFFX12cod" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div>
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
        <header>
            <div>
                {{ $header }}
            </div>
        </header>
        @endisset
        @if(auth()->check() && auth()->user()->role == 'admin')
        <div class="container mt-3">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-primary">Admin Dashboard</a>
        </div>
        @endif
        <main class="py-4">
            @yield('content')
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-HoA+CQ9/j3zYYzKg0U8a+RbxqSBOW5hwG5V0qMVF5C7wL+7z/gfzxW2cA8QDpbFG" crossorigin="anonymous"></script>
</body>

</html>