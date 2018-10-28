<template>
    <div>
        <el-row class="search">
            <el-input v-model="search.mobile" placeholder="用户手机号"></el-input>
            <el-select v-model="search.status" placeholder="审核状态">
                <el-option value="" label="全部"></el-option>
                <el-option :value="0" label="待审核"></el-option>
                <el-option :value="1" label="已审核"></el-option>
                <el-option :value="2" label="已拒绝"></el-option>
            </el-select>
            <!--<el-input v-model="search.bill_no" placeholder="系统订单号"></el-input>-->
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
            <el-table :data="dataList.data" stripe @sort-change="sortChange">
                <el-table-column prop="id" label="ID" min-width="80"></el-table-column>
                <el-table-column prop="_user.mobile" label="用户" min-width="110"></el-table-column>
                <el-table-column prop="_user._profile.amount" label="用户积分" min-width="110"></el-table-column>
                <el-table-column prop="_product.title" label="内容" min-width="110"></el-table-column>
                <el-table-column prop="_product_bill.quantity" label="时长" min-width="110">
                </el-table-column>
                <el-table-column prop="_product_bill.amount" label="消耗积分" min-width="110">
                </el-table-column>
                <el-table-column prop="status_label" label="状态" min-width="110"></el-table-column>
                <el-table-column prop="_admin.name" label="审核人" min-width="110"></el-table-column>
                <el-table-column prop="check_at" label="审核时间" min-width="170"></el-table-column>
                <el-table-column prop="created_at" label="创建时间" min-width="170"></el-table-column>
                <el-table-column label="操作" min-width="400">
                    <template slot-scope="scope">
                        <el-button v-if="scope.row.status===0" type="primary" @click="check(scope.row)">审核
                        </el-button>
                        <el-button v-if="scope.row.status===0" type="danger" @click="reject(scope.row)">拒绝
                        </el-button>
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
                apiList: api.adminUserAuthBillIndex,
                dataList: {meta: {}},
                search: {order_query: {}},
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
            sortChange: function (item) {
                this.search.order_query = {field: item.prop, order: item.order};
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
            check: function (item) {
                let self = this;
                self.$confirm('是否通过审核？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'primary'
                }).then(() => {
                    self.doCheck(item.id);
                }).catch(() => {
                });
            },
            doCheck: function (id) {
                let self = this;
                axios.post(api.adminUserAuthBillCheck, {id: id}).then(function (res) {
                    self.$message.success(res.data.message);
                    self.loadData();
                });
            },
            reject: function (item) {
                let self = this;
                self.$confirm('是否拒绝审核？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    self.doReject(item.id);
                }).catch(() => {
                });
            },
            doReject: function (id) {
                let self = this;
                axios.post(api.adminUserAuthBillReject, {id: id}).then(function (res) {
                    self.$message.success(res.data.message);
                    self.loadData();
                });
            }
        }
    }
</script>

<style scoped>
    .search > .el-input {
        width: 195px;
    }
</style>