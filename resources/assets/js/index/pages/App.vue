<template>
    <div>
        <my-nav></my-nav>

        <div class="container" style="padding-top:51px">

            <top-ad></top-ad>

            <my-logo></my-logo>

            <my-notice></my-notice>

            <!--<my-breadcrumb></my-breadcrumb>-->

            <router-view></router-view>

            <hr>

            <my-copyright></my-copyright>

        </div>

        <pop-window></pop-window>
    </div>
</template>

<script>
    export default {
        name: "app",
    };
</script>

<style>
    @media (min-width: 1200px) {
        .container {
            width: 970px;
        }
    }

    body {
        background-color: #fff;
        color: #222;
    }

    ul {
        padding-left: 0;
        margin-bottom: 0;
    }

    li {
        list-style: none;
    }

    .row {
        margin: 10px 0;
    }

    .row > div {
        padding: 0 5px;
    }

    @media (max-width: 768px) {
        .el-message {
            min-width: 300px;
        }
    }

    @media (min-width: 768px) {
        .el-message {
            /*width: 50%;*/
        }
    }

    .el-message {
        top: 10%;
        font-size: 18px;
    }

    .el-message > p {
        font-size: 18px;
    }
</style>