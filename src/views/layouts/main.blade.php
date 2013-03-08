<!doctype html>
<html>
    <head>
        <title>@yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/css/bootstrap-combined.min.css" rel="stylesheet">
        <?=Basset\Facades\Basset::show('mothership.css'); ?>
    </head>
    <body>
        <header class="app-header" id="app-header">
            @include('mothership::common.navbar')
        </header>
        <div class="container" id="container">
            @include('mothership::common.breadcrumbs')
            <?=Messages::get_html()?>
            <div class="app-content" id="app-content">
                @yield('content')
            </div>
        </div>
        <script src="http://code.jquery.com/jquery.js"></script>
        <script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/js/bootstrap.min.js"></script>
    </body>
</html>