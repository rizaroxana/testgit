<!-- index.blade.php -->
@extends('master')
@section('content')
    <div class="container">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Category slug</th>
                <th>Type</th>
                <th>Parent ID</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php //dd($categories) ?>
            @foreach($produse as $produs)
            <?php //dd($produs) ?>
            <tr>
                <td>{{$produs->id}}</td>
                <td> <a href="{{ url('/category/'.$produs->id) }}">{{$produs->nume}}</a></td>
                <td>{{$produs->buc}}</td>
                <td>{{$produs->status}}</td>
                <td><a href="{{url('/category/delete', $produs->id)}}" class="btn btn-danger" data-method="delete">Delete</a></td>
            </tr>
            @endforeach
            <a href="{{ url('/category/create') }}" class="btn btn-success"> Adaugare produs no </a>
            </tbody>
        </table>
    </div>
    @endsection