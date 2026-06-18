@extends('layouts.app')

@section('content')

<div class="container">

    <h2>Create File</h2>


    @if ($errors->any())
    <div style="color:red;">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form action="{{ route('files.store') }}" method="POST">

        @csrf

        @if(auth()->user()->role == 'super_admin')

        <select name="department_id" class="form-control">
            @foreach($departments as $department)
            <option value="{{ $department->id }}">
                {{ $department->name }}
            </option>
            @endforeach
        </select>

        @else

        <input
            type="text"
            class="form-control"
            value="{{ auth()->user()->department->name }}"
            readonly>

        <input
            type="hidden"
            name="department_id"
            value="{{ auth()->user()->department_id }}">

        @endif
        <div class="mb-3">
            <label>File Name</label>

            <input
                type="text"
                name="file_name"
                class="form-control"
                required>
        </div>

        <div class="mb-3">
            <label>Remarks</label>

            <textarea name="remarks"
                class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-success">
            Save File
        </button>

    </form>

</div>

@endsection