<template>
    <div>
        <div class="panel">
            <div class="panel-heading">创建管理员</div>
            <div class="panel-body">
                <el-form ref="form" :model="form">
                    <el-form-item label="名称" labelWidth="100px">
                        <el-input v-model="form.name"></el-input>
                    </el-form-item>
                    <el-form-item label="密码" labelWidth="100px">
                        <el-input v-model="form.password" type="password"></el-input>
                    </el-form-item>
                    <el-form-item label="角色" labelWidth="100px">
                        <el-checkbox-group v-model="form.roles_ids">
                            <el-checkbox :label="item.id" v-for="item in roles" :key="'admin_create_'+item.id">
                                {{item.title}}
                            </el-checkbox>
                        </el-checkbox-group>
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
                apiCreate: api.adminAdminCreate,
                form: {roles_ids: []},
            }
        },
        computed: {
            roles: function () {
                return this.$store.state.roles;
            }
        },
        created: function () {
            this.$store.commit('roles');
        },
        methods: {
            doReturn: function () {
                this.$router.push({name: 'adminIndex'});
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