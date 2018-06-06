<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">积分充值</div>
            <div class="panel-body">
                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">充值说明</label>
                        <div class="col-sm-9 form-control-static">
                            <p>1、支付宝扫描下方二维码，输入需要充值的金额，最低充值1元。（1元=100积分）</p>
                            <p>2、支付宝转款一定要备注手机号：<span class="text-danger">{{user.mobile}}</span>，并且只写手机号，否者系统无法识别</p>
                            <p>3、完成支付宝转账后，请点击
                                <a class="btn btn-success" @click="doRecharge" style="padding: 4px 10px">充值完成</a>
                                ，或刷新页面积分即可到账</p>
                            <p>4、若5分钟后积分未能到账，请联系充值客服，微信号：ywh171337832</p>
                            <div class="col-md-8">
                                <img class="center-block" style="width: 180px" src="/images/tbpzw_alipay.png" alt="">
                            </div>
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
                this.$router.push('/');
            },
        }
    }
</script>

<style scoped>

</style>