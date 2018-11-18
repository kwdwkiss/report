<template>
    <div>
        <div class="panel">
            <div class="panel-heading">编辑管理员</div>
            <div class="panel-body">
                <el-form ref="form" :model="form">
                    <el-form-item label="名称" labelWidth="100px">
                        <label>{{form.name}}</label>
                    </el-form-item>
                    <el-form-item label="密码" labelWidth="100px">
                        <el-input v-model="form.password" type="password"></el-input>
                    </el-form-item>
                    <el-form-item label="角色" labelWidth="100px">
                        <el-checkbox-group v-model="form.roles_ids">
                            <el-checkbox :label="item.id" v-for="item in roles" :key="'admin_edit_'+item.id">
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
        name: "Update",
        data: function () {
            return {
                apiShow: api.agentAdminShow,
                apiUpdate: api.agentAdminUpdate,
                form: {roles_ids: []},
            }
        },
        computed: {
            roles: function () {
                return this.$store.state.roles;
            }
        },
        created: function () {
            this.loadData();
            this.$store.commit('roles');
        },
        methods: {
            loadData: function () {
                let self = this;
                let id = self.$route.params.id;
                axios.get(self.apiShow, {params: {id: id}}).then(function (res) {
                    self.form = res.data.data;
                });
            },
            doReturn: function () {
                this.$router.push({name: 'adminIndex'});
            },
            doSubmit: function () {
                let self = this;
                axios.post(self.apiUpdate, self.form).then(function () {
                    self.$message.success('成功');
                    self.doReturn();
                });
            },
        }
    }
</script>

<style scoped>

</style>