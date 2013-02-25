@extends('mothership::layouts.main')

@section('title')
{{ $title}}
@stop

@section('content')
<h1>{{ $title }}</h1>

@include('mothership::common.tabs')

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Label</th>
            <th>Name</th>
            <th>Column</th>
            <th>Value</th>
            <th>Type</th>
            <th>Form</th>
            <th>Validation</th>
            <!--<th>Dump</th>-->
        </tr>
    </thead>
    <tbody>
<?
        foreach ($fields as $field => $spec):
?>
        <tr>
            <th>{{ $spec->label }}</th>
            <td>{{ $field }}</td>
            <td>{{ $spec->dataType() }}</td>
            <td>{{ $resource->$field }}</td>
            <td>{{ $spec->type }}</td>
            <td>{{ $spec->type }}</td>
            <td>{{ implode('|', $spec->validation) }}</td>
            <!--<td><pre>{{var_dump($spec)}}</pre></td>-->
        </tr>
<?
        endforeach;
?>
    </tbody>
</table>
@stop
