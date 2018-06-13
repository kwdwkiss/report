<template>
    <div>
        <div class="row">
            <h3 class="text-center text-success">京东验号工具</h3>
        </div>
        <div class="row">
            <p class="col-xs-offset-1 col-xs-10 text-danger">说明：用手机京东扫描下方二维码，依次点击下方链接，进入相应页面后截图保存即可。</p>
        </div>
        <div class="row">
            <p class="col-xs-offset-1 col-xs-5">
                <a href="https://vip.m.jd.com/?sceneval=2&sid=">1、京东会员</a>
            </p>
            <p class="col-xs-offset-1 col-xs-5">
                <a href="https://wqs.jd.com/my/accountv2.shtml?sceneval=2">2、账户设置</a>
            </p>
            <p class="col-xs-offset-1 col-xs-5">
                <a href="https://home.m.jd.com/myJd/newhome.action">3、我的</a>
            </p>
            <p class="col-xs-offset-1 col-xs-5">
                <a href="https://wqs.jd.com/my/accountv2.shtml?sceneval=2">4、账户设置</a>
            </p>
        </div>
        <div class="row">
            <img class="center-block" src="https://tbpzw-static.oss-cn-shenzhen.aliyuncs.com/check_pdd_qrcode.png"
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