<template>
    <div>
        <el-row class="search">
            <el-input v-model="search.name" placeholder="名称"></el-input>

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
                <el-table-column prop="id" label="ID" min-width="100"></el-table-column>
                <el-table-column prop="name" label="名称" min-width="150"></el-table-column>
                <el-table-column prop="roles_label" label="角色" min-width="400"></el-table-column>
                <!--<el-table-column prop="mobile" label="手机" min-width="150"></el-table-column>-->
                <!--<el-table-column prop="email" label="邮箱" min-width="150"></el-table-column>-->
                <el-table-column label="操作" min-width="400">
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
                apiList: api.agentAdminIndex,
                apiUpdate: api.agentAdminUpdate,
                apiDelete: api.agentAdminDelete,
                routeNameCreate: 'adminCreate',
                routeNameUpdate: 'adminUpdate',
                dataList: {meta: {}},
                search: {},
            }
        },
        computed: {
            roles: function () {
                return this.$store.state.roles;
            }
        },
        created: function () {
            this.search = this.$route.query;
            this.doSearch();
        },
        mounted: function () {
            let self = this;
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
        }
    }
</script>

<style scoped>
    .search > .el-input {
        width: 195px;
    }
</style>