@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Amigurumi Patterns</h2>
        <a href="{{ route('amigurumi-patterns.create') }}" class="btn btn-primary">+ New Pattern</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
    @forelse ($patterns as $pattern)
    <div class="col-lg-4 col-12">
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h4 class="card-title">{{ $pattern->title }}</h4>
                <p class="mb-1"><strong>Yarn:</strong> {{ $pattern->yarn_description }}</p>
                <p class="mb-3"><strong>Tools:</strong> {{ $pattern->tools_description }}</p>

                <a href="{{ route('amigurumi-patterns.show', $pattern->id) }}" class="btn btn-sm btn-outline-info me-2">{{ __('View') }}</a>
                <a href="{{ route('amigurumi-patterns.edit', $pattern->id) }}" class="btn btn-sm btn-outline-warning me-2">{{ __('Edit') }}</a>
                <form action="{{ route('amigurumi-patterns.destroy', $pattern->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('{{ __('Are you sure you want to delete this pattern?') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                </form>
               
            </div>
        </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-secondary ">No patterns found. Click "New Pattern" to create one.</div>
        </div>
    @endforelse
    </div>
@endsection
