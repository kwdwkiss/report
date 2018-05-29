<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <meta name="keywords" content="宏海网络 淘宝验号"/>
    <meta name="description" content="宏海网络 淘宝验号"/>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>宏海网络</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <style>
        .row {
            margin: 10px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <h3 class="text-center text-success">宏海网络--淘宝验号工具</h3>
    </div>
    <div class="row">
        <p class="col-xs-offset-1 col-xs-10 text-danger">说明：用手机淘宝扫描下方二维码，依次点击下方1-4个步骤，进入淘宝页面后截图保存即可。</p>
    </div>
    <div class="row">
        <p class="col-xs-offset-1 col-xs-10">
            <a href="{{url('/check_tb_page?page=my_rate')}}">1、淘宝评价</a>
        </p>
        <p class="col-xs-offset-1 col-xs-10">
            <a href="{{url('/check_tb_page?page=item_list')}}">2、淘宝订单</a>
        </p>
        <p class="col-xs-offset-1 col-xs-10">
            <a href="{{url('/check_tb_page?page=raise_naughty')}}">3、淘气值</a>
        </p>
        <p class="col-xs-offset-1 col-xs-10">
            <a href="{{url('/check_tb_page?page=appeal_center')}}">4、账号体检</a>
        </p>
    </div>
    <div class="row">
        <img class="center-block" src="images/check_tb_qrcode.png" alt="" style="width: 200px">
        <p class="text-center text-primary">淘宝验号工具二维码</p>
    </div>
</div>
<!-- Scripts -->
<script src="https://s22.cnzz.com/z_stat.php?id=1271314784&web_id=1271314784" language="JavaScript"></script>
<style>body > a[title=站长统计] {
        display: none
    }</style>
</body>
</html>
