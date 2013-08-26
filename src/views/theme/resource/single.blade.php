@extends('mothership::theme.layouts.single')

@section('mainColumn')
    
    @section ('header')
    <header class="main-header">
        <h1>{{ $title }}</h1>
        <nav>
            <a class="pull-right btn btn-default" href="{{ mo_index() }}"><span class="glyphicon glyphicon-arrow-left
"></span> Back to {{ $plural }}</a>
        </nav>
        @include('mothership::theme.common.tabs')
    </header>
    @show
    <section class="main-content">
        @section('content')
            {{ $content }}
        @show
    </section>
@stop