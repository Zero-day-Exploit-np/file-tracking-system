@extends('layouts.app')
@section('title', 'Edit Designation')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.designations.index') }}">Designations</a></li>
<li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Designation</h1>
    <a href="{{ route('admin.designations.index') }}" class="btn-portal-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>
<div class="portal-form-card">
    <form method="POST" action="{{ route('admin.designations.update', $designation->id) }}" class="portal-form">
        @csrf @method('PUT')
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Designation Name <span class="required-star">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $designation->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="1" {{ $designation->is_active ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$designation->is_active ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn-portal-primary"><i class="fa-solid fa-floppy-disk"></i> Update</button>
            <a href="{{ route('admin.designations.index') }}" class="btn-portal-outline">Cancel</a>
        </div>
    </form>
</div>
@endsection
