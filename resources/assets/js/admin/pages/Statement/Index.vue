<template>
    <div>
        <el-row class="search">
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
                <el-table-column prop="date" label="日期" min-width="100"></el-table-column>
                <el-table-column prop="account_search" label="查询次数" min-width="100"></el-table-column>
                <el-table-column prop="account_search_user" label="查询用户数" min-width="100"></el-table-column>
                <el-table-column prop="account_report" label="举报次数" min-width="100"></el-table-column>
                <el-table-column prop="recharge_count" label="充值笔数" min-width="100"></el-table-column>
                <el-table-column prop="recharge_money" label="充值金额" min-width="100"></el-table-column>
                <el-table-column prop="recharge_first_user" label="首充用户" min-width="100"></el-table-column>
                <el-table-column prop="user_register" label="注册用户" min-width="100"></el-table-column>
                <el-table-column prop="user_register_inviter" label="邀请注册" min-width="80"></el-table-column>
                <el-table-column prop="recharge_referer_count" label="提成笔数" min-width="100"></el-table-column>
                <el-table-column prop="recharge_referer_amount" label="提成积分" min-width="100"></el-table-column>
                <el-table-column label="操作" min-width="400">
                    <template slot-scope="scope">
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
                apiList: api.adminStatementList,
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
        computed: {
            location: function () {

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
        }
    }
</script>

<style scoped>
    .search > .el-input {
        width: 195px;
    }
</style>