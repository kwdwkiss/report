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
            <div class="col-xs-3 service-wx">
                <ul>
                    <li class="col-xs-12">客服微信：</li>
                    <li class="col-xs-6" v-for="item in page.service_wx">{{item.name}}</li>
                </ul>
            </div>
            <div class="col-xs-3 service-qq">
                <ul>
                    <li class="col-xs-12">客服QQ：</li>
                    <li class="col-xs-6" v-for="item in page.service_qq">{{item.name}}</li>
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
            <span>公告：{{page.notice}}</span>
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
    let page = window.laravel;

    export default {
        name: "app",
        data: function () {
            return {
                page: page,
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

    .search {
        background-color: #f5f5f5;
        line-height: 60px;
    }

    .search > div:first-child > * {
        margin: 0 5px;
        height: 35px;
    }

    .search select {
        width: 100px;
    }

    .search input[name=name] {
        width: 250px;
    }

    .search button {
        width: 80px;
    }

    .member-num {
        font-size: 16px;
        font-weight: 600;
    }

    .member-num span {
        color: green;
    }

    .report-num {
        font-size: 16px;
        font-weight: 600;
    }

    .report-num span {
        color: red;
    }

    .report-data {
        font-size: 16px;
    }

    .search-data {
        font-size: 16px;
        font-weight: 600;
    }

    .article-data > div {
        margin: 5px 0;
    }

    .article-data > div > div {
        border: 1px solid #9d9d9d;
        border-radius: 3px;
        height: 160px;
    }

    .article-data p {
        padding: 5px 10px;
    }

    .article-data p:first-child {
        background-color: #f5f5f5;
    }

    .article-data p:first-child > a {
        float: right;
    }

    .report-form {
        background-color: #f5f5f5;
        line-height: 60px;
    }

    .report-form > div > * {
        margin: 0 5px;
        height: 35px;
    }

    .report-form select {
        width: 100px;
    }

    .report-form input[name=name] {
        width: 200px;
    }

    input[name=captcha] {
        width: 100px;
    }

    .copyright {
        text-align: center;
    }
</style>