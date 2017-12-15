<template>
    <div>
        <div class="row ad">
            <div class="col-xs-6" v-for="item in page.ad_top">
                <a target="_blank" :href="item.url">
                    <img :src="item.img_src">
                </a>
            </div>
        </div>
        <div class="row ad">
            <div class="col-xs-3" v-for="item in page.ad_second">
                <a target="_blank" :href="item.url">
                    <img :src="item.img_src">
                </a>
            </div>
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
                    <li class="col-xs-6" v-for="item in page.service_qq">{{item.name}}</li>
                </ul>
            </div>
            <div class="col-xs-3 service-wx">
                <ul>
                    <li class="col-xs-6" v-for="item in page.service_wx">
                        <img :src="item.name" alt="">
                    </li>
                </ul>
            </div>
        </div>

        <div class="row nav">
            <ul>
                <a :href="item.url" v-for="item in page.menu">
                    <li>{{item.name}}</li>
                </a>
            </ul>
        </div>

        <div class="row notice">
            <span>公告：{{page.notice.title}}</span>
            <span><a :href="page.notice.moreUrl">更多</a></span>
        </div>

        <router-view></router-view>

        <div class="row ad">
            <div class="col-xs-3" v-for="item in page.ad_foot">
                <a target="_blank" :href="item.url">
                    <img :src="item.img_src">
                </a>
            </div>
        </div>

        <hr>

        <div class="row copyright">
            <div>
                <p>Copyright©2015-2020 www.tbpzw.com .All Rights Reserved ICP证：桂ICP备14007039号</p>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "app",
        data: function () {
            return {
                page: store.state.page,
            }
        }
    }
</script>

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