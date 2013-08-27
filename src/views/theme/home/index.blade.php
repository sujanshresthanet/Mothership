@extends('mothership::theme.layouts.sidebar')

@section('title')
    Welcome
@stop


@section('content')
<h1>{{ $title }}</h1>

{{ $content }}

@stop

@section('sidebar')
<h2>Welcome {{ Sentry::getUser()->first_name }}</h2>
<p>You last logged in <span class="label">{{ Sentry::getUser()->last_login }}</span></p>
<ul>
    <li><a href="{{ URL::to('admin') }}">Home</a></li>
    <li><a href="{{ URL::to('admin/profile') }}">Update profile</a></li>
    <li><a href="{{ URL::to('admin/password') }}">Change password</a></li>
    <li><a href="{{ URL::to('admin/logout') }}">Logout</a></li>
</ul>
@stop