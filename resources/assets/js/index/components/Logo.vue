<template>
    <div class="hidden-xs hidden-sm row logo">
        <div class="col-md-6">
            <a href="/#/">
                <img src="/images/logo.png">
            </a>
        </div>
        <div class="hidden-xs hidden-sm service-qq col-xs-12 col-md-3">
            <ul>
                <li class="col-xs-12">QQ客服：</li>
                <li class="col-xs-6" v-for="(item, index) in page.service_qq" :key="index">
                    {{item.name}}
                </li>
                <li class="col-xs-12">微信客服：</li>
                <li class="col-xs-6" v-for="(item, index) in page.service_wx_id" :key="index">
                    {{item.name}}
                </li>
            </ul>
        </div>
        <div class="hidden-xs hidden-sm service-wx col-xs-12 col-md-3">
            <ul>
                <li>微信<br/>公众号：</li>
                <li v-for="(item, index) in page.service_wx" :key="index" v-if="item.name">
                    <img :src="item.name" alt="">
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Logo",
        computed: {
            user: function () {
                return this.$store.state.user;
            },
            page: function () {
                return this.$store.state.page;
            }
        }
    };
</script>

<style scoped>
    .logo img {
        width: 100%;
        height: 80px;
    }

    @media (max-width: 768px) {
        .logo img {
            height: auto;
        }
    }

    .service-qq {
        color: green;
        font-size: 16px;
        font-weight: 600;
    }

    .service-wx {
        color: green;
        font-size: 16px;
        font-weight: 600;
    }

    .service-wx li {
        margin: 0 2px;
        float: left;
    }

    .service-wx img {
        height: 80px;
        width: 80px;
    }
</style>