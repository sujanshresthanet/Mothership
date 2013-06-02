@extends('mothership::layouts.main')

@section('title')
{{ $title }}
@stop


@section('content')
<h1>{{ $title }}</h1>
<nav>
    {{ $createButton }}
</nav>
<form action="" method="POST">
    <input type="hidden" name="_method" value="DELETE" />
    <table class="table table-bordered table-striped table-hover">
        <caption>{{ $caption }}</caption>
        <thead>
            <tr>
                <th><input id="row-all" name="ids-all" type="checkbox" /></th>
             @foreach ($columns as $k => $v)
                @if (is_callable($v))
                <th>{{ $k }}</th>
                @elseif(is_object($v))
                <th>{{ $v->label }}</th>
                @endif
            @endforeach
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($paginator as $r)
            <tr>
                <td><input id="row-{{ $r->id }}" name="ids[]" type="checkbox" value="{{ $r->id }}"/></td>
            @foreach ($columns as $k => $v)
                @if (is_object($v) && !($v instanceof Closure))
                    <td><label for="row-{{ $r->id }}">{{ $v->getTable($r) }}</label></td>
                @else
                <td><label for="row-{{ $r->id }}">{{ call_user_func($v, $r) }}</label></td>
                @endif
            @endforeach
                <td>
                    <a class="btn" href="{{ URL::to('admin/'.$controller.'/'.$r->id) }}">View</a>
                    <a class="btn" href="{{ URL::to('admin/'.$controller.'/'.$r->id.'/edit') }}">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="form-actions">
        <button name="delete" value="delete" type="submit" class="pull-right btn btn-danger">Delete Selected</button>
    </div>
</form>
<div class="pagination pagination-centered">
    {{ $paginator->links() }}
</div>
@stop