@extends('layouts.app')

@section('content')
<div class="p-6">

    <h1>Create User</h1>

    <form method="POST" action="{{ route('users.store') }}">
        @csrf

        <input type="text" name="name" placeholder="Name" class="border p-2 w-full mb-2">

        <input type="email" name="email" placeholder="Email" class="border p-2 w-full mb-2">

        <input type="password" name="password" placeholder="Password" class="border p-2 w-full mb-2">

        <select name="designation_id" class="border p-2 w-full mb-2">
            @foreach($designations as $des)
            <option value="{{ $des->id }}">{{ $des->name }}</option>
            @endforeach
        </select>

        <button class="bg-green-600 text-white px-4 py-2">
            Save
        </button>

    </form>

</div>
@endsection