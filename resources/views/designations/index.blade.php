@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-3">Designations List</h2>

    <a href="{{ route('designations.create') }}" class="btn btn-primary mb-3">
        + Add Designation
    </a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Department</th>
                <th>Designation Name</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($designations as $designation)
            <tr>
                <td>{{ $designation->id }}</td>

                <td>
                    {{ $designation->department->name ?? 'N/A' }}
                </td>

                <td>{{ $designation->name }}</td>

                <td>
                    @if($designation->is_active)
                    <span class="text-success">Active</span>
                    @else
                    <span class="text-danger">Inactive</span>
                    @endif
                </td>

                <td>
                    {{ $designation->created_at ?? 'N/A' }}
                </td>

                <td>
                    <a href="{{ route('designations.edit', $designation->id) }}" class="btn btn-sm btn-warning">
                        Edit
                    </a>

                    <form action="{{ route('designations.destroy', $designation->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">
                    No designations found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</div>
@endsection