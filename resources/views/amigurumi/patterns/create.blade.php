@extends('layouts.app')

@section('page-title')
    {{ __('Create New Amigurumi Pattern') }}
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('amigurumi-patterns.index') }}">{{__('Amigurumi patterns')}}</a></li>
    <li class="breadcrumb-item active">{{ __('Create New Amigurumi Pattern') }}</li>
@endsection

@section('content')
<div class="container">
    <div class="mb-4">
        <a href="{{ route('amigurumi-patterns.index') }}" class="btn btn-secondary btn-sm">← {{ __('Back to List') }}</a>
    </div>

    {{-- Hibák megjelenítése --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>{{ __('There were some problems with your input:') }}</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('amigurumi-patterns.store') }}">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">{{ __('Pattern Title') }}</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
        </div>

        <div class="mb-3">
            <label for="yarn_description" class="form-label">{{ __('Yarn Description') }}</label>
            <textarea  rows=3 name="yarn_description" id="yarn_description" class="form-control" rows="3" required>{{ old('yarn_description') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="tools_description" class="form-label">{{ __('Tools Description') }}</label>
            <textarea rows=3 name="tools_description" id="tools_description" class="form-control" rows="2" required>{{ old('tools_description') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">{{ __('Create Pattern') }}</button>
    </form>
</div>
@endsection
