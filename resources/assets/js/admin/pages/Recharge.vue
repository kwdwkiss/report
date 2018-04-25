<template>
    <div>
        <div v-show="action=='list'">
            <el-row class="search">
                <el-input v-model="search.mobile" placeholder="用户手机号"></el-input>
                <el-input v-model="search.pay_no" placeholder="外部订单号"></el-input>
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
                <el-button type="success" @click="openCreateDialog">人工充值</el-button>

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
                    <el-table-column prop="_user.mobile" label="用户" min-width="110"></el-table-column>
                    <el-table-column prop="bill_no" label="系统订单号" min-width="150"></el-table-column>
                    <el-table-column prop="pay_type_label" label="支付类型" min-width="80"></el-table-column>
                    <el-table-column prop="pay_no" label="外部订单号" min-width="280"></el-table-column>
                    <el-table-column prop="money" label="金额" min-width="80"></el-table-column>
                    <el-table-column prop="status_label" label="订单状态" min-width="80"></el-table-column>
                    <el-table-column prop="created_at" label="创建时间" min-width="160"></el-table-column>
                    <el-table-column label="操作" min-width="400">
                        <template slot-scope="scope">
                        </template>
                    </el-table-column>
                </el-table>
            </el-row>
        </div>
        <div v-show="action=='create'">
            <div class="panel">
                <div class="panel-heading">人工充值</div>
                <div class="panel-body">
                    <el-form ref="createForm" :model="dialogCreate.data" :rules="rules">
                        <el-form-item prop="mobile" label="用户手机号" labelWidth="100px">
                            <el-input v-model="dialogCreate.data.mobile"></el-input>
                        </el-form-item>
                        <el-form-item prop="pay_no" label="支付类型" labelWidth="100px">
                            <el-select v-model="dialogCreate.data.pay_type">
                                <el-option :key="1" :value="1" label="支付宝"></el-option>
                                <el-option :key="2" :value="2" label="微信"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item prop="pay_no" label="外部订单号" labelWidth="100px">
                            <el-input v-model="dialogCreate.data.pay_no"></el-input>
                        </el-form-item>
                        <el-form-item prop="money" label="金额" labelWidth="100px">
                            <el-input v-model="dialogCreate.data.money"></el-input>
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
                        <el-form-item prop="type" label="文章类型" labelWidth="100px">
                        </el-form-item>
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
                action: 'list',
                apiList: api.rechargeList,
                apiCreate: api.rechargeCreate,
                apiUpdate: '',
                apiDelete: '',
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
        created: function () {
            this.loadData();
        },
        methods: {
            createEditorReady: function (ue) {
                this.editor.create = ue;
            },
            updateEditorReady: function (ue) {
                this.editordialogCreate.update = ue;
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
                this.dialogCreate.data = {pay_type: 1, mobile: '', pay_no: '', money: ''};
                this.action = 'create';
            },
            doCreate: function () {
                let self = this;
                self.$refs.createForm.validate((valid) => {
                    if (valid) {
                        axios.post(self.apiCreate, [self.dialogCreate.data]).then(function () {
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