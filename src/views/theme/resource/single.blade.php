@extends('mothership::theme.layouts.main')

@section('title')
{{ $title}}
@stop

@section('content')
    <header>
        <h1>{{ $title }}</h1>
        <nav>
            <a class="pull-right btn" href="{{ mo_index() }}"><i class="icon icon-arrow-left"></i> Back to {{ $plural }}</a>
        </nav>
        @include('mothership::theme.common.tabs')
    </header>
    <section>
        {{ $content }}
    </section>
@stop