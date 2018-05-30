<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <meta name="keywords" content=""/>
    <meta name="description" content=""/>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>宏海网络 IP测试</title>

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div class="container">
    @foreach($data as $key=>$item)
        <div class="row">
            {{$key}}-{{$item}}
        </div>
    @endforeach
</div>
<!-- Scripts -->
</body>
</html>
