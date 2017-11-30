<template>
    <div>
        <el-row class="search">
            <el-select v-model="search.type" placeholder="账号类型">
                <el-option v-for="item in accountTypeList" :key="item.id" :value="item.id"
                           :label="item.name"></el-option>
            </el-select>
            <el-input v-model="search.name" placeholder="举报账号"></el-input>
            <el-select v-model="search.status" placeholder="账号状态">
                <el-option v-for="item in accountStatusList" :key="item.id" :value="item.id"
                           :label="item.name"></el-option>
            </el-select>

            <el-button type="primary" @click="loadData">搜索</el-button>
            <el-button type="warning" @click="reset">重置</el-button>

            <el-pagination layout="prev, pager, next"
                           :total="dataList.meta.total"
                           :page-size="dataList.meta.per_page"
                           @current-change="paginate"></el-pagination>
        </el-row>

        <el-row>
            <el-table :data="dataList.data" stripe>
                <el-table-column prop="account_type" label="账号类型" min-width="100"></el-table-column>
                <el-table-column prop="name" label="账号" min-width="150"></el-table-column>
                <el-table-column prop="account_status" label="账号状态" min-width="150"></el-table-column>
                <el-table-column prop="report_count" label="举报次数" min-width="100"></el-table-column>
                <el-table-column prop="auth_cash" label="合作金额" min-width="100"></el-table-column>
                <el-table-column prop="address" label="常用地址" min-width="150"></el-table-column>
                <el-table-column prop="remark" label="备注" min-width="200"></el-table-column>
                <el-table-column prop="created_at" label="创建时间" min-width="180"></el-table-column>
                <el-table-column prop="updated_at" label="更新时间" min-width="180"></el-table-column>
                <el-table-column label="操作" min-width="200">
                    <template slot-scope="scope">
                        <el-button type="warning" @click="openUpdateDialog(scope)">修改</el-button>
                        <el-button type="danger" @click="doDelete(scope)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-row>

        <el-dialog title="创建" :visible.sync="dialogCreate.display">
            <el-form ref="createForm" :model="dialogCreate.data" :rules="rules">
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogCreate.display = false">取 消</el-button>
                <el-button type="primary" @click="doCreate">确 定</el-button>
            </div>
        </el-dialog>

        <el-dialog title="更新" :visible.sync="dialogUpdate.display">
            <el-form>
                <el-form-item prop="type" label="账号类型" labelWidth="100px">
                    <span>{{dialogUpdate.data.account_type}}</span>
                </el-form-item>
                <el-form-item prop="name" label="账号" labelWidth="100px">
                    <span>{{dialogUpdate.data.name}}</span>
                </el-form-item>
                <el-form-item prop="status" label="账号状态" labelWidth="100px">
                    <el-select v-model="dialogUpdate.data.status">
                        <el-option v-for="item in accountStatusList" :key="item.id" :value="item.id"
                                   :label="item.name"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item prop="report_count" label="举报次数" labelWidth="100px">
                    <span>{{dialogUpdate.data.report_count}}</span>
                </el-form-item>
                <el-form-item prop="auth_cash" label="合作金额" labelWidth="100px">
                    <el-input v-model="dialogUpdate.data.auth_cash"></el-input>
                </el-form-item>
                <el-form-item prop="address" label="常用地址" labelWidth="100px">
                    <el-input v-model="dialogUpdate.data.address"></el-input>
                </el-form-item>
                <el-form-item prop="remark" label="备注" labelWidth="100px">
                    <el-input v-model="dialogUpdate.data.remark"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogUpdate.display = false">取 消</el-button>
                <el-button type="primary" @click="doUpdate">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                accountTypeList: {},
                accountStatusList: {},
                apiList: api.accountList,
                apiCreate: api.accountCreate,
                apiUpdate: api.accountUpdate,
                apiDelete: api.accountDelete,
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
            this.loadAccountType();
            this.loadAccountStatus();
            this.loadData();
        },
        methods: {
            loadAccountType: function () {
                let self = this;
                axios.get(api.taxonomyAccountTypeList).then(function (res) {
                    self.accountTypeList = res.data.data;
                });
            },
            loadAccountStatus: function () {
                let self = this;
                axios.get(api.taxonomyAccountStatusList).then(function (res) {
                    self.accountStatusList = res.data.data;
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
            openCreateDialog: function () {
                this.dialogCreate.data = {pid: this.search.pid, order: 0, display: 1, remark: ''};
                this.dialogCreate.display = true
            },
            doCreate: function () {
                let self = this;
                self.$refs.createForm.validate((valid) => {
                    if (valid) {
                        axios.post(self.apiCreate, self.dialogCreate.data).then(function () {
                            self.dialogCreate.display = false;
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
                this.dialogUpdate.display = true;
            },
            doUpdate: function () {
                let self = this;
                axios.post(self.apiUpdate, [self.dialogUpdate.data]).then(function () {
                    self.dialogUpdate.display = false;
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
            openDeleteDialog: function (scope) {
                this.dialogDelete.data = Object.assign({}, scope.row);
                this.dialogDelete.display = true;
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
    .search > .el-input {
        width: 195px;
    }

    .el-dialog .el-select {
        width: 100%;
    }
</style>