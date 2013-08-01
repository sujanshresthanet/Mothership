@extends('mothership::theme.layouts.base')

@section('body')

    <div class="container" id="container">
        
        {{ Stwt\Mothership\Messages::getHtml() }}
        
        @section('content')
            <h1>{{ $title }}</h1>
            <p class="alert alert-info">This area is the <em>content</em> section.</p>
        @show

    </div>

@stop