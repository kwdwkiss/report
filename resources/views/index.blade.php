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
</head>
<body>
<div id="app"></div>
<hr>
<div class="row copyright">
    <div>
        <p>
            <script type="text/javascript" src="https://s22.cnzz.com/z_stat.php?id=1271314784&web_id=1271314784"></script>
            |
            Copyright©2015-2020 www.tbpzw.com .All Rights Reserved ICP证：桂ICP备14007039号
        </p>
    </div>
</div>
<!-- Scripts -->
<script type="text/javascript" src="{{ mix("js/index/app.js") }}"></script>
</body>
</html>
