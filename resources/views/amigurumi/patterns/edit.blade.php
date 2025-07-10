@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">{{ __('Edit Amigurumi Pattern') }}</h2>

    <form method="POST" action="{{ route('amigurumi-patterns.update', $amigurumiPattern) }}">
        @csrf
        @method('PUT')

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Title') }}</label>
            <input type="text" id="title" name="title" value="{{ old('name', $amigurumiPattern->title) }}"
                   class="form-control @error('title') is-invalid @enderror" required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Yarn -->
        <div class="mb-3">
            <label for="yarn_description" class="form-label">{{ __('Yarn Description') }}</label>
            <input type="text" id="yarn_description" name="yarn_description" value="{{ old('yarn_description', $amigurumiPattern->yarn_description) }}"
                   class="form-control @error('yarn_description') is-invalid @enderror">
            @error('yarn_description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Tools -->
        <div class="mb-3">
            <label for="tools_description" class="form-label">{{ __('Other Tools') }}</label>
            <input type="text" id="tools_description" name="tools_description" value="{{ old('tools_description', $amigurumiPattern->tools_description) }}"
                   class="form-control @error('tools_description') is-invalid @enderror">
            @error('tools_description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit -->
        <div class="text-end">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </div>
    </form>
</div>
@endsection
