            <div class="navbar navbar-inverse navbar-static-top">
                <div class="navbar-inner">
                    <div class="container">
                        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </a>
                        <a class="brand" href="#">Mothership</a>
                        @if (Auth::check())
                        <div class="nav-collapse collapse">
                             <ul class="nav">
                            @foreach ($navigation as $uri => $label)
                                <li class="{{ (Request::is('admin/'.$uri.'/*') ? 'active' : '') }}">
                                    <a href="{{ URL::to('admin/'.$uri) }}">{{ $label }}</a>
                                </li>
                            @endforeach
                            </ul>
                            <ul class="nav pull-right">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ $user->displayName() }} <b class="caret"></b></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="{{ URL::to('admin/profile') }}">Update Profile</a></li>
                                        <li><a href="{{ URL::to('admin/password') }}">Change Password</a></li>
                                        <li class="divider"></li>
                                        <li><a href="{{ URL::to('admin/logout') }}">Logout</a></li>
                                    </ul>
                                </li>
                            </ul>
                            <form class="navbar-search pull-right">
                                <input type="text" class="search-query" placeholder="Search">
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>