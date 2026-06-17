@extends('layouts.app')

@section('content')

<h2>Edit Designation</h2>

<form method="POST" action="{{ route('designations.update', $designation->id) }}">
    @csrf
    @method('PUT')

    <input type="text" name="name" value="{{ $designation->name }}">

    <select name="department_id">
        @foreach($departments as $dept)
        <option value="{{ $dept->id }}"
            {{ $designation->department_id == $dept->id ? 'selected' : '' }}>
            {{ $dept->name }}
        </option>
        @endforeach
    </select>

    <select name="is_active">
        <option value="1" {{ $designation->is_active ? 'selected' : '' }}>Active</option>
        <option value="0" {{ !$designation->is_active ? 'selected' : '' }}>Inactive</option>
    </select>

    <button type="submit">Update</button>
</form>

@endsection