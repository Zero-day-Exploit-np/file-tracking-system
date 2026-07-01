@extends('layouts.app')
@section('title', 'Create File')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('files.index') }}">My Files</a></li>
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
            <label class="form-label">Department</label>
            {{-- Users always use their own department -- no selection allowed --}}
            <input type="text" class="form-control bg-light" value="{{ auth()->user()->department->name ?? 'N/A' }}" readonly>
            <div class="form-text text-muted">
                <i class="fa-solid fa-lock me-1"></i>Files are registered to your assigned department.
            </div>
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
