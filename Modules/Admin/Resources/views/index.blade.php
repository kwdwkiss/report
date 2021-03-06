<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>宏海网络-管理后台</title>

    <script>
        window.laravel = JSON.parse('{!! json_encode($config) !!}');
    </script>
    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app"></div>

<!-- Scripts -->
<script src="{{ mix("js/admin/app.js") }}"></script>
</body>
</html>
