<template>
    <div>
        <my-nav></my-nav>

        <div class="container" style="padding-top:51px">

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

    @media (max-width: 768px) {
        body {
            font-size: 12px;
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

    p {
        margin-bottom: 0;
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
</style>