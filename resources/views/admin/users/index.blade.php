@extends('layouts.app')

@section('content')

<div class="p-6">

    <h1 class="text-xl font-bold mb-4">Users</h1>

    <a href="{{ route('users.create') }}" class="bg-blue-500 text-white px-3 py-2">
        + Add User
    </a>

    <table border="1" width="100%" class="mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Designation</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->designation->name ?? 'N/A' }}</td>

                <td>
                    <a href="{{ route('users.edit', $user->id) }}">Edit</a>

                    <form method="POST" action="{{ route('users.destroy', $user->id) }}">
                        @csrf
                        @method('DELETE')
                        <button onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $users->links() }}

</div>

@endsection