@extends('layouts.app')

@section('page-title')
    {{ $amigurumiPattern->title }}
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('amigurumi-patterns.index') }}">{{__('Amigurumi patterns')}}</a></li>
    <li class="breadcrumb-item active">{{ $amigurumiPattern->title }}</li>
@endsection

@section('content')
<div class="container mt-5">
    
    {{-- Fő kép (ha van) --}}
    @if($amigurumiPattern->mainImage)
        <div class="mb-4 text-center">
            <img src="{{asset('storage/' . $amigurumiPattern->mainImage->path) }}" class="img-fluid rounded shadow" alt="Main Image">
            @if($amigurumiPattern->mainImage->caption)
                <p class="mt-2 text-muted fst-italic">{{ $amigurumiPattern->mainImage->caption }}</p>
            @endif
        </div>
    @endif

    {{-- Alapadatok --}}
    <div class="card mb-4">
        <div class="card-body">
            <p><strong>{{ __('Yarn') }}:</strong> {{ $amigurumiPattern->yarn_description }}</p>
            <p><strong>{{ __('Tools') }}:</strong> {{ $amigurumiPattern->tools_description }}</p>
        </div>
    </div>

    {{-- További képek (fő kép kivételével) --}}
    @php
        $otherImages = $amigurumiPattern->images->filter(fn($img) => !$img->is_main);
    @endphp

    @if($otherImages->count())
        <div class="mb-5">
            <h5 class="text-secondary">{{ __('Additional Images') }}</h5>
            <div class="row">
                @foreach($otherImages as $image)
                    <div class=" col-3 mb-3">
                        <div class="card shadow-sm h-100">
                            <img src="{{ asset('storage/' .$image->path) }}" class="card-img-top" alt="Pattern Image">
                            @if($image->caption)
                                <div class="card-body p-2">
                                    <p class="small text-muted text-center">{{ $image->caption }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Szekciók és sorok --}}
    @foreach ($amigurumiPattern->amigurumiSections as $section)
        <div class="mb-4">
            <h5 class="text-primary">{{ $section->title }}</h5>

            {{-- Szekció képek --}}
            @if($section->images->count())
                <div class="row mb-3">
                    @foreach($section->images as $image)
                        <div class=" col-6 mb-2">
                            <div class="card shadow-sm h-100">
                                <img src="{{asset('storage/' . $image->path) }}" class="card-img-top" alt="Section Image">
                                @if($image->caption)
                                    <div class="card-body p-2">
                                        <p class="small text-muted text-center">{{ $image->caption }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Sorok listája --}}
            <ul class="list-group">
                @foreach ($section->amigurumiRows as $row)
                    <li class="list-group-item">
                        <strong>{{ __('Row') }} {{ $row->row_number }}:</strong> {{ $row->instructions }}
                        @if(isset($row->stitch_number))
                            ({{ $row->stitch_number }})
                        @endif
                        @if(isset($row->comment))
                            | <span class="text-muted fst-italic">{{ $row->comment }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach

    {{-- Szerkesztés gomb --}}
    <a href="{{ route('amigurumi-patterns.edit', $amigurumiPattern) }}" class="btn btn-outline-secondary mt-3">
        {{ __('Edit Pattern') }}
    </a>
</div>
@endsection
