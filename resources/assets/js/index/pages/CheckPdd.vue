<template>
    <div>
        <div class="row">
            <h3 class="text-center text-success">拼多多验号工具</h3>
        </div>
        <div class="row">
            <p class="col-xs-offset-1 col-xs-10 text-danger">说明：用微信打开本页面，依次点击下方链接，进入相应页面后截图保存即可。</p>
        </div>
        <div class="row">
            <p class="col-xs-offset-1 col-xs-10">
                <a href="/check_pdd?page=personal">1、个人中心</a>
            </p>
            <p class="col-xs-offset-1 col-xs-10">
                <a href="/check_pdd?page=complaint_list">2、退款列表</a>
            </p>
        </div>
        <div class="row">
            <img class="center-block" src="images/check_pdd_qrcode.png" alt="" style="width: 180px">
            <p class="text-center text-primary">拼多多验号二维码</p>
        </div>
    </div>
</template>

<script>
    export default {
        name: "CheckPdd"
    }
</script>

<style scoped>

</style>