<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">积分充值</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">充值说明</label>
                        <div class="col-sm-9">
                            <div class="row">
                                <span>1、1元=100积分，最低充值金额1元，请用支付宝扫描下方二维码，<span class="text-danger">备注填写注册手机号：{{user.mobile}}（否则不能自动到账）</span></span>
                            </div>
                            <div class="row">
                                <span>2、完成支付宝转账后，请点击充值完成按钮，耐心等待积分到账</span>
                            </div>
                            <div class="row">
                                <span>3、若5分钟后未能到账，或忘记备注手机号，请联系充值客服，微信号：ywh171337832</span>
                            </div>
                            <div class="row">
                                <img style="width: 200px" src="/images/tbpzw_alipay.png" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-3 col-sm-9">
                            <a class="btn btn-success" @click="doRecharge">充值完成</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Recharge",
        data: function () {
            return {}
        },
        computed: {
            user: function () {
                return this.$store.state.user;
            },
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
            },
        }
    }
</script>

<style scoped>

</style>