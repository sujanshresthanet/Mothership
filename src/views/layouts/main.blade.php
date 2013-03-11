<!doctype html>
<html>
    <head>
        <title>@yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/css/bootstrap-combined.min.css" rel="stylesheet">
        <?=Basset\Facades\Basset::show('mothership.css'); ?>
        <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
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
        <?=Basset\Facades\Basset::show('mothership-footer.js'); ?>
    </body>
</html>