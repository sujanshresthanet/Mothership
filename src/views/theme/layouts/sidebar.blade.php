<!doctype html>
<html>
    <head>
        <title>@yield('title')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="{{ URL::to('packages/stwt/mothership/styles/css/mothership.css') }}" rel="stylesheet" />
    </head>
    <body>
        <header class="app-header" id="app-header">
            @include('mothership::common.navbar')
        </header>
        <div class="container" id="container">
            @include('mothership::common.breadcrumbs')
            {{ Stwt\Mothership\Messages::getHtml() }}
            <div class="app-content row" id="app-content">
                <section class="span8">
                    @yield('content')
                </section>
                <aside class="span4">
                    @yield('sidebar')
                </aside>
            </div>
        </div>
        <script src="http://code.jquery.com/jquery.js"></script>
        <script src="{{ URL::to('packages/stwt/mothership/scripts/js/mothership.min.js') }}"></script>
    </body>
</html>