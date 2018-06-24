<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width">

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
            background-image: url('/images/check_bg.png');
            background-color: #000;
            opacity: 0.15;
        }

        .timer {
            position: fixed;
            top: 10px;
            right: 10px;
            color: #000;
            font-size: 18px;
        }

        .geo {
            position: fixed;
            top: 40px;
            right: 10px;
            color: #000;
            font-size: 18px;
        }

        iframe {
            overflow: scroll;
            -webkit-overflow-scrolling: touch;
            min-width: 100%;
            *width: 100%;
            width: 1px;
        }
    </style>
</head>
<body>
<div class="layer"></div>
<div class="geo">{{$geo}}</div>
<div id="timer" class="timer"></div>
<iframe id="my-frame" src="{{$page}}" frameborder="0" scrolling="no" style="min-height: 800px"></iframe>
</body>
<script>
    var timer = document.getElementById('timer');
    var frame = document.getElementById('my-frame');
    var height = window.innerHeight;
    frame.getAttributeNode('style').value = "height: " + height + "px";
    function dateFtt(fmt,date)
    { //author: meizz
        var o = {
            "M+" : date.getMonth()+1,                 //月份
            "d+" : date.getDate(),                    //日
            "h+" : date.getHours(),                   //小时
            "m+" : date.getMinutes(),                 //分
            "s+" : date.getSeconds(),                 //秒
            "q+" : Math.floor((date.getMonth()+3)/3), //季度
            "S"  : date.getMilliseconds()             //毫秒
        };
        if(/(y+)/.test(fmt))
            fmt=fmt.replace(RegExp.$1, (date.getFullYear()+"").substr(4 - RegExp.$1.length));
        for(var k in o)
            if(new RegExp("("+ k +")").test(fmt))
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        return fmt;
    }
    timer.innerHTML = dateFtt("yyyy-MM-dd hh:mm:ss",new Date);
    setInterval(function () {
        timer.innerHTML = (new Date).toISOString();
    }, 1000);
</script>
<!-- Scripts -->
<script src="https://s22.cnzz.com/z_stat.php?id=1271314784&web_id=1271314784" language="JavaScript"></script>
<style>body > a[title=站长统计] {
        display: none
    }</style>
</html>
