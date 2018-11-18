<template>
    <div>
        <div v-show="action=='list'">
            <el-row class="search">
                <el-input v-model="search.name" placeholder="名称"></el-input>

                <el-button type="primary" @click="loadData">搜索</el-button>
                <el-button type="warning" @click="reset">重置</el-button>
                <el-button type="success" @click="openCreateDialog">发送通知</el-button>

                <el-pagination layout="prev, pager, next"
                               :total="dataList.meta.total"
                               :page-size="dataList.meta.per_page"
                               @current-change="paginate"></el-pagination>
            </el-row>

            <el-row>
                <el-table :data="dataList.data" stripe>
                    <el-table-column prop="id" label="ID" min-width="50"></el-table-column>
                    <!--<el-table-column label="URL" min-width="50">-->
                    <!--<template slot-scope="scope">-->
                    <!--<a target="_blank" :href="scope.row.url">查看</a>-->
                    <!--</template>-->
                    <!--</el-table-column>-->
                    <el-table-column prop="name" label="名称" min-width="100"></el-table-column>
                    <el-table-column prop="content" label="内容" min-width="200"></el-table-column>
                    <el-table-column prop="remark" label="备注" min-width="160"></el-table-column>
                    <el-table-column prop="created_at" label="创建时间" min-width="170"></el-table-column>
                    <el-table-column label="操作" min-width="400">
                        <template slot-scope="scope">
                            <el-button type="warning" @click="openUpdateDialog(scope)">修改</el-button>
                            <el-button type="danger" @click="doDelete(scope)">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </el-row>
        </div>
        <div v-show="action=='create'">
            <div class="panel">
                <div class="panel-heading">发送通知</div>
                <div class="panel-body">
                    <el-form ref="createForm" :model="dialogCreate.data" :rules="rules">
                        <el-form-item prop="userType" label="选择用户" labelWidth="100px">
                            <el-radio v-model="dialogCreate.data.userSelect" :label="1">全部用户</el-radio>
                            <el-radio v-model="dialogCreate.data.userSelect" :label="2">用户类型</el-radio>
                            <el-radio v-model="dialogCreate.data.userSelect" :label="3">指定ID</el-radio>
                            <el-radio v-model="dialogCreate.data.userSelect" :label="4">指定手机号</el-radio>
                        </el-form-item>
                        <el-form-item prop="users" label="用户" labelWidth="100px">
                            <label v-if="dialogCreate.data.userSelect==1">所有用户</label>
                            <el-select v-if="dialogCreate.data.userSelect==2" v-model="dialogCreate.data.userType"
                                       placeholder="会员类型">
                                <el-option v-for="item in userTypeList" :key="item.id" :value="item.id"
                                           :label="item.name"></el-option>
                            </el-select>
                            <el-input v-if="dialogCreate.data.userSelect==3" type="textarea" :rows="3"
                                      v-model="dialogCreate.data.userId" placeholder="id用英文逗号隔开"></el-input>
                            <el-input v-if="dialogCreate.data.userSelect==4" type="textarea" :rows="3"
                                      v-model="dialogCreate.data.userMobile" placeholder="多个手机号码用英文逗号隔开"></el-input>
                        </el-form-item>
                        <el-form-item prop="name" label="名称" labelWidth="100px">
                            <el-input v-model="dialogCreate.data.name"></el-input>
                        </el-form-item>
                        <el-form-item prop="content" label="内容" labelWidth="100px">
                            <el-input type="textarea" :rows="3" v-model="dialogCreate.data.content"></el-input>
                        </el-form-item>
                        <el-form-item prop="remark" label="备注" labelWidth="100px">
                            <el-input v-model="dialogCreate.data.remark"></el-input>
                        </el-form-item>
                        <el-form-item labelWidth="100px">
                            <el-button type="primary" @click="action='list'">返回</el-button>
                            <el-button type="success" @click="doCreate">提交</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </div>
        <div v-show="action=='update'">
            <div class="panel">
                <div class="panel-heading">编辑文章</div>
                <div class="panel-body">
                    <el-form ref="createForm" :model="dialogUpdate.data" :rules="rules">
                        <el-form-item prop="title" label="标题" labelWidth="100px">
                            <el-input v-model="dialogUpdate.data.title"></el-input>
                        </el-form-item>
                        <el-form-item prop="remark" label="备注" labelWidth="100px">
                            <el-input v-model="dialogUpdate.data.remark"></el-input>
                        </el-form-item>
                        <el-form-item prop="content" label="内容" labelWidth="100px">
                        </el-form-item>
                        <el-form-item labelWidth="100px">
                            <el-button type="primary" @click="action='list'">返回</el-button>
                            <el-button type="success" @click="doUpdate">提交</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                action: 'list',
                apiList: api.agentMessageIndex,
                apiCreate: api.agentMessageCreate,
                apiDelete: api.agentMessageDelete,
                dataList: {
                    meta: {},
                },
                search: {},
                dialogCreate: {
                    display: false,
                    data: {}
                },
                dialogUpdate: {
                    display: false,
                    data: {}
                },
                rules: {},
                editor: {}
            }
        },
        computed: {
            userTypeList: function () {
                return this.$store.state.taxonomy.user_type;
            }
        },
        created: function () {
            this.loadData();
        },
        methods: {
            createEditorReady: function (ue) {
                this.editor.create = ue;
            },
            updateEditorReady: function (ue) {
                this.editor.update = ue;
                this.editor.update.setContent(this.dialogUpdate.data.content);
            },
            loadData: function () {
                let self = this;
                axios.get(self.apiList, {params: self.search}).then(function (res) {
                    self.dataList = res.data;
                });
            },
            paginate: function (page) {
                this.search.page = page;
                this.loadData();
            },
            reset: function () {
                this.search = {};
                this.loadData();
            },
            openCreateDialog: function () {
                this.dialogCreate.data = {userSelect: 4, name: '', content: '', remark: ''};
                this.action = 'create';
            },
            doCreate: function () {
                let self = this;
                self.$refs.createForm.validate((valid) => {
                    if (valid) {
                        axios.post(self.apiCreate, self.dialogCreate.data).then(function () {
                            self.action = 'list';
                            self.$message.success('成功');
                            self.loadData();
                        })
                    } else {
                        return false;
                    }
                });
            },
            openUpdateDialog: function (scope) {
                this.dialogUpdate.data = _.cloneDeep(scope.row);
                this.action = 'update';
            },
            doUpdate: function () {
                let self = this;
                self.dialogUpdate.data.content = self.editor.update.getContent();
                axios.post(self.apiUpdate, [self.dialogUpdate.data]).then(function () {
                    self.action = 'list';
                    self.$message.success('成功');
                    self.loadData();
                });
            },
            switchDisplay: function (row) {
                let self = this;
                axios.post(self.apiUpdate, [{id: row.id, display: row.display}]).then(function () {
                    self.loadData();
                });
            },
            doDelete: function (scope) {
                let self = this;
                this.$confirm('是否删除？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'error'
                }).then(() => {
                    axios.post(self.apiDelete, {id: scope.row.id}).then(function () {
                        self.$message.success('成功');
                        self.loadData();
                    });
                }).catch(() => {
                });
            },
            preview: function (scope) {
                window.open(scope.row.url);
            }
        }
    }
</script>

<style scoped>
    .search > .el-input {
        width: 195px;
    }

    .el-dialog .el-select {
        width: 100%;
    }
</style>