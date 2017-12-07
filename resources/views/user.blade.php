<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $config['siteName'] }}</title>

    <script>
        window.laravel = JSON.parse('{!! json_encode($config) !!}');
    </script>
    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app"></div>

<!-- Scripts -->
<script src="{{ mix("js/user/manifest.js") }}"></script>
<script src="{{ mix("js/user/vendor.js") }}"></script>
<script src="{{ mix("js/user/app.js") }}"></script>
</body>
</html>
