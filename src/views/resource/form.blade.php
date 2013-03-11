@extends('mothership::layouts.main')

@section('title')
{{ $title}}
@stop


@section('content')
<h1>{{ $title }}</h1>

@include('mothership::common.tabs')

{{ $form->generate() }}
@stop