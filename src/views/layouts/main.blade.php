<!doctype html>
<html>
    <head>
        <title>@yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/css/bootstrap-combined.min.css" rel="stylesheet">
        <?=Basset::show('mothership.css'); ?>
    </head>
    <body>
        <header class="app-header" id="app-header">
            <div class="navbar navbar-inverse navbar-static-top">
                <div class="navbar-inner">
                    <div class="container">
                        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </a>
                        <a class="brand" href="#">Mothership</a>
                        <div class="nav-collapse collapse">
                             <ul class="nav">
                            @foreach ($navigation as $uri => $label)
                                <li class="{{ (Request::is('admin/'.$uri.'/*') ? 'active' : '') }}">
                                    <a href="{{ URL::to('admin/'.$uri) }}">{{ $label }}</a>
                                </li>
                            @endforeach
                            </ul>
                            <form class="navbar-search pull-right">
                                <input type="text" class="search-query" placeholder="Search">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="container" id="container">
            <ul class="breadcrumb">
            @foreach ($breadcrumbs as $uri => $label)
                @if ( $uri === 'active' )
                <li class="active">{{ $label }}</a></li>
                @else
                <li><a href="{{ URL::to('admin'.$uri) }}">{{ $label }}</a> <span class="divider">/</span></li>
                @endif
            @endforeach
            </ul>
            <div class="app-content" id="app-content">
                <?=Messages::get_html()?>
                @yield('content')
            </div>
        </div>
        <script src="http://code.jquery.com/jquery.js"></script>
        <script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/js/bootstrap.min.js"></script>
    </body>
</html>