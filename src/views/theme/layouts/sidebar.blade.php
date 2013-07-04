@include('mothership::theme.common.head')
<body>
    <header class="app-header" id="app-header">
        @include('mothership::theme.common.navbar')
    </header>
    <div class="container" id="container">
        {{--@include('mothership::common.breadcrumbs')--}}
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
@include('mothership::theme.common.foot')
        