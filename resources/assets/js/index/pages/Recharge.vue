<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">积分充值</div>
            <div class="panel-body">
                <div class="row" style="font-size: 16px;font-weight: bold">
                    <div>
                        <label class="radio-inline">
                            <input type="radio" name="way" :value="1" v-model="way">
                            充值方式一：（<span class="text-danger">推荐，不用写备注</span>）
                        </label>
                    </div>
                    <!--<div>-->
                    <!--<label class="radio-inline">-->
                    <!--<input type="radio" name="way" :value="2" v-model="way">-->
                    <!--充值方式二：（<span class="text-danger">需要填写手机号备注</span>）-->
                    <!--</label>-->
                    <!--</div>-->
                </div>
                <div class="row" v-show="way===1">
                    <template v-if="!basic.close_recharge">
                        <p>如有支付问题，请联系微信：ywh171337832</p>
                        <div class="col-md-8">
                            <iframe :src="pay_src" id="myiframe" scrolling="no" width="100%" height="350"
                                    frameborder="0"></iframe>
                        </div>
                        <div class="col-md-4">
                            <p class="text-center" style="color: red;font-size: 18px;font-weight: bold">支付宝扫码领红包福利</p>
                            <img class="center-block" src="/images/alipay_redpack.png" alt="" style="max-width: 200px">
                        </div>
                    </template>
                    <template v-else>
                        <h2 class="text-danger">{{basic.close_recharge_text}}</h2>
                    </template>
                </div>
                <div class="row" v-show="way===2">
                    <div class="col-md-8">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-md-2 control-label">充值说明</label>
                                <div class="col-md-8 form-control-static">
                                    <p>1、支付宝扫描下方二维码，输入需要充值的金额，最低充值1元。（1元=100积分）</p>
                                    <p>2、支付宝转款一定要备注手机号：<span class="text-danger">{{user.mobile}}</span>，并且只写手机号，否者系统无法识别
                                    </p>
                                    <p>3、完成支付宝转账后，请点击
                                        <a class="btn btn-success" @click="doRecharge"
                                           style="padding: 4px 10px">充值完成</a>
                                        ，或刷新页面积分即可到账</p>
                                    <p>4、若5分钟后积分未能到账，请联系充值客服，微信号：ywh171337832</p>
                                    <div class="col-xs-6">
                                        <img class="center-block" style="width:100%;max-width: 200px"
                                             src="/images/recharge_img_demo.png" alt="">
                                    </div>
                                    <div class="col-xs-6">
                                        <img class="center-block" style="width:100%;max-width: 200px"
                                             src="/images/tbpzw_alipay.png" alt="">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <p class="text-center" style="color: red;font-size: 18px;font-weight: bold">支付宝扫码领红包福利</p>
                        <img class="center-block" src="/images/alipay_redpack.png" alt="" style="max-width: 200px">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Recharge",
        data: function () {
            return {
                way: 1,
                appid: '2018062932',
                domain: 'pay.tbpzw.com',
                back_url: encodeURIComponent('http://' + location.host + '/user/recharge/page-callback'),
            }
        },
        computed: {
            user: function () {
                return this.$store.state.user;
            },
            pay_src: function () {
                return `http://${this.domain}/pay/?appid=${this.appid}&payno=${this.user.mobile}&back_url=${this.back_url}`
            },
            basic: function () {
                return this.$store.state.basic;
            },
        },
        mounted: function () {
            this.$store.commit('basic');
        },
        methods: {
            doRecharge: function () {
                let self = this;
                let count = 5;
                self.$store.commit("user");
                let handle = setInterval(function () {
                    if (count <= 0) {
                        clearInterval(handle);
                    } else {
                        self.$store.commit("user");
                        count--;
                    }
                }, 10000);
                this.$router.push('/');
            },
        }
    }
</script>

<style scoped>

</style>