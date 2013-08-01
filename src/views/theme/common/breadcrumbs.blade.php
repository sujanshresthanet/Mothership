            <ul class="breadcrumb">
            @foreach ($breadcrumbs as $uri => $label)
                @if ( $uri === 'active' )
                <li class="active">{{ $label }}</a></li>
                @elseif (strpos($uri, "http://") !== false)
                <li><a href="{{ $uri }}">{{ $label }}</a></li>
                @else
                <li><a href="{{ URL::to('admin/'.$uri) }}">{{ $label }}</a></li>
                @endif
            @endforeach
            </ul>