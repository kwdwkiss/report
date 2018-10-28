<template>
    <div>
        <div class="panel">
            <div class="panel-heading">创建</div>
            <div class="panel-body">
                <el-form ref="form" :model="form">
                    <!--<el-form-item prop="type" label="会员类型" labelWidth="100px">-->
                    <!--<el-select v-model="form.type">-->
                    <!--<el-option v-for="item in userTypeList" :key="item.id" :value="item.id"-->
                    <!--:label="item.name"></el-option>-->
                    <!--</el-select>-->
                    <!--</el-form-item>-->
                    <el-form-item prop="mobile" label="手机号" labelWidth="100px">
                        <el-input v-model="form.mobile"></el-input>
                    </el-form-item>
                    <el-form-item label="密码" labelWidth="100px">
                        <el-input type="password" v-model="form.password"></el-input>
                    </el-form-item>
                    <el-form-item label="邀请人" labelWidth="100px">
                        <el-input v-model="form.inviter"></el-input>
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
                apiCreate: api.adminUserCreate,
                form: {_profile: {}, _merchant: {}},
            }
        },
        computed: {
            userTypeList: function () {
                return this.$store.state.taxonomy.user_type;
            }
        },
        methods: {
            doReturn: function () {
                this.$router.push({name: 'userList'});
            },
            doSubmit: function () {
                let self = this;
                axios.post(self.apiCreate, self.form).then(function () {
                    self.$message.success('成功');
                    self.doReturn();
                });
            },
        }
    }
</script>

<style scoped>

</style>