<ul class="nav nav-tabs">
@foreach($action_tabs as $uri => $action)
    <li class="{{ $action['class'] }}">
        {{ $action['link'] }}
    </li>
@endforeach
</ul>