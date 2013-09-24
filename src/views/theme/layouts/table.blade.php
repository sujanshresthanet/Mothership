@extends('mothership::theme.layouts.single')

@section('mainColumn')
    <header class="clearfix">
    @section('mainHeader')
        <h1>{{ $title }}</h1>
        <p class="clearfix">
            <a class="pull-right btn btn-success" href="{{ mo_create() }}"><i class="glyphicon glyphicon-plus"></i> {{ $singular }}</a>
        </p>
    @show
    </header>
    <section>
        <form action="" method="POST">

        @section('table')
            <table class="table table-hover table-bordered">
                <thead>
                  <tr>
                    <th><input type="checkbox" /></th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Username</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><input type="checkbox" /></td>
                    <td>Mark</td>
                    <td>Otto</td>
                    <td>@mdo</td>
                  </tr>
                  <tr>
                    <td><input type="checkbox" /></td>
                    <td>Jacob</td>
                    <td>Thornton</td>
                    <td>@fat</td>
                  </tr>
                  <tr>
                    <td><input type="checkbox" /></td>
                    <td>Larry</td>
                    <td>the Bird</td>
                    <td>@twitter</td>
                  </tr>
                </tbody>
            </table>
        @show
        
        @section('actions')
            <div class="form-actions">
                <button name="_method" value="delete" type="submit" class="pull-right btn btn-danger"><span class="glyphicon glyphicon-trash"></span> Delete Selected</button>
            </div>
        @show

        @section('pagination')
            <ul class="pagination">
                <li><a href="#">&laquo;</a></li>
                <li><a href="#">1</a></li>
                <li class="active"><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">4</a></li>
                <li><a href="#">5</a></li>
                <li><a href="#">&raquo;</a></li>
            </ul>
        @show

        </form>
    </section>
@stop