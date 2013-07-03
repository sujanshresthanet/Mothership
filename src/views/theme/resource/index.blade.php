@extends('mothership::theme.layouts.main')

@section('content')
    <header>
        <h1>{{ $title }}</h1>
        <nav>
            <a class="pull-right btn btn-success" href="{{ mo_create() }}"><i class="icon-white icon-plus"></i> {{ $singular }}</a>
        </nav>
    </header>
    <section>
        <form action="" method="POST">
            <table class="table table-bordered table-striped table-hover">
                <caption>{{ $caption }}</caption>
                <thead>
                    <th><input id="row-all" name="ids-all" type="checkbox" /></th>
                    @foreach ($columns as $k => $v)
                        @if (is_callable($v))
                        <th>{{ $k }}</th>
                        @elseif(is_object($v))
                        <th>{{ $v->label }}</th>
                        @endif
                    @endforeach
                    <th></th>
                </thead>
                <tbody>
                    @foreach ($collection as $r)
                    <tr>
                        <td><input id="row-{{ $r->id }}" name="ids[]" type="checkbox" value="{{ $r->id }}"/></td>
                    @foreach ($columns as $k => $v)
                        @if (is_object($v) && !($v instanceof Closure))
                        <td><label for="row-{{ $r->id }}">{{ $v->getTable($r) }}</label></td>
                        @else
                        <td><label for="row-{{ $r->id }}">{{ call_user_func($v, $r) }}</label></td>
                        @endif
                    @endforeach
                        <td><a class="btn" href="{{ mo_edit($r->id) }}">Edit</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="form-actions">
                <button name="delete" value="delete" type="submit" class="pull-right btn btn-danger">Delete Selected</button>
            </div>
        </form>
    </section>
    <footer class="pagination pagination-centered">
        {{ $collection->links() }}
    </footer>
@stop