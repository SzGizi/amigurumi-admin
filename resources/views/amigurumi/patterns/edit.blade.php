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
            :initial-title='@json($amigurumiPattern->title)'
            :initial-yarn-description='@json($amigurumiPattern->yarn_description)'
            :initial-tools-description='@json($amigurumiPattern->tools_description)'
            :initial-sections='{{ $sectionsJson }}'
            update-url="{{ route('amigurumi-patterns.update', $amigurumiPattern) }}"
            
        />
    </div>
@endsection
