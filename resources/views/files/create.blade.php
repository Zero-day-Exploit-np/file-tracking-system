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
    <a href="{{ route('files.index') }}" class="btn-portal-outline">
        <i class="fa-solid fa-arrow-left"></i> Back
    </a>
</div>

<div class="portal-form-card">
    <form action="{{ route('files.store') }}" method="POST" enctype="multipart/form-data" class="portal-form">
        @csrf

        {{-- Government File Number (manual) --}}
        <div class="mb-3">
            <label class="form-label">
                Government File Number <span class="required-star">*</span>
            </label>
            <input type="text"
                   name="file_number"
                   class="form-control @error('file_number') is-invalid @enderror"
                   value="{{ old('file_number') }}"
                   placeholder="e.g. HR/FIN/2026/234  or  FIN-12/456"
                   required
                   autocomplete="off">
            <div class="form-text text-muted">
                <i class="fa-solid fa-circle-info me-1"></i>
                Enter the official government file number. Must be unique. Allowed: letters, numbers, hyphens, slashes, dots.
            </div>
            @error('file_number')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- File Name --}}
        <div class="mb-3">
            <label class="form-label">File Name / Subject <span class="required-star">*</span></label>
            <input type="text"
                   name="file_name"
                   class="form-control @error('file_name') is-invalid @enderror"
                   value="{{ old('file_name') }}"
                   placeholder="Enter file name or subject"
                   required>
            @error('file_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Department (any dept allowed) --}}
        <div class="mb-3">
            <label class="form-label">Department <span class="required-star">*</span></label>
            <select name="department_id"
                    class="form-select @error('department_id') is-invalid @enderror"
                    required>
                <option value="">— Select Department —</option>
                @foreach($departments as $dept)
                <option value="{{ $dept->id }}"
                    {{ old('department_id', auth()->user()->department_id) == $dept->id ? 'selected' : '' }}>
                    {{ $dept->name }}
                </option>
                @endforeach
            </select>
            <div class="form-text text-muted">
                <i class="fa-solid fa-circle-info me-1"></i>
                Select the department this file belongs to.
            </div>
            @error('department_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Remarks --}}
        <div class="mb-3">
            <label class="form-label">Remarks</label>
            <textarea name="remarks"
                      class="form-control @error('remarks') is-invalid @enderror"
                      rows="3"
                      placeholder="Optional remarks or notes">{{ old('remarks') }}</textarea>
            @error('remarks')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        {{-- Attachment --}}
        <div class="mb-4">
            <label class="form-label">Upload Document</label>
            <input type="file"
                   name="attachment"
                   class="form-control @error('attachment') is-invalid @enderror"
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png">
            <div class="form-text text-muted">Max 10 MB. Allowed: PDF, Word, Excel, PowerPoint, Images.</div>
            @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn-portal-primary">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save File
            </button>
            <a href="{{ route('files.index') }}" class="btn-portal-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
