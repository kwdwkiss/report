<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>宏海网络</title>

    <script>
        window.laravel = JSON.parse('{!! json_encode($page) !!}');
    </script>
    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            margin: 0 auto;
            width: 1000px;
            min-height: 400px;
            background-color: #fff;
        }
    </style>
</head>
<body>
<div id="app"></div>

<!-- Scripts -->
<script src="{{ mix("js/index/app.js") }}"></script>
</body>
</html>
