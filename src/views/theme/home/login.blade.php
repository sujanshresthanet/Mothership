@extends('mothership::theme.layouts.base')

@section('body')

<div class="container" id="container">   
    {{ Stwt\Mothership\Messages::getHtml() }}
    <div class="row">
        <div class="col-lg-4 col-offset-4">
            <header class="page-header">
                <h1>{{ $title }}</h1>
            </header>
            <form action="{{ URL::to('admin/login') }}" class="form-horizontal" method="post">
              <div class="form-group">
                <label for="email" class="col-lg-3 control-label">Email</label>
                <div class="col-lg-9">
                  <input type="text" class="form-control" id="email" placeholder="Email" name="email" />
                </div>
              </div>
              <div class="form-group">
                <label for="inputPassword" class="col-lg-3 control-label">Password</label>
                <div class="col-lg-9">
                  <input type="password" class="form-control" id="password" placeholder="Password" name="password">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox"> Remember me
                    </label>
                  </div>
                  <button type="submit" class="btn btn-default">Sign in</button>
                </div>
              </div>
            </form>
        </div>
    </div>
</div>

@stop