<template>
    <div>
        <el-row class="search">
            <el-select v-model="search.type" placeholder="文章类型">
                <el-option v-for="item in articleTypeList" :key="item.id" :value="item.id"
                           :label="item.name"></el-option>
            </el-select>
            <el-input v-model="search.title" placeholder="标题"></el-input>

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
                <el-table-column prop="article_type" label="文章类型" min-width="100"></el-table-column>
                <el-table-column prop="title" label="标题" min-width="200"></el-table-column>
                <el-table-column prop="remark" label="备注" min-width="160"></el-table-column>
                <el-table-column label="显示" min-width="100">
                    <template slot-scope="scope">
                        <el-switch v-model="scope.row.display" :active-value="1" :inactive-value="0"
                                   @change="switchDisplay(scope.row)">
                        </el-switch>
                    </template>
                </el-table-column>
                <el-table-column prop="created_at" label="创建时间" min-width="160"></el-table-column>
                <el-table-column label="操作" min-width="400">
                    <template slot-scope="scope">
                        <el-button type="primary" class="btn-copy" :data-clipboard-text="scope.row.url">复制链接
                        </el-button>
                        <el-button type="success" @click="preview(scope.row)">预览</el-button>
                        <el-button type="warning" @click="doUpdate(scope.row)">修改</el-button>
                        <el-button type="danger" @click="doDelete(scope.row)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-row>
    </div>
</template>

<script>
    import Clipboard from 'clipboard';

    export default {
        name: "index",
        data: function () {
            return {
                apiList: api.articleList,
                apiUpdate: api.articleUpdate,
                apiDelete: api.articleDelete,
                dataList: {meta: {}},
                search: {},
            }
        },
        computed: {
            articleTypeList: function () {
                return this.$store.state.taxonomy.article_type
            }
        },
        created: function () {
            this.search = this.$route.query;
            this.doSearch();
        },
        mounted: function () {
            let self = this;
            let clipboard = new Clipboard('.btn-copy');
            clipboard.on('success', function (e) {
                e.clearSelection();
                self.$message.success('复制成功');
            });
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
                this.$router.push({name: 'articleCreate'});
            },
            doUpdate: function (row) {
                this.$router.push({name: 'articleUpdate', params: {id: row.id}});
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
            switchDisplay: function (row) {
                let self = this;
                axios.post(self.apiUpdate, [{id: row.id, display: row.display}]).then(function () {
                    self.loadData();
                });
            },
            preview: function (row) {
                window.open(row.url);
            }
        }
    }
</script>

<style scoped>
    .search > .el-input {
        width: 195px;
    }
</style>