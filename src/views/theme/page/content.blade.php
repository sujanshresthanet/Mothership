<form action="" method="POST">
    <input type="hidden" name="_method" value="DELETE" />
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th><input id="row-all" name="ids-all" type="checkbox" /></th>
                <th>Key</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($regions as $region)
            <tr>
                <td><input name="ids" type="checkbox" /></td>
                <td>{{ $region->key }}</td>
                <td>{{ $region->type() }}</td>
                <td>
                    <a class="btn" href="{{ URL::to('admin/') }}">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</form>