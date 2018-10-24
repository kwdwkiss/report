<template>
    <div>
        <div class="hidden-xs hidden-sm row logo">
            <div class="col-md-6">
                <a href="/#/">
                    <img src="/images/logo.png">
                </a>
            </div>
            <div class="hidden-xs hidden-sm service-wx col-xs-12 col-md-3">
                <ul>
                    <li>微信<br/>公众号：</li>
                    <li v-for="(item, index) in page.service_wx" :key="'service_wx_'+index" v-if="item.name">
                        <img :src="item.name" alt="">
                    </li>
                </ul>
            </div>
        </div>
        <div class="row hidden-xs hidden-sm" style="margin: 2px">
            <div class="service-qq col-md-2">QQ客服：</div>
            <div class="service-qq col-md-2" v-for="(item, index) in page.service_qq" :key="'service_qq_'+index">
                {{item.name}}
            </div>
        </div>
        <div class="row hidden-xs hidden-sm" style="margin: 2px">
            <div class="service-qq col-md-2">微信客服：</div>
            <div class="service-qq col-md-2" v-for="(item, index) in page.service_wx_id" :key="'service_qq_'+index">
                {{item.name}}
            </div>
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
        font-size: 14px;
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