<template>
    <div>
        <div class="row">
            <h3 class="text-center text-success">淘宝验号工具</h3>
        </div>
        <div class="row">
            <p class="col-xs-offset-1 col-xs-10 text-danger">说明：用手机淘宝扫描下方二维码，依次点击下方链接，进入淘宝页面后截图保存即可。</p>
        </div>
        <div class="row">
            <p class="col-xs-offset-1 col-xs-10">
                <a href="/check_tb?page=my_rate">1、淘宝评价</a>
            </p>
            <p class="col-xs-offset-1 col-xs-10">
                <a href="/check_tb?page=item_list">2、淘宝订单</a>
            </p>
            <p class="col-xs-offset-1 col-xs-10">
                <a href="https://market.m.taobao.com/apps/market/m-vip/raise-naughty.html">3、淘气值</a>
            </p>
            <p class="col-xs-offset-1 col-xs-10">
                <a href="/check_tb?page=appeal_center">4、账号体检</a>
            </p>
            <p class="col-xs-offset-1 col-xs-10">
                <a href="/check_tb?page=account_profile">5、个人交易信息</a>
            </p>
        </div>
        <div class="row">
            <img class="center-block" src="images/check_tb_qrcode.png" alt="" style="width: 180px">
            <p class="text-center text-primary">淘宝验号二维码</p>
        </div>
    </div>
</template>

<script>
    export default {
        name: "CheckTb"
    }
</script>

<style scoped>

</style>