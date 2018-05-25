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

            <el-button type="primary" @click="doSearch">搜索</el-button>
            <el-button type="warning" @click="reset">重置</el-button>

            <el-pagination layout="prev, pager, next"
                           :total="dataList.meta.total"
                           :page-size="dataList.meta.per_page"
                           @current-change="paginate"></el-pagination>
        </el-row>

        <el-row>
            <el-table :data="dataList.data" stripe>
                <el-table-column prop="account_type_label" label="账号类型" min-width="80"></el-table-column>
                <el-table-column prop="account_name" label="账号" min-width="150"></el-table-column>
                <el-table-column prop="user_mobile" label="举报者" min-width="110"></el-table-column>
                <el-table-column prop="type_label" label="举报类型" min-width="80"></el-table-column>
                <el-table-column prop="ip" label="IP" min-width="120"></el-table-column>
                <el-table-column prop="created_at" label="举报时间" min-width="180"></el-table-column>
                <el-table-column label="显示" min-width="80">
                    <template slot-scope="scope">
                        <el-switch v-model="scope.row.display" :active-value="1" :inactive-value="0"
                                   @change="doUpdateRow(scope.row)">
                        </el-switch>
                    </template>
                </el-table-column>
                <el-table-column label="图片" min-width="100">
                    <template slot-scope="scope">
                        <a v-if="scope.row.attachment" target="_blank" :href="scope.row.attachment.url">
                            <img :src="scope.row.attachment.url" alt=""
                                 style="max-height: 80px">
                        </a>
                    </template>
                </el-table-column>
                <el-table-column prop="description" label="描述" min-width="200"></el-table-column>
                <el-table-column prop="remark" label="备注" min-width="200"></el-table-column>
                <el-table-column label="操作" min-width="200">
                    <template slot-scope="scope">
                        <el-button type="warning" @click="doUpdate(scope.row)">修改</el-button>
                        <el-button type="danger" @click="doDelete(scope.row)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-row>
    </div>
</template>

<script>
    export default {
        name: "index",
        data: function () {
            return {
                apiList: api.accountReportList,
                apiUpdate: api.accountReportUpdate,
                apiDelete: api.accountReportDelete,
                routeNameCreate: 'account_report_create',
                routeNameUpdate: 'account_report_update',
                dataList: {meta: {}},
                search: {},
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
            }
        },
        computed: {
            accountTypeList: function () {
                return this.$store.state.taxonomy.account_type;
            },
            reportTypeList: function () {
                return this.$store.state.taxonomy.report_type;
            }
        },
        created: function () {
            this.search = this.$route.query;
            this.doSearch();
        },
        watch: {
            '$route'(to, from) {
                this.search = this.$route.query;
                this.doSearch();
            }
        },
        methods: {
            doSearch: function () {
                this.search.page = null;
                this.loadData();
            },
            reset: function () {
                this.search = {};
                this.loadData();
            },
            paginate: function (page) {
                this.search.page = page;
                this.loadData();
            },
            loadData: function () {
                let self = this;
                axios.get(self.apiList, {params: self.search}).then(function (res) {
                    self.dataList = res.data;
                });
            },
            doCreate: function () {
                this.$router.push({name: this.routeNameCreate});
            },
            doUpdate: function (row) {
                this.$router.push({name: this.routeNameUpdate, params: {id: row.id}});
            },
            doDelete: function (row) {
                let self = this;
                this.$confirm('是否删除？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'error'
                }).then(() => {
                    axios.post(self.apiDelete, {id: row.id}).then(function () {
                        self.$message.success('成功');
                        self.loadData();
                    });
                }).catch(() => {
                });
            },
            doUpdateRow: function (row) {
                let self = this;
                axios.post(self.apiUpdate, row).then(function () {
                    self.$message.success('成功');
                    self.loadData();
                });
            },
            //self methods
        }
    }
</script>

<style scoped>
    .search > .el-input {
        width: 195px;
    }

    .panel-body .el-select {
        width: 100%;
    }

    .demo-table-expand {
        font-size: 0;
    }

    .demo-table-expand label {
        width: 90px;
        color: #99a9bf;
    }

    .demo-table-expand .el-form-item {
        margin-right: 0;
        margin-bottom: 0;
        width: 50%;
    }
</style>