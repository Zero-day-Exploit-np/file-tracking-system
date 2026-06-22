<form method="post" action="{{ route('password.update') }}" class="portal-form">
    @csrf
    @method('put')

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="update_password_current_password">Current Password <span class="required-star">*</span></label>
            <input id="update_password_current_password" name="current_password" type="password"
                class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                autocomplete="current-password">
            @error('current_password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="update_password_password">New Password <span class="required-star">*</span></label>
            <input id="update_password_password" name="password" type="password"
                class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                autocomplete="new-password">
            @error('password', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="update_password_password_confirmation">Confirm New Password <span class="required-star">*</span></label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn-portal-primary">
            <i class="fa-solid fa-lock me-1"></i> Update Password
        </button>
        @if (session('status') === 'password-updated')
        <span class="text-success ms-3 small"><i class="fa-solid fa-check me-1"></i>Password updated.</span>
        @endif
    </div>
</form>
