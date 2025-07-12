@extends('layouts.app')

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
