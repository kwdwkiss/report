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

            <el-pagination layout="prev, pager, next"
                           :total="dataList.meta.total"
                           :page-size="dataList.meta.per_page"
                           @current-change="paginate"></el-pagination>
        </el-row>

        <el-row>
            <el-table :data="dataList.data" stripe @sort-change="sortChange">
                <el-table-column prop="id" label="ID" min-width="70"></el-table-column>
                <el-table-column prop="_user.mobile" label="用户" min-width="110"></el-table-column>
                <el-table-column prop="_product.title" label="产品" min-width="110"></el-table-column>
                <el-table-column prop="quantity" label="数量" min-width="110"></el-table-column>
                <el-table-column prop="amount" label="总积分" min-width="110"></el-table-column>
                <el-table-column prop="pay_status_label" label="支付状态" min-width="110"></el-table-column>
                <el-table-column prop="pay_at" label="支付时间" min-width="170"></el-table-column>
                <el-table-column prop="created_at" label="创建时间" min-width="170"></el-table-column>
                <el-table-column label="操作" min-width="300">
                    <template slot-scope="scope">
                        <el-button type="primary" @click="doDetail(scope.row)">详情</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-row>

        <div class="modal fade detail-dialog">
            <div class="modal-dialog" style="width: auto">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            &times;
                        </button>
                        <h4 class="modal-title">表格详情</h4>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr v-for="(row,index) in table" v-if="index===0">
                                    <th v-for="col in row">{{col}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(row,index) in table" v-if="index!==0">
                                    <td v-for="col in row">{{col}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <button class="btn btn-primary" @click="doDownLoad">下载</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "index",
        data: function () {
            return {
                apiList: api.agentProductBillIndex,
                dataList: {meta: {}},
                search: {order_query: {}},
                item: {},
                table: [],
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
                this.search = {order_query: {}};
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
            doDetail: function (item) {
                this.item = item;
                this.table = JSON.parse(item.body);
                $('.detail-dialog').modal('show');
            },
            doDownLoad: function () {

            }
        }
    }
</script>

<style scoped>
    .search > .el-input {
        width: 195px;
    }
</style>