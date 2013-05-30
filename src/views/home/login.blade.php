@extends('mothership::layouts.blank')

@section('title')
    Please Login
@stop


@section('content')
<form action="{{ URL::to('admin/login') }}" class="form-signin" method="POST" style="margin-top:30px;">
    <h2 class="form-signin-heading">Please sign in</h2>
    <input class="input-block-level" name="email" placeholder="Email address" type="text" />
    <input class="input-block-level" name="password" placeholder="Password" type="password" />
    <label class="checkbox">
        <input type="checkbox" value="remember-me"> Remember me
    </label>
    <button class="btn btn-large btn-primary" type="submit">Sign in</button>
</form>
@stop