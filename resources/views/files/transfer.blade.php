@extends('layouts.app')
@section('title', 'Transfer File')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('files.index') }}">Files</a></li>
<li class="breadcrumb-item active">Transfer</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Transfer File</h1>
        <div class="page-subtitle">Route this file to another user or department</div>
    </div>
    <a href="{{ route('files.index') }}" class="btn-portal-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="portal-card">
            <div class="card-header"><i class="fa-solid fa-file-lines me-2 text-primary"></i>File Details</div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted fs-sm mb-1">File Name</div>
                    <div class="fw-700">{{ $file->file_name }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted fs-sm mb-1">File Number</div>
                    <div class="fw-700 text-portal-primary">{{ $file->file_number }}</div>
                </div>
                <div class="mb-3">
                    <div class="text-muted fs-sm mb-1">Current Department</div>
                    <div>{{ $file->department->name ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-muted fs-sm mb-1">Status</div>
                    @include('partials.status-badge', ['status' => $file->status])
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="portal-form-card" style="max-width:100%">
            <h5 class="fw-700 mb-4"><i class="fa-solid fa-right-left me-2 text-primary"></i>Transfer Details</h5>
            <form action="{{ route('files.transfer.store') }}" method="POST" class="portal-form">
                @csrf
                <input type="hidden" name="file_record_uuid" value="{{ $file->uuid }}">

                <div class="mb-3">
                    <label class="form-label">Transfer To <span class="required-star">*</span></label>
                    <select name="to_user_id" class="form-select @error('to_user_id') is-invalid @enderror" required>
                        <option value="">Select recipient user</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('to_user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} &mdash; {{ $u->department->name ?? 'No Dept' }}
                            @if($u->designation) ({{ $u->designation->name }}) @endif
                        </option>
                        @endforeach
                    </select>
                    @error('to_user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">Transferring to a different department will create a transfer request pending admin approval.</div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Remarks</label>
                    <textarea name="remarks" class="form-control" rows="3"
                        placeholder="Add any notes about this transfer">{{ old('remarks') }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn-portal-primary"
                        onclick="return confirm('Confirm file transfer?')">
                        <i class="fa-solid fa-paper-plane"></i> Submit Transfer
                    </button>
                    <a href="{{ route('files.index') }}" class="btn-portal-outline">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
