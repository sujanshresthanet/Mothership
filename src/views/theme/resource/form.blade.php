@extends('mothership::layouts.main')

@section('title')
{{ $title}}
@stop


@section('content')
    <header>
        <h1>{{ $title }}</h1>
        @include('mothership::theme.common.tabs')
    </header>
    <section>
        {{ $form }}
    </section>
@stop