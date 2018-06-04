<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=990">

    <meta name="keywords" content="宏海网络 淘宝验号"/>
    <meta name="description" content="宏海网络 淘宝验号"/>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>宏海网络</title>

    <style>
        .layer {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background-image: url('/images/check.png');
            background-color: #000;
            opacity: 0.15;
        }

        .timer {
            position: fixed;
            top: 50px;
            right: 20px;
            color: #000;
            font-size: 30px;
        }

        .geo {
            position: fixed;
            top: 100px;
            right: 20px;
            color: #000;
            font-size: 30px;
        }
    </style>
</head>
<body>
<div class="layer"></div>
<div class="geo">{{$geo}}</div>
<div id="timer" class="timer"></div>
<iframe id="my-frame" src="{{$page}}" frameborder="0" style="width: 100%;min-height: 800px"></iframe>
</body>
<script>
    var timer = document.getElementById('timer');
    var frame = document.getElementById('my-frame');
    var height = window.innerHeight;
    frame.getAttributeNode('style').value = "width: 100%;height: " + height + "px";
    timer.innerHTML = (new Date).toISOString();
    setInterval(function () {
        timer.innerHTML = (new Date).toISOString();
    }, 1000);
</script>
<!-- Scripts -->
<script type="text/javascript" src="{{ mix("js/index/app.js") }}"></script>
<script src="https://s22.cnzz.com/z_stat.php?id=1271314784&web_id=1271314784" language="JavaScript"></script>
<style>body > a[title=站长统计] {
        display: none
    }</style>
</html>