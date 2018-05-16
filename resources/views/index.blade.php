<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>宏海网络</title>

    <script>
        window.laravel = {!! json_encode($page) !!};
    </script>
    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
<div id="app"></div>
<!-- Scripts -->
<script type="text/javascript" src="{{ mix("js/index/app.js") }}"></script>
<script src="https://s22.cnzz.com/z_stat.php?id=1271314784&web_id=1271314784" language="JavaScript"></script>
<style>body > a[title=站长统计] {
        display: none
    }</style>
</body>
</html>
