<template>
    <div>
        <div class="panel">
            <div class="panel-heading">创建</div>
            <div class="panel-body">
                <el-form ref="form" :model="form">
                    <el-form-item prop="type" label="账号类型" labelWidth="100px">
                        <el-select v-model="form.type" placeholder="账号类型">
                            <el-option v-for="item in accountTypeList" :key="item.id" :value="item.id"
                                       :label="item.name"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item prop="name" label="账号" labelWidth="100px">
                        <el-input v-model="form.name"></el-input>
                    </el-form-item>
                    <el-form-item prop="status" label="账号状态" labelWidth="100px">
                        <el-select v-model="form.status">
                            <el-option v-for="item in accountStatusList" :key="item.id" :value="item.id"
                                       :label="item.name"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item prop="auth_cash" label="合作金额" labelWidth="100px">
                        <el-input v-model="form.auth_cash"></el-input>
                    </el-form-item>
                    <el-form-item prop="address" label="常用地址" labelWidth="100px">
                        <el-input v-model="form.address"></el-input>
                    </el-form-item>
                    <el-form-item prop="remark" label="备注" labelWidth="100px">
                        <el-input v-model="form.remark"></el-input>
                    </el-form-item>
                    <el-form-item labelWidth="100px">
                        <el-button type="primary" @click="doReturn">返回</el-button>
                        <el-button type="success" @click="doSubmit">提交</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Recharge",
        data: function () {
            return {
                apiCreate: api.adminAccountCreate,
                routeNameList: 'accountList',
                form: {},
            }
        },
        computed: {
            accountTypeList: function () {
                return this.$store.state.taxonomy.account_type;
            },
            accountStatusList: function () {
                return this.$store.state.taxonomy.account_status;
            }
        },
        methods: {
            doReturn: function () {
                this.$router.push({name: this.routeNameList});
            },
            doSubmit: function () {
                let self = this;
                axios.post(self.apiCreate, self.form).then(function () {
                    self.$message.success('成功');
                    self.doReturn();
                });
            },
            //self
        }
    }
</script>

<style scoped>

</style>