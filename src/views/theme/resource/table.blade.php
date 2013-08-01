@extends('mothership::theme.layouts.table');

@section('table')

    <table class="table table-bordered table-striped table-hover">
        <caption>{{ $caption }}</caption>
        <thead>
            @if($selectable)
            <th><input id="row-all" name="ids-all" type="checkbox" /></th>
            @endif
            @foreach ($columns as $k => $v)
                @if (is_callable($v))
                <th>{{ $k }}</th>
                @elseif(is_object($v))
                <th>{{ $v->label }}</th>
                @endif
            @endforeach
        </thead>
        <tbody>
    @foreach ($collection as $r)
        @if (is_subclass_of($r, 'Stwt\Mothership\BaseModel'))
            <tr>
            @if($selectable)
                <td><input id="row-{{ $r->id }}" name="ids[]" type="checkbox" value="{{ $r->id }}"/></td>
            @endif
            @foreach ($columns as $k => $v)
                <td>
                @if ($primaryColumn == $k)
                    <a href="{{ mo_edit($r->id) }}">
                @else
                    <label for="row-{{ $r->id }}">
                @endif

                    @if (is_object($v) && !($v instanceof Closure))
                        {{ $v->getTable($r) }}
                    @else
                        {{ call_user_func($v, $r) }}
                    @endif

                @if ($primaryColumn == $k)
                    </a>
                @else
                    </label>
                @endif
                </td>
            @endforeach
            </tr>
        @endif
    @endforeach
        </tbody>
    </table>
@stop

@section('pagination')
    
    <footer>
        {{ $pagination }}
    </footer>

@stop