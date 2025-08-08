@extends('layouts.app')

@section('page-title')
    {{__('Edit Profile')}} :
    <br>
    <small>{{ $user->name }}</small>
@endsection

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
    <li class="breadcrumb-item active"> {{ $user->name}}</li>
@endsection

@section('content')

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 ">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 ">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 ">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
            <div class="p-4">
                <section class="container my-4">
                    <div id="app">
                        <solcial-link-edit/>
                    </div>   
                </section  >   
            </div>
        </div>
    </div>
@endsection
