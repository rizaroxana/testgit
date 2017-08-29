<!-- index.blade.php -->
@extends('master')
@section('content')
    <div class="container">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Numar bucati</th>
                <th>Data</th>
            </tr>
            </thead>
            <tbody>
            <?php //dd($categories) ?>
            @foreach($intrari as $intrare)
            <?php //dd($produs) ?>
            <tr>
                <td>{{$intrare->id}}</td>
                <td> {{$intrare->buc}}</td>
                <td>{{$intrare->created_at}}</td>
            </tr>
            @endforeach
            <a href="{{ url('/produs/intrare', $productId) }}" class="btn btn-success"> Adaugare intrare </a>
            </tbody>
        </table>
    </div>
    @endsection