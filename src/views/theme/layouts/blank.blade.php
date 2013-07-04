@include('mothership::theme.common.head')
    <header class="app-header" id="app-header">
        @include('mothership::theme.common.navbar')
    </header>
    {{ Stwt\Mothership\Messages::getHtml() }}
    @yield('content')
@include('mothership::theme.common.foot')