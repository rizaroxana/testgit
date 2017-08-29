<!-- create.blade.php -->

@extends('master')
@section('content')
    <div class="container">
        <form method="post" action="{{url('produs/intrare')}}">
            <div class="form-group row">
                {{csrf_field()}}
                <input name="productId" type="hidden" value="{{$productId}}">
                <label for="lgFormGroupInput" class="col-sm-2 col-form-label col-form-label-lg">Nr bucati</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control form-control-lg" id="lgFormGroupInput" placeholder="bucati" name="buc">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-2"></div>
                <input type="submit" class="btn btn-primary">
            </div>
        </form>
    </div>
@endsection