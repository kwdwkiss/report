<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">联系客服</div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12"><h4>QQ：</h4></div>
                </div>
                <div class="row">
                    <div class="col-xs-6 col-md-3 text-center" v-for="(item, index) in page.service_qq" :key="index">
                        <span class="service-qq">{{item.name}}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12"><h4>微信：</h4></div>
                </div>
                <div class="row">
                    <div class="col-xs-6 col-md-3 text-center" v-for="(item, index) in page.service_wx_id" :key="index">
                        <span class="service-qq">{{item.name}}</span>
                    </div>
                </div>
                <!--<div class="row">-->
                    <!--<div class="col-md-12"><h4>微信：</h4></div>-->
                <!--</div>-->
                <!--<div class="row">-->
                    <!--<div class="col-xs-6 col-md-3" v-for="(item, index) in page.service_wx" :key="index">-->
                        <!--<img class="service-wx center-block" :src="item.name" alt="">-->
                    <!--</div>-->
                <!--</div>-->
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "CustomerService",
        computed: {
            page: function () {
                return this.$store.state.page;
            }
        }
    }
</script>

<style scoped>
    .service-qq {
        color: green;
        font-size: 18px;
        font-weight: bold;
    }

    .service-wx {
        width: 100%;
        max-width: 150px;
    }
</style>