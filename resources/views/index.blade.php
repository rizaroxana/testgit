<!-- index.blade.php -->
@extends('master')
@section('content')
    <div class="container">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nume produs</th>
                <th>Bucati disponibile</th>
                <th>Status</th>
                <th></th>
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
                <td><a href="{{url('/produs/intrari', $produs->id)}}" class="btn btn-danger" data-method="delete">Intrari</a></td>

            </tr>
            @endforeach
            <a href="{{ url('/produs/create') }}" class="btn btn-success"> Adaugare produs nou </a>
            </tbody>
        </table>
    </div>
    @endsection