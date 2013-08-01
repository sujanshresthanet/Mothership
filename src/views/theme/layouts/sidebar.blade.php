@extends('mothership::theme.layouts.base')

@section('body')

    <div class="container" id="container">

        {{--@include('mothership::common.breadcrumbs')--}}
        
        {{ Stwt\Mothership\Messages::getHtml() }}
        
        <div class="row">

            <div class="col-lg-8">
                @section('content')
                    <h1>{{ $title }}</h1>
                    <p class="alert alert-info">This area is the <em>content</em> section.</p>
                @show
            </div>
            <div class="col-lg-4">
                @section('sidebar')
                    <p class="alert alert-info">This area is the <em>sidebar</em> section.</p>
                @show
            </div>
        </div>

    </div>

@stop