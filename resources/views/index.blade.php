<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <meta name="keywords" content="宏海网络 恶意账号 查询工具 查询骗子"/>
    <meta name="description" content="宏海网络 恶意账号 查询工具 查询骗子"/>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>宏海网络</title>

    <script>
        window.laravel = {!! json_encode($page) !!};
    </script>

    <!--[if lt IE 9]>
    <script>
        if(window.attachEvent){
            window.attachEvent('onload', function(){
                var lowestSupportedIEVersion = 9;
                if(window.LOWEST_IE_VERSION != undefined){
                    lowestSupportedIEVersion = window.LOWEST_IE_VERSION;
                }
                var el = document.createElement('div'),
                        elStyle = el.style,
                        docBody = document.getElementsByTagName('body')[0],
                        linkStyle = 'color:#06F;text-decoration: underline;';
                el.innerHTML =	'尊敬的用户：<br />'+
                        '使用宏海网络需要安装Internet Explorer更新版本的浏览器，'+
                        '请<a href="http://windows.microsoft.com/zh-cn/internet-explorer/download-ie" style="'+linkStyle+'" target="_blank">下载安装IE' + lowestSupportedIEVersion + '</a>（或更新）。'+
                        '也可以在其他浏览器，'+
                        '如<a href="https://www.baidu.com/s?ie=UTF-8&wd=chrome" style="'+linkStyle+'" target="_blank">Chrome</a>'+
                        '或<a href="http://www.firefox.com.cn/download/" style="'+linkStyle+'" target="_blank">Firefox</a>火狐中打开控制台。';
                elStyle.width = '720px';
                elStyle.color = '#000';
                elStyle.fontSize = '14px';
                elStyle.lineHeight = '180%';
                elStyle.margin = '60px auto';
                elStyle.backgroundColor = '#fffbd5';
                elStyle.border = '1px solid #CCC';
                elStyle.padding = '24px 48px';
                docBody.innerHTML = '';
                docBody.appendChild(el);
            });
        }
    </script>
    <![endif]-->

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
