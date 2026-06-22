@extends('layouts.app')
@section('title', 'Create File')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('files.index') }}">Files</a></li>
<li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Create New File</h1>
        <div class="page-subtitle">Register a new official document in the system</div>
    </div>
    <a href="{{ route('files.index') }}" class="btn-portal-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>

<div class="portal-form-card">
    <form action="{{ route('files.store') }}" method="POST" class="portal-form">
        @csrf

        <div class="mb-3">
            <label class="form-label">Department <span class="required-star">*</span></label>
            @if(auth()->user()->role === 'super_admin')
            <select name="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                <option value="">Select Department</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                @endforeach
            </select>
            @else
            <input type="text" class="form-control" value="{{ auth()->user()->department->name ?? 'N/A' }}" readonly>
            <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}">
            @endif
            @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">File Name <span class="required-star">*</span></label>
            <input type="text" name="file_name" class="form-control @error('file_name') is-invalid @enderror"
                value="{{ old('file_name') }}" placeholder="Enter file name or subject" required>
            @error('file_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Remarks</label>
            <textarea name="remarks" class="form-control @error('remarks') is-invalid @enderror"
                rows="3" placeholder="Optional remarks or notes">{{ old('remarks') }}</textarea>
            @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-portal-primary"><i class="fa-solid fa-floppy-disk"></i> Save File</button>
            <a href="{{ route('files.index') }}" class="btn-portal-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
