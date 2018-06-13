<template>
    <div>
        <div class="row">
            <h3 class="text-center text-success">京东验号工具</h3>
        </div>
        <div class="row">
            <p class="col-xs-offset-1 col-xs-10 text-danger">说明：用微信扫描下方二维码，依次点击下方链接，进入相应页面后截图保存即可。</p>
            <p class="col-xs-offset-1 col-xs-10 text-danger">注意：验号前京东需要绑定微信</p>
        </div>
        <div class="row">
            <p class="col-xs-offset-1 col-xs-5">
                <a href="/check_jd?page=jd_member">1、京东会员</a>
            </p>
            <p class="col-xs-offset-1 col-xs-5">
                <a href="/check_jd?page=credit">2、小白信用</a>
            </p>
            <p class="col-xs-offset-1 col-xs-5">
                <a href="/check_jd?page=my_jd">3、我的京东</a>
            </p>
            <p class="col-xs-offset-1 col-xs-5">
                <a href="/check_jd?page=account">4、账户设置</a>
            </p>
        </div>
        <div class="row">
            <img class="center-block" src="/images/check_jd_qrcode.png"
                 alt="" style="width: 280px">
        </div>
        <hr>
        <div class="row hidden-md hidden-lg hidden-sm">
            <a :href="page.mobile_ad.check_pdd.url">
                <img :src="page.mobile_ad.check_pdd.img_src" style="width: 100%;max-height: 100px">
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "CheckPdd",
        computed: {
            page: function () {
                return this.$store.state.page;
            },
        }
    }
</script>

<style scoped>

</style>