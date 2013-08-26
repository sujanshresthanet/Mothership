@extends('mothership::theme.layouts.base')

@section('title')
    Please Login
@stop


@section('body')
  <main class="sign-in" id="sign-in">
      <form action="{{ URL::to('admin/login') }}" class="form-signin" method="POST" style="margin-top:30px;" role="form">
          <legend>Please Sign In</legend>
          <div class="form-group">
              <label for="inputEmail1" class="col-lg-2 control-label">Email</label>
              <div class="col-lg-10">
                  <input type="email" name="email" class="form-control" id="inputEmail1" placeholder="Email">
              </div>
          </div>
          <div class="form-group">
              <label for="inputPassword1" class="col-lg-2 control-label">Password</label>
              <div class="col-lg-10">
                  <input type="password" name="password" class="form-control" id="inputPassword1" placeholder="Password">
              </div>
          </div>
          <div class="form-group">
              <div class="col-lg-offset-2 col-lg-10">
                <div class="checkbox">
                      <label>
                          <input type="checkbox"> Remember me
                      </label>
                  </div>
              </div>
          </div>
          <div class="form-group">
              <div class="col-lg-offset-2 col-lg-10">
                  <button type="submit" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-ok"></span> Sign in</button>
              </div>
          </div>
      </form>
  </main>
@stop