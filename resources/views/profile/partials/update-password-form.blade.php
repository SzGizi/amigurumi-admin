<section class="container my-4">
    <h2 class="h5 mb-3">Update Password</h2>
    <p class="text-muted mb-4">
        {{ __('Ensure your account is using a long, random password to stay secure.') }}
    </p>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        {{-- Current password --}}
        <div class="basic-input mb-3">
            <label for="update_password_current_password" class="form-label">Current Password</label>
            <input
                type="password"
                id="update_password_current_password"
                name="current_password"
                autocomplete="current-password"
                class="form-control"
            >
            @if($errors->updatePassword->has('current_password'))
                <div class="text-danger mt-1">
                    {{ $errors->updatePassword->first('current_password') }}
                </div>
            @endif
        </div>

        {{-- New password --}}
        <div class="basic-input mb-3">
            <label for="update_password_password" class="form-label">New Password</label>
            <input
                type="password"
                id="update_password_password"
                name="password"
                autocomplete="new-password"
                class="form-control"
            >
            @if($errors->updatePassword->has('password'))
                <div class="text-danger mt-1">
                    {{ $errors->updatePassword->first('password') }}
                </div>
            @endif
        </div>

        {{-- Confirm new password --}}
        <div class="basic-input mb-4">
            <label for="update_password_password_confirmation" class="form-label">Confirm Password</label>
            <input
                type="password"
                id="update_password_password_confirmation"
                name="password_confirmation"
                autocomplete="new-password"
                class="form-control"
            >
            @if($errors->updatePassword->has('password_confirmation'))
                <div class="text-danger mt-1">
                    {{ $errors->updatePassword->first('password_confirmation') }}
                </div>
            @endif
        </div>

        {{-- Save button & status --}}
        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Save</button>

            @if (session('status') === 'password-updated')
                <span class="text-success small">Saved.</span>
            @endif
        </div>
    </form>
</section>
