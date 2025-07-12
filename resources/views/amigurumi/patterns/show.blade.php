@extends('layouts.app')

@section('content')
<div class="container mt-5">
    

    <div class="card mb-4">
        <div class="card-body">
            <h2 class="mb-4">{{ $amigurumiPattern->title }}</h2>
            <p><strong>{{ __('Yarn') }}:</strong> {{ $amigurumiPattern->yarn_description }}</p>
            <p><strong>{{ __('Tools') }}:</strong> {{ $amigurumiPattern->tools_description }}</p>
        </div>
    </div>

    <!-- Sections and Rows -->
    @foreach ($amigurumiPattern->amigurumiSections as $section)
        <div class="mb-4">
            <h5 class="text-primary">{{ $section->title }}</h5>
            <ul class="list-group">
                @foreach ($section->amigurumiRows as $row)
                    <li class="list-group-item">
                        <strong>{{ __('Row') }} {{ $row->row_number }}:</strong> {{ $row->instructions }}
                        @if(isset($row->stitch_number))
                            | {{ $row->stitch_number }}
                        @endif
                        @if(isset($row->comment))
                            | {{ $row->comment }}
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach

    <a href="{{ route('amigurumi-patterns.edit', $amigurumiPattern) }}" class="btn btn-outline-secondary mt-3">
        {{ __('Edit Pattern') }}
    </a>
</div>
@endsection
