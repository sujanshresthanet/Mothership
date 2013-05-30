@extends('mothership::layouts.main')

@section('title')
{{ $title}}
@stop

@section('content')
<h1>{{ $title }}</h1>

@include('mothership::common.tabs')

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Field</th>
            <th>Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($fields as $name => $field)
        <tr>
            <th>{{ $field->label }}</th>
            <th>{{ $resource->{$name} }}</th>
        </tr>
        @endforeach
    </tbody>
</table>
@stop
