@extends('layouts.app')

@section('content')

<div class="p-6">

    <h1>Edit User</h1>

    <form method="POST" action="{{ route('users.update', $user->id) }}">
        @csrf
        @method('PUT')

        <input type="text" name="name" value="{{ $user->name }}" class="border p-2 w-full mb-2">

        <input type="email" name="email" value="{{ $user->email }}" class="border p-2 w-full mb-2">

        <select name="designation_id" class="border p-2 w-full mb-2">
            @foreach($designations as $des)
            <option value="{{ $des->id }}"
                {{ $user->designation_id == $des->id ? 'selected' : '' }}>
                {{ $des->name }}
            </option>
            @endforeach
        </select>

        <button class="bg-blue-500 text-white px-4 py-2">
            Update
        </button>

    </form>

</div>

@endsection