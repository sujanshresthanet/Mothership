<ul class="nav nav-tabs">
@foreach($action_tabs as $uri => $action)
    <li class="{{ $action['class'] }}">
        {{ $action['link'] }}
    </li>
@endforeach
<!--
    <li class="{{ ($create ? 'disabled' : '') }}">
        <a href="{{ ($create ? '#' : URL::to('admin/'.$controller.'/'.$resource->id)) }}">View</a>
    </li>
    <li class="{{ ($create ? 'disabled' : 'active') }}">
        <a href="{{ ($create ? '#' : URL::to('admin/'.$controller.'/'.$resource->id.'/edit')) }}">Edit</a>
    </li>
    <li class="{{ ($create ? 'disabled' : '') }}">
        <a href="{{ ($create ? '#' : URL::to('admin/'.$controller.'/'.$resource->id.'/delete')) }}">Delete</a>
    </li>
    <li>
        <a href="{{URL::to('admin/'.$controller.'/create')}}">Create</a>
    </li>
-->
</ul>