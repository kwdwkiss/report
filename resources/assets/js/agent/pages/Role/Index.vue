<template>
    <div>
        <el-row class="search">
            <!--<el-select v-model="search.type" placeholder="文章类型">-->
            <!--<el-option v-for="item in articleTypeList" :key="item.id" :value="item.id"-->
            <!--:label="item.name"></el-option>-->
            <!--</el-select>-->
            <el-input v-model="search.name" placeholder="名称"></el-input>

            <el-button type="primary" @click="doSearch">搜索</el-button>
            <el-button type="warning" @click="reset">重置</el-button>
            <el-button type="success" @click="doPermissionRefresh">权限刷新</el-button>

            <el-pagination layout="prev, pager, next"
                           :total="dataList.meta.total"
                           :page-size="dataList.meta.per_page"
                           @current-change="paginate"></el-pagination>
        </el-row>

        <el-row>
            <el-table :data="dataList.data" stripe>
                <el-table-column prop="id" label="ID" min-width="60"></el-table-column>
                <!--<el-table-column prop="name" label="名称" min-width="100"></el-table-column>-->
                <el-table-column prop="guard_title" label="平台" min-width="100"></el-table-column>
                <el-table-column prop="title" label="标题" min-width="100"></el-table-column>
                <el-table-column label="权限" min-width="800">
                    <template slot-scope="scope">
                        <template v-for="perm in scope.row.permissions">{{perm.title}}，</template>
                    </template>
                </el-table-column>
                <el-table-column label="操作" min-width="200">
                    <template slot-scope="scope">
                        <el-button type="warning" @click="doUpdate(scope.row)">修改</el-button>
                        <!--<el-button type="danger" @click="doDelete(scope.row)">删除</el-button>-->
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
                apiList: api.agentRoleIndex,
                apiUpdate: api.agentRoleUpdate,
                apiDelete: api.agentRoleDelete,
                routeNameCreate: 'roleCreate',
                routeNameUpdate: 'roleUpdate',
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
            doPermissionRefresh: function () {
                let self = this;
                axios.post(api.agentPermissionRefresh).then(function () {
                    self.$message.success('成功');
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