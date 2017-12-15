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
<style>
    body {
        margin: 0 auto;
        width: 1000px;
        min-height: 400px;
        background-color: #fff;
        color: #222
    }

    ul {
        padding-left: 0;
        margin-bottom: 0;
    }

    li {
        list-style: none;
    }

    th, td {
        text-align: center;
    }

    p {
        margin-bottom: 0;
    }

    input {
        line-height: normal;
    }

    a {
        color: #222;
    }

    .row {
        margin: 10px 0;
    }

    .row > div {
        padding: 0 5px;
    }

    .ad img, .logo img {
        width: 100%;
        height: 80px;
    }

    .nav {
        background-color: #e6e6e6;
        color: #505050;
        line-height: 40px;
        font-size: 16px;
        font-weight: 600;
    }

    .nav li {
        display: inline-block;
        float: left;
        padding: 0 35px;
    }

    .nav li:hover {
        background-color: #41A51D;
        color: #fff;
    }

    .notice {
        font-size: 16px;
        font-weight: 600;
        color: red;
    }

    .service-wx, .service-qq {
        color: green;
        font-size: 16px;
        font-weight: 600;
    }

    .service-wx img {
        height: 80px;
        width: 80px;
    }

    .copyright {
        text-align: center;
    }
</style>
<div>
    <div class="row ad">
        @foreach($page['ad_top'] as $item)
            <div class="col-xs-6">
                <a target="_blank" href="{{$item['url']}}">
                    <img src="{{$item['img_src']}}">
                </a>
            </div>
        @endforeach
    </div>
    <div class="row ad">
        @foreach($page['ad_second'] as $item)
            <div class="col-xs-3">
                <a target="_blank" href="{{$item['url']}}">
                    <img src="{{$item['img_src']}}">
                </a>
            </div>
        @endforeach
    </div>

    <div class="row logo">
        <div class="col-xs-6">
            <a href="">
                <img src="/images/logo.jpg">
            </a>
        </div>
        <div class="col-xs-3 service-qq">
            <ul>
                <li class="col-xs-12">客服QQ、微信：</li>
                @foreach($page['service_qq'] as $item)
                    <li class="col-xs-6">{{$item['name']}}</li>
                @endforeach
            </ul>
        </div>
        <div class="col-xs-3 service-wx">
            <ul>
                @foreach($page['service_wx'] as $item)
                    <li class="col-xs-6">
                        <img src="{{$item['name']}}" alt="">
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div class="row nav">
        <ul>
            @foreach($page['menu'] as $item)
                <a href="{{$item['url']}}">
                    <li>{{$item['name']}}</li>
                </a>
            @endforeach
        </ul>
    </div>

    <div class="row notice">
        <span>公告：{{$page['notice']['title']}}</span>
        <span><a href="{{$page['notice']['moreUrl']}}">更多</a></span>
    </div>

    @yield('content')

    <div class="row ad">
        @foreach($page['ad_foot'] as $item)
            <div class="col-xs-3">
                <a target="_blank" href="{{$item['url']}}">
                    <img src="{{$item['img_src']}}">
                </a>
            </div>
        @endforeach
    </div>

    <hr>

    <div class="row copyright">
        <div>
            <p>Copyright©2015-2020 www.tbpzw.com .All Rights Reserved ICP证：桂ICP备14007039号</p>
        </div>
    </div>
</div>

<!-- Scripts -->
<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");
    document.write(unescape("%3Cspan id='cnzz_stat_icon_1271314784'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s22.cnzz.com/z_stat.php%3Fid%3D1271314784%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));</script>
</body>
</html>