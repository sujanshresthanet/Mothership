            <ul class="breadcrumb">
            @foreach ($breadcrumbs as $uri => $label)
                @if ( $uri === 'active' )
                <li class="active">{{ $label }}</a></li>
                @else
                <li><a href="{{ URL::to('admin'.$uri) }}">{{ $label }}</a> <span class="divider">/</span></li>
                @endif
            @endforeach
            </ul>