@extends('layouts.app')
@section('title', 'Profile')

@section('breadcrumb')
<li class="breadcrumb-item active">Profile</li>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">My Profile</h1>
        <div class="page-subtitle">Manage your account settings</div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="portal-card mb-3">
            <div class="card-header"><i class="fa-solid fa-user-pen me-2 text-primary"></i>Profile Information</div>
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>
        <div class="portal-card mb-3">
            <div class="card-header"><i class="fa-solid fa-lock me-2 text-primary"></i>Update Password</div>
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>
        <div class="portal-card">
            <div class="card-header"><i class="fa-solid fa-triangle-exclamation me-2 text-danger"></i>Delete Account</div>
            <div class="card-body">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="portal-card">
            <div class="card-header"><i class="fa-solid fa-circle-info me-2 text-primary"></i>Account Details</div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div style="width:72px;height:72px;border-radius:50%;background:#dbeafe;color:#2563eb;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.8rem;margin:0 auto;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="fw-700 mt-2">{{ auth()->user()->name }}</div>
                    <div class="text-muted fs-sm">{{ auth()->user()->email }}</div>
                </div>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fs-sm">Role</span>
                        <span class="badge-status badge-role-{{ auth()->user()->role }}">{{ ucfirst(str_replace('_',' ', auth()->user()->role)) }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fs-sm">Department</span>
                        <span class="fw-700 fs-sm">{{ auth()->user()->department->name ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fs-sm">Designation</span>
                        <span class="fw-700 fs-sm">{{ auth()->user()->designation->name ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
