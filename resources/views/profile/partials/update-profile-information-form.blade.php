<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" class="portal-form">
    @csrf
    @method('patch')

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="name">Full Name <span class="required-star">*</span></label>
            <input id="name" name="name" type="text"
                class="form-control @error('name') is-invalid @enderror"
                value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6">
            <label class="form-label" for="email">Email Address <span class="required-star">*</span></label>
            <input id="email" name="email" type="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2">
                <p class="text-muted small">
                    Your email address is unverified.
                    <button form="send-verification" class="btn btn-link btn-sm p-0">
                        Click here to re-send the verification email.
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                <p class="text-success small mt-1">A new verification link has been sent to your email address.</p>
                @endif
            </div>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <button type="submit" class="btn-portal-primary">
            <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
        </button>
        @if (session('status') === 'profile-updated')
        <span class="text-success ms-3 small"><i class="fa-solid fa-check me-1"></i>Saved successfully.</span>
        @endif
    </div>
</form>
