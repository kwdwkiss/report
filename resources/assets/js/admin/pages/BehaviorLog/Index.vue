<template>
    <div>
        <el-row class="search">
            <!--<el-input v-model="search.mobile" placeholder="用户手机号"></el-input>-->
            <el-select v-model="search.type" placeholder="行为类型">
                <el-option :value="0" label="无"></el-option>
                <el-option :value="1" label="广告点击"></el-option>
                <el-option :value="2" label="一键EXCEL"></el-option>
                <el-option :value="3" label="管理员登录"></el-option>
                <el-option :value="4" label="用户登录"></el-option>
                <el-option :value="5" label="管理员充值"></el-option>
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
                <!--<el-table-column label="URL" min-width="50">-->
                <!--<template slot-scope="scope">-->
                <!--<a target="_blank" :href="scope.row.url">查看</a>-->
                <!--</template>-->
                <!--</el-table-column>-->
                <el-table-column prop="user_mobile" label="用户" min-width="110"></el-table-column>
                <el-table-column prop="type_label" label="行为类型" min-width="110"></el-table-column>
                <el-table-column prop="content" label="内容" min-width="500"></el-table-column>
                <el-table-column prop="created_at" label="创建时间" min-width="170"></el-table-column>
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
                apiList: api.adminBehaviorLogIndex,
                dataList: {meta: {}},
                search: {order_query: {}, type: 0},
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
        }
    }
</script>

<style scoped>
    .search > .el-input {
        width: 195px;
    }
</style>