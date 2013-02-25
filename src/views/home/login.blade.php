@extends('mothership::layouts.blank')

@section('title')
    Login
@stop


@section('content')
<form class="form-signin" style="margin-top:30px;">
    <h2 class="form-signin-heading">Please sign in</h2>
    <input class="input-block-level" placeholder="Email address" type="text" />
    <input class="input-block-level" placeholder="Password" type="password" />
    <label class="checkbox">
        <input type="checkbox" value="remember-me"> Remember me
    </label>
    <button class="btn btn-large btn-primary" type="submit">Sign in</button>
</form>
@stop