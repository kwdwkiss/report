<template>
    <div>
        <el-row class="search">
            <el-input v-model="search.mobile" placeholder="用户手机号"></el-input>
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
            <el-button type="success" @click="doCreate">添加</el-button>

            <el-pagination layout="prev, pager, next"
                           :total="dataList.meta.total"
                           :page-size="dataList.meta.per_page"
                           @current-change="paginate"></el-pagination>
        </el-row>

        <el-row>
            <el-table :data="dataList.data" stripe @sort-change="sortChange">
                <el-table-column prop="id" label="ID" min-width="80"></el-table-column>
                <el-table-column prop="_user.mobile" label="用户" min-width="110"></el-table-column>
                <el-table-column prop="status_label" label="状态" min-width="110"></el-table-column>
                <el-table-column prop="amount" label="支付积分" min-width="110"></el-table-column>
                <el-table-column prop="type_label" label="认证类型" min-width="110"></el-table-column>
                <el-table-column prop="duration_label" label="时长" min-width="110"></el-table-column>
                <el-table-column prop="pay_at" label="支付时间" min-width="170"></el-table-column>
                <!--<el-table-column prop="start_at" label="开始时间" min-width="170"></el-table-column>-->
                <!--<el-table-column prop="end_at" label="结束时间" min-width="170"></el-table-column>-->
                <el-table-column prop="created_at" label="创建时间" min-width="170"></el-table-column>
                <el-table-column label="操作" min-width="400">
                    <template slot-scope="scope">
                        <el-button v-if="scope.row.status===0" type="danger" @click="deleteConfirm(scope.row)">删除
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
                apiDelete: api.adminUserAuthBillDelete,
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
            doCreate: function () {
                this.$router.push({name: 'userAuthBillCreate'});
            },
            deleteConfirm: function (item) {
                let self = this;
                self.$confirm('是否删除？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'error'
                }).then(() => {
                    self.doDelete(item.id);
                }).catch(() => {
                });
            },
            doDelete: function (id) {
                let self = this;
                axios.post(self.apiDelete, {id: id}).then(function (res) {
                    self.$message.success('删除成功');
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