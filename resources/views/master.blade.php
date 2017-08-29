<!-- master.blade.php -->
<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gestiune</title>

    <!-- Fonts -->
    <link href="{{asset('css/app.css')}}" rel="stylesheet" type="text/css">
</head>
<body>
<br><br>
<div class="flash-message">
    @if(session('message'))
        {{session('message')}}
    @endif
</div> <!-- end .flash-message -->
@yield('content')
</body>
</html>