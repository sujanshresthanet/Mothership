@extends('mothership::theme.layouts.sidebar')

@section('title')
    Welcome
@stop


@section('content')
<h1>{{ $title }}</h1>

{{ $content }}

@stop

@section('sidebar')
<h2>Welcome {{ $user->username }}</h2>
<p>You last logged in <span class="label">{{ $user->lastLogin() }}</span></p>
<ul>
    <li><a href="{{ URL::to('admin') }}">Home</a></li>
    @if (Auth::user()->canUpdateProfile())
    <li><a href="{{ URL::to('admin/profile') }}">Update profile</a></li>
    <li><a href="{{ URL::to('admin/password') }}">Change password</a></li>
    @endif
    <li><a href="{{ URL::to('admin/logout') }}">Logout</a></li>
</ul>
@stop