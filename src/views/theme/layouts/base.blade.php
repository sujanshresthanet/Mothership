@include('mothership::theme.common.head')
    
    <header class="app-header" id="app-header">
        @include('mothership::theme.common.navbar')
    </header>
    
    {{ Stwt\Mothership\Messages::getHtml() }}

    @section('body')

    <div class="container" id="container">
        <h1>{{ $title }}</h1>
        <p class="alert alert-info">This area is the <em>body</em> section.</p>
    </div>

    @show

@include('mothership::theme.common.foot')