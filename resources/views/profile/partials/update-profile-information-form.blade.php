<section class="container my-4">
    <h2 class="h5 mb-3">Profile Information</h2>
    <p class="text-muted mb-4">
        {{ __("Update your account's profile information and email address.") }}
    </p>

    {{-- E-mail verification form --}}
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        {{-- Name --}}
        <div class="basic-input mb-3">
            <label for="name" class="form-label">Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $user->name) }}"
                class="form-control"
                required
                autocomplete="name"
            >
            @error('name')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Email --}}
        <div class="basic-input mb-3">
            <label for="email" class="form-label">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $user->email) }}"
                class="form-control"
                required
                autocomplete="username"
            >
            @error('email')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 small text-muted">
                    {{ __('Your email address is unverified.') }}
                    <button form="send-verification" class="btn btn-link btn-sm p-0 align-baseline">
                        {{ __('Click here to re-send the verification email.') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <div class="text-success mt-2">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Creator Name --}}
        <div class="basic-input mb-3">
            <label for="creator_name" class="form-label">Creator Name</label>
            <input
                type="text"
                id="creator_name"
                name="creator_name"
                value="{{ old('creator_name', $user->creator_name) }}"
                class="form-control"
            >
            @error('creator_name')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        {{-- Logo --}}
        <div class="basic-input mb-3">
            <label for="logo" class="form-label">Logo</label>
            <input
                type="file"
                id="logo"
                name="logo"
                class="form-control"
                accept="image/*"
            >
            @error('logo')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror

            @if ($user->logo)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $user->logo) }}" alt="Current Logo" style="max-height: 80px;">
                </div>
            @endif
        </div>

        {{-- Submit button + status --}}
        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">Save</button>

            @if (session('status') === 'profile-updated')
                <span class="text-success small">Saved.</span>
            @endif
        </div>
    </form>
</section>
