<template>
    <div>
        <el-row class="search">
            <el-select v-model="search.report_type" placeholder="举报类型">
                <el-option v-for="item in reportTypeList" :key="item.id" :value="item.id"
                           :label="item.name"></el-option>
            </el-select>
            <el-select v-model="search.account_type" placeholder="账号类型">
                <el-option v-for="item in accountTypeList" :key="item.id" :value="item.id"
                           :label="item.name"></el-option>
            </el-select>
            <el-input v-model="search.account_name" placeholder="举报账号"></el-input>
            <el-date-picker
                    v-model="search.created_at"
                    type="daterange"
                    align="right"
                    unlink-panels
                    range-separator="-"
                    start-placeholder="开始日期"
                    end-placeholder="结束日期"
                    value-format="yyyy-MM-dd"
                    :picker-options="datePickerOptions">
            </el-date-picker>

            <el-button type="primary" @click="loadData">搜索</el-button>
            <el-button type="warning" @click="reset">重置</el-button>

            <el-pagination layout="prev, pager, next"
                           :total="dataList.meta.total"
                           :page-size="dataList.meta.per_page"
                           @current-change="paginate"></el-pagination>
        </el-row>

        <el-row>
            <el-table :data="dataList.data" stripe>
                <el-table-column prop="account_type_label" label="账号类型" min-width="100"></el-table-column>
                <el-table-column prop="account_name" label="账号" min-width="150"></el-table-column>
                <el-table-column prop="type_label" label="举报类型" min-width="100"></el-table-column>
                <el-table-column prop="ip" label="IP" min-width="150"></el-table-column>
                <el-table-column prop="created_at" label="举报时间" min-width="180"></el-table-column>
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
                <el-form-item prop="account_type" label="账号类型" labelWidth="100px">
                    <span>{{dialogUpdate.data.account_type}}</span>
                </el-form-item>
                <el-form-item prop="account_name" label="账号" labelWidth="100px">
                    <span>{{dialogUpdate.data.account_name}}</span>
                </el-form-item>
                <el-form-item prop="display" label="举报类型" labelWidth="100px">
                    <el-select v-model="dialogUpdate.data.type">
                        <el-option v-for="item in reportTypeList" :key="item.id" :value="item.id"
                                   :label="item.name"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item prop="ip" label="IP" labelWidth="100px">
                    <span>{{dialogUpdate.data.ip}}</span>
                </el-form-item>
                <el-form-item prop="created_at" label="举报时间" labelWidth="100px">
                    <span>{{dialogUpdate.data.created_at}}</span>
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
                datePickerOptions: {
                    shortcuts: [{
                        text: '最近一周',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '最近一个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                            picker.$emit('pick', [start, end]);
                        }
                    }, {
                        text: '最近三个月',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                            picker.$emit('pick', [start, end]);
                        }
                    }]
                },
                accountTypeList: store.state.taxonomy.account_type,
                reportTypeList: store.state.taxonomy.report_type,
                apiList: api.accountReportList,
                apiCreate: api.accountReportCreate,
                apiUpdate: api.accountReportUpdate,
                apiDelete: api.accountReportDelete,
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
            this.loadData();
        },
        methods: {
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