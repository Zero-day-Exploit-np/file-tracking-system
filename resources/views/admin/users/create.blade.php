@extends('layouts.app')

@section('content')

<div class="container">

    <h2>Create User</h2>
    @if ($errors->any())
    <div>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
        @csrf

        <input type="text" name="name" placeholder="Name" class="form-control mb-2">

        <input type="text" name="contact_number" placeholder="Contact Number" class="form-control mb-2">

        <input type="email" name="email" placeholder="Email" class="form-control mb-2">

        <input type="password" name="password" placeholder="Password" class="form-control mb-2">

        <div class="mb-2">
            <label>Designation</label>
            <select name="designation_id" class="form-control" required>
                <option value="">Select Designation</option>
                @foreach($designations as $designation)
                <option value="{{ $designation->id }}">
                    {{ $designation->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-2">
            <label>Photo (Optional)</label>
            <input type="file" name="photo" class="form-control">
        </div>

        <div class="mb-2">
            <label>
                <input type="checkbox" name="can_create_file" value="1">
                File creation permission
            </label>
        </div>

        <button class="btn btn-primary">Save User</button>
    </form>
</div>

@endsection