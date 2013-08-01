@extends('mothership::theme.layouts.single')

@section('mainColumn')
    
    @section ('header')
    <header>
        <h1>{{ $title }}</h1>
        <nav>
            <a class="pull-right btn" href="{{ mo_index() }}"><i class="icon icon-arrow-left"></i> Back to {{ $plural }}</a>
        </nav>
        @include('mothership::theme.common.tabs')
    </header>
    @show
    <section>
        @section('content')
            {{ $content }}
        @show
    </section>
@stop