@extends('mothership::theme.resource.table')

@section('table')
    <div class="row">
        <div class="col-lg-10">
            <table class="table table-bordered table-hover">
                <caption>Page content regions</caption>
                <thead>
                    <tr>
                        <th><input disabled id="row-all" name="ids-all" type="checkbox" /></th>
                        <th>Region</th>
                        <th>Type</th>
                        <th>Content</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($collection as $name => $region)
                    @if (!is_object($region))
                    <tr class="danger">
                        <th><input disabled type="checkbox" /></th>
                        <td>
                            <a 
                                href="{{ mo_create().'?'.http_build_query(['defaults' => ['key' => $name]]) }}" 
                                data-toggle="tooltip" 
                                title="Add a new region for {{ $name }}"
                            >{{ $name }}</a>
                        </td>
                        <td>---</td>
                        <td>This region is currently missing and may break the page layout.</td>
                    </tr>
                    @else
                    <tr class="{{ ($region->isShared() ? 'success' : '') }}">
                        <th><input disabled type="checkbox" /></th>
                        <td>
                            <a 
                                href="{{ mo_edit($region->id) }}"
                                data-toggle="tooltip"
                                title="Edit the {{ $name }} region"
                            >{{ ucwords(humanize($region->key)) }}</a>
                        </td>
                        <td>{{ $region->type() }}</td>
                        <td>{{ $region->excerpt() }}</td>
                    </tr>
                    @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-lg-2">
            <table class="table table-bordered table-hover">
                <caption>Toggle Visiblity</caption>
                <thead>
                    <tr>
                        <td><input id="toggle-all" type="checkbox" /></td>
                        <th>Key</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input id="toggle-page-regions" type="checkbox" /></td>
                        <td><label for="toggle-page-regions">Page Content</label></td>
                    </tr>
                    <tr class="success">
                        <td><input id="toggle-shared-regions" type="checkbox" /></td>
                        <td><label for="toggle-shared-regions">Shared Content</label></td>
                    </tr>
                    <tr class="warning">
                        <td><input id="toggle-surplus-regions" type="checkbox" /></td>
                        <td><label for="toggle-surplus-regions">Content not used</label></td>
                    </tr>
                    <tr class="danger">
                        <td><input id="toggle-missing-regions" type="checkbox" /></td>
                        <td><label for="toggle-missing-regions">Missing Content</label></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('pagination')
@stop