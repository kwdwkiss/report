<template>
    <div class="panel">
        <div class="panel-heading">人工充值</div>
        <div class="panel-body">
            <el-form ref="form" :model="form">
                <el-form-item prop="mobile" label="用户手机号" labelWidth="100px">
                    <el-input v-model="form.mobile"></el-input>
                </el-form-item>
                <el-form-item prop="pay_no" label="支付类型" labelWidth="100px">
                    <el-select v-model="form.pay_type">
                        <el-option :key="1" :value="1" label="支付宝"></el-option>
                        <el-option :key="2" :value="2" label="微信"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item prop="pay_no" label="外部订单号" labelWidth="100px">
                    <el-input v-model="form.pay_no"></el-input>
                </el-form-item>
                <el-form-item prop="money" label="金额" labelWidth="100px">
                    <el-input v-model="form.money"></el-input>
                </el-form-item>
                <el-form-item labelWidth="100px">
                    <el-button type="primary" @click="doReturn">返回</el-button>
                    <el-button type="success" @click="doSubmit">提交</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Recharge",
        data: function () {
            return {
                api: api.adminRechargeCreate,
                form: {}
            }
        },
        methods: {
            doReturn: function () {
                this.$router.push({name: 'rechargeList'});
            },
            doSubmit: function () {
                let self = this;
                axios.post(self.api, self.form).then(function () {
                    self.$message.success('成功');
                    self.doReturn();
                });
            }
        }
    }
</script>

<style scoped>

</style>