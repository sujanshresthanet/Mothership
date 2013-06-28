@extends('mothership::theme.layouts.main')

@section('content')
    <header>
        <h1>{{ $title }}</h1>
        <nav>
            <a class="pull-right btn btn-success" href="{{ mo_create() }}"><i class="icon-white icon-plus"></i> {{ $singular }}</a>
        </nav>
    </header>
    <section>
        <ol>
        @foreach ($collection as $r)
            @if ($r->isRoot())
            <li>
                {{ $r }}
                <ol>
                @foreach ($r->children() as $c)
                    <li>{{ $c }}</li>
                @endforeach
                </ol>
            </li>
            @endif
        @endforeach
        </ol>
    </section>
@stop