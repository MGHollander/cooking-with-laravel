@extends('inertia.layout')

@section('head')
    @routes
    @vite('resources/js/Inertia/app.js')
    @inertiaHead
@endsection

@section('content')
    @inertia
@endsection
