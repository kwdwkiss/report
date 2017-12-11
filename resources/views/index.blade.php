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
<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1271314784'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s22.cnzz.com/z_stat.php%3Fid%3D1271314784%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));</script>
<body>
<div id="app"></div>

<!-- Scripts -->
<script src="{{ mix("js/index/app.js") }}"></script>
</body>
</html>
