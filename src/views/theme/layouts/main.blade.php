@include('mothership::theme.common.head')
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
@include('mothership::theme.common.foot')