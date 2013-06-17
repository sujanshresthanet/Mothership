<ul class="nav nav-tabs">
@foreach($tabs as $uri => $action)
    <li class="{{ $action['class'] }}">
        {{ $action['link'] }}
    </li>
@endforeach
</ul>