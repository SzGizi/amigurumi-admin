@extends('layouts.app')

@section('page-title')
    {{__('Edit Amigurumi Pattern')}} :
    <br>
    <small>{{ $amigurumiPattern->title }}</small>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('amigurumi-patterns.index') }}">{{__('Amigurumi patterns')}}</a></li>
    <li class="breadcrumb-item active"> {{ $amigurumiPattern->title }}</li>
@endsection

@section('content')


    <div id="app">
      
       
        <amigurumi-pattern-edit
            :initial-pattern-id='@json($amigurumiPattern->id)'
            :initial-main-image-id='@json($amigurumiPattern->main_image_id)'
            :initial-title='@json($amigurumiPattern->title)'
            :initial-yarn-description='@json($amigurumiPattern->yarn_description)'
            :initial-tools-description='@json($amigurumiPattern->tools_description)'
            :initial-final-size='@json($amigurumiPattern->final_size)'
            :initial-difficulty='@json($amigurumiPattern->difficulty)'
            :initial-sections='{{ $sectionsJson }}'
            update-url="{{ route('amigurumi-patterns.update', $amigurumiPattern) }}"
            
        />
    </div>
@endsection
