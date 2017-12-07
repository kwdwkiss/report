<template>
    <div>
        <div v-show="action=='list'">
            <el-row>
                <el-select v-model="search.pid" @change="searchPid">
                    <el-option v-for="item in dataListL0.data" :key="item.id" :value="item.id"
                               :label="item.name"></el-option>
                </el-select>
                <el-button type="success" @click="openCreateDialog">添加</el-button>
                <el-button type="warning" @click="doMultiUpdate">批量更新</el-button>

                <el-pagination layout="prev, pager, next"
                               :total="dataList.meta.total"
                               :page-size="dataList.meta.per_page"
                               @current-change="paginate"></el-pagination>
            </el-row>
            <el-row>
                <el-table :data="dataList.data" stripe>
                    <el-table-column prop="name" label="名称" min-width="150"></el-table-column>
                    <el-table-column label="顺序" min-width="100">
                        <template slot-scope="scope">
                            <el-input v-model="scope.row.order"></el-input>
                        </template>
                    </el-table-column>
                    <el-table-column label="显示" min-width="100">
                        <template slot-scope="scope">
                            <el-switch v-model="scope.row.display" :active-value="1" :inactive-value="0"
                                       @change="switchDisplay(scope.row)">
                            </el-switch>
                        </template>
                    </el-table-column>
                    <el-table-column prop="remark" label="备注" min-width="200"></el-table-column>
                    <el-table-column label="操作" min-width="200">
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
                <div class="panel-heading">创建分类</div>
                <div class="panel-body">
                    <el-form ref="createForm" :model="dialogCreate.data" :rules="rules">
                        <el-form-item prop="name" label="名称" labelWidth="100px">
                            <el-input v-model="dialogCreate.data.name"></el-input>
                        </el-form-item>
                        <el-form-item prop="order" label="顺序" labelWidth="100px">
                            <el-input v-model="dialogCreate.data.order"></el-input>
                        </el-form-item>
                        <el-form-item prop="display" label="显示" labelWidth="100px">
                            <el-switch v-model="dialogCreate.data.display" :active-value="1"
                                       :inactive-value="0"></el-switch>
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
                <div class="panel-heading">更新分类</div>
                <div class="panel-body">
                    <el-form :model="dialogUpdate.data">
                        <el-form-item prop="name" label="名称" labelWidth="100px">
                            <el-input v-model="dialogUpdate.data.name"></el-input>
                        </el-form-item>
                        <el-form-item prop="order" label="顺序" labelWidth="100px">
                            <el-input v-model="dialogUpdate.data.order"></el-input>
                        </el-form-item>
                        <el-form-item prop="display" label="显示" labelWidth="100px">
                            <el-switch v-model="dialogUpdate.data.display" :active-value="1"
                                       :inactive-value="0"></el-switch>
                        </el-form-item>
                        <el-form-item prop="remark" label="备注" labelWidth="100px">
                            <el-input v-model="dialogUpdate.data.remark"></el-input>
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
                apiList: api.taxonomyList,
                apiCreate: api.taxonomyCreate,
                apiUpdate: api.taxonomyUpdate,
                apiDelete: api.taxonomyDelete,
                dataListL0: {},
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
                rules: {}
            }
        },
        created: function () {
            this.loadDataL0();
        },
        methods: {
            loadDataL0: function () {
                let self = this;
                axios.get(self.apiList, {params: {pid: 0}}).then(function (res) {
                    self.dataListL0 = res.data;
                    if (self.dataListL0.data.length > 0) {
                        self.search.pid = self.dataListL0.data[0].id;
                        self.loadData();
                    }
                });
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
            searchPid: function (value) {
                this.search.pid = value;
                this.loadData();
            },
            openCreateDialog: function () {
                this.dialogCreate.data = {pid: this.search.pid, order: 0, display: 1, remark: ''};
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
                this.dialogUpdate.data = Object.assign({}, scope.row);
                this.action = 'update';
            },
            doUpdate: function () {
                let self = this;
                axios.post(self.apiUpdate, [self.dialogUpdate.data]).then(function () {
                    self.action = 'list';
                    self.$message.success('成功');
                    self.loadData();
                });
            },
            doMultiUpdate: function () {
                let self = this;
                axios.post(self.apiUpdate, self.dataList.data).then(function () {
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
            }
        }
    }
</script>

<style scoped>
    .el-dialog .el-select {
        width: 100%;
    }
</style>