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

            <el-button type="primary" @click="doSearch">搜索</el-button>
            <el-button type="warning" @click="reset">重置</el-button>
            <el-button type="success" @click="doCreate">添加</el-button>

            <el-pagination layout="prev, pager, next"
                           :total="dataList.meta.total"
                           :page-size="dataList.meta.per_page"
                           @current-change="paginate"></el-pagination>
        </el-row>

        <el-row>
            <el-table :data="dataList.data" stripe>
                <el-table-column prop="type_label" label="账号类型" min-width="100"></el-table-column>
                <el-table-column prop="name" label="账号" min-width="150"></el-table-column>
                <el-table-column prop="status_label" label="账号状态" min-width="150"></el-table-column>
                <el-table-column prop="report_count" label="举报次数" min-width="100"></el-table-column>
                <el-table-column prop="auth_cash" label="合作金额" min-width="100"></el-table-column>
                <el-table-column prop="address" label="常用地址" min-width="150"></el-table-column>
                <el-table-column prop="remark" label="备注" min-width="200"></el-table-column>
                <el-table-column prop="created_at" label="创建时间" min-width="180"></el-table-column>
                <el-table-column prop="updated_at" label="更新时间" min-width="180"></el-table-column>
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
                apiList: api.accountList,
                apiUpdate: api.accountUpdate,
                apiDelete: api.accountDelete,
                routeNameCreate: 'account_create',
                routeNameUpdate: 'account_update',
                dataList: {meta: {}},
                search: {},
            }
        },
        computed: {
            accountTypeList: function () {
                return this.$store.state.taxonomy.account_type;
            },
            accountStatusList: function () {
                return this.$store.state.taxonomy.account_status;
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