<template>
    <div>
        <div class="panel">
            <div class="panel-heading">编辑角色</div>
            <div class="panel-body">
                <el-form ref="form" :model="form">
                    <el-form-item label="平台" labelWidth="100px">
                        <label>{{form.guard_title}}</label>
                    </el-form-item>
                    <el-form-item label="角色" labelWidth="100px">
                        <label>{{form.title}}</label>
                    </el-form-item>
                    <el-form-item label="权限" labelWidth="100px">
                        <el-checkbox-group v-model="form.permissions">
                            <el-checkbox :label="item.id" v-for="item in permissions" :key="item.id">
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
                apiShow: api.agentRoleShow,
                apiUpdate: api.agentRoleUpdate,
                form: {permissions: [1, 2]},
            }
        },
        computed: {
            roles: function () {
                return this.$store.state.roles;
            },
            permissions: function () {
                return this.$store.state.permissions;
            }
        },
        created: function () {
            this.loadData();
            this.$store.commit('permissions');
        },
        methods: {
            loadData: function () {
                let self = this;
                let id = self.$route.params.id;
                axios.get(self.apiShow, {params: {id: id}}).then(function (res) {
                    res.data.data.permissions = _.map(res.data.data.permissions, function (item) {
                        return item.id;
                    });
                    Vue.set(self, 'form', res.data.data);
                });
            },
            doReturn: function () {
                this.$router.push({name: 'roleIndex'});
            },
            doSubmit: function () {
                let self = this;
                axios.post(self.apiUpdate, self.form).then(function () {
                    self.$message.success('成功');
                    self.loadData();
                    //self.doReturn();
                });
            },
        }
    }
</script>

<style scoped>

</style>