<nav class="navbar navbar-default" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">{{ $app_name }}</a>
    </div>
    @if (Sentry::check())
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
        @foreach ($navigation as $uri => $label)
            <li class="{{ (Request::is('admin/'.$uri.'/*') ? 'active' : '') }}">
                <a href="{{ URL::to('admin/'.$uri) }}">{{ $label }}</a>
            </li>
        @endforeach
        </ul>
        <form class="navbar-form navbar-left" role="search">
          <div class="form-group">
            <input disabled type="text" class="form-control" placeholder="Search">
          </div>
          <button disabled type="submit" class="btn btn-default">Submit</button>
        </form>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ Sentry::getUser()->first_name }} <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="{{ URL::to('admin/profile') }}">Update Profile</a></li>
                    <li><a href="{{ URL::to('admin/password') }}">Change Password</a></li>
                    <li class="divider"></li>
                    <li><a href="{{ URL::to('admin/logout') }}">Logout</a></li>
                </ul>
            </li>
        </ul>
    </div><!-- /.navbar-collapse -->
    @endif
</nav>