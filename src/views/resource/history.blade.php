@extends('mothership::layouts.main')

@section('title')
{{ $title}}
@stop


@section('content')
<h1>{{ $title }}</h1>

@include('mothership::common.tabs')

<ul>
@foreach($resource->revisionHistory as $history )
    
    <li><b>{{ ($history->userResponsible() ? $history->userResponsible()->first_name : 'System') }}</b> changed <b>{{ $history->fieldName() }}</b> from <em>"{{ ($history->oldValue() ?: 'Null') }}"</em> to <em>"{{ ($history->newValue() ?: 'Null') }}"</em></li>
@endforeach
</ul>
@stop