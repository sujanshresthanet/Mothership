<!doctype html>
<html>
    <head>
        <title>{{ $title }}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="{{ URL::to('packages/stwt/mothership/styles/css/mothership.css') }}" rel="stylesheet" />
        <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
        <!--[if lt IE 9]>
          <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
    </head>
    <body>
        <header class="app-header" id="app-header">
            @include('mothership::theme.common.navbar')
        </header>
        <div class="container" id="container">
            {{-- @include('mothership::theme.common.breadcrumbs') --}}
            {{ Stwt\Mothership\Messages::getHtml() }}
            <div class="app-content" id="app-content">
                @yield('content')
            </div>
        </div>
        <script src="http://code.jquery.com/jquery.js"></script>
        <script src="{{ URL::to('packages/stwt/mothership/scripts/js/mothership.min.js') }}"></script>
    </body>
</html>