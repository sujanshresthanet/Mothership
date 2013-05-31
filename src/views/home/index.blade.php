@extends('mothership::layouts.sidebar')

@section('title')
    Welcome
@stop


@section('content')
<h1>Hi There!</h1>

<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
@stop

@section('sidebar')
<h2>Welcome {{ $user->username }}</h2>
<p>You last logged in <span class="label">{{ $user->lastLogin() }}</span></p>
<ul>
    <li>Item One</li>
    <li>Item Two</li>
    <li>Item Three</li>
</ul>
@stop