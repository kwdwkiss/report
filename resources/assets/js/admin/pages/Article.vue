<template>
    <div>
        <div v-show="action=='list'">
            <el-row class="search">
                <el-select v-model="search.type" placeholder="文章类型">
                    <el-option v-for="item in articleTypeList" :key="item.id" :value="item.id"
                               :label="item.name"></el-option>
                </el-select>
                <el-input v-model="search.title" placeholder="标题"></el-input>

                <el-button type="primary" @click="loadData">搜索</el-button>
                <el-button type="warning" @click="reset">重置</el-button>
                <el-button type="success" @click="openCreateDialog">添加</el-button>

                <el-pagination layout="prev, pager, next"
                               :total="dataList.meta.total"
                               :page-size="dataList.meta.per_page"
                               @current-change="paginate"></el-pagination>
            </el-row>

            <el-row>
                <el-table :data="dataList.data" stripe>
                    <el-table-column prop="id" label="ID" min-width="50"></el-table-column>
                    <!--<el-table-column label="URL" min-width="50">-->
                    <!--<template slot-scope="scope">-->
                    <!--<a target="_blank" :href="scope.row.url">查看</a>-->
                    <!--</template>-->
                    <!--</el-table-column>-->
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
                            <el-button type="success" @click="preview(scope)">预览</el-button>
                            <el-button type="warning" @click="openUpdateDialog(scope)">修改</el-button>
                            <el-button type="danger" @click="doDelete(scope)">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </el-row>
        </div>
        <div v-show="action=='create'">
            <div class="panel">
                <div class="panel-heading">创建文章</div>
                <div class="panel-body">
                    <el-form ref="createForm" :model="dialogCreate.data" :rules="rules">
                        <el-form-item prop="type" label="文章类型" labelWidth="100px">
                            <el-select v-model="dialogCreate.data.type">
                                <el-option v-for="item in articleTypeList" :key="item.id" :value="item.id"
                                           :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item prop="title" label="标题" labelWidth="100px">
                            <el-input v-model="dialogCreate.data.title"></el-input>
                        </el-form-item>
                        <el-form-item prop="remark" label="备注" labelWidth="100px">
                            <el-input v-model="dialogCreate.data.remark"></el-input>
                        </el-form-item>
                        <el-form-item class="editor-form-item" prop="content" label="内容" labelWidth="100px">
                            <VueUEditor v-if="action=='create'" ueditorPath="/ueditor/"
                                        @ready="createEditorReady"
                                        style="line-height: 20px">
                            </VueUEditor>
                        </el-form-item>
                        <el-form-item labelWidth="100px">
                            <el-button type="primary" @click="action='list'">返回</el-button>
                            <el-button type="success" @click="doCreate">提交</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </div>
        <div v-show="action=='update'">
            <div class="panel">
                <div class="panel-heading">编辑文章</div>
                <div class="panel-body">
                    <el-form ref="createForm" :model="dialogUpdate.data" :rules="rules">
                        <el-form-item prop="type" label="文章类型" labelWidth="100px">
                            <el-select v-model="dialogUpdate.data.type">
                                <el-option v-for="item in articleTypeList" :key="item.id" :value="item.id"
                                           :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item prop="title" label="标题" labelWidth="100px">
                            <el-input v-model="dialogUpdate.data.title"></el-input>
                        </el-form-item>
                        <el-form-item prop="remark" label="备注" labelWidth="100px">
                            <el-input v-model="dialogUpdate.data.remark"></el-input>
                        </el-form-item>
                        <el-form-item prop="content" label="内容" labelWidth="100px">
                            <VueUEditor v-if="action=='update'" ueditorPath="/ueditor/"
                                        @ready="updateEditorReady"
                                        style="line-height: 20px">
                            </VueUEditor>
                        </el-form-item>
                        <el-form-item labelWidth="100px">
                            <el-button type="primary" @click="action='list'">返回</el-button>
                            <el-button type="success" @click="doUpdate">提交</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import VueUEditor from 'vue-ueditor';
    import Clipboard from 'clipboard';

    export default {
        components: {VueUEditor},
        data: function () {
            return {
                action: 'list',
                apiList: api.articleList,
                apiCreate: api.articleCreate,
                apiUpdate: api.articleUpdate,
                apiDelete: api.articleDelete,
                dataList: {
                    meta: {},
                },
                search: {},
                dialogCreate: {
                    display: false,
                    data: {}
                },
                dialogUpdate: {
                    display: false,
                    data: {}
                },
                rules: {},
                editor: {}
            }
        },
        computed: {
            articleTypeList: function () {
                return this.$store.state.taxonomy.article_type
            }
        },
        created: function () {
            this.loadData();
        },
        mounted: function () {
            let clipboard = new Clipboard('.btn-copy');
            clipboard.on('success', function (e) {
                e.clearSelection();
            });
        },
        methods: {
            createEditorReady: function (ue) {
                this.editor.create = ue;
            },
            updateEditorReady: function (ue) {
                this.editor.update = ue;
                this.editor.update.setContent(this.dialogUpdate.data.content);
            },
            loadData: function () {
                let self = this;
                axios.get(self.apiList, {params: self.search}).then(function (res) {
                    self.dataList = res.data;
                });
            },
            paginate: function (page) {
                this.search.page = page;
                this.loadData();
            },
            reset: function () {
                this.search = {};
                this.loadData();
            },
            openCreateDialog: function () {
                let type = this.articleTypeList.length && this.articleTypeList[0].id;
                this.dialogCreate.data = {type: type, title: '', remark: '', content: ''};
                this.action = 'create';
            },
            doCreate: function () {
                let self = this;
                self.$refs.createForm.validate((valid) => {
                    if (valid) {
                        self.dialogCreate.data.content = self.editor.create.getContent();
                        axios.post(self.apiCreate, [self.dialogCreate.data]).then(function () {
                            self.action = 'list';
                            self.$message.success('成功');
                            self.loadData();
                        })
                    } else {
                        return false;
                    }
                });
            },
            openUpdateDialog: function (scope) {
                this.dialogUpdate.data = _.cloneDeep(scope.row);
                this.action = 'update';
            },
            doUpdate: function () {
                let self = this;
                self.dialogUpdate.data.content = self.editor.update.getContent();
                axios.post(self.apiUpdate, [self.dialogUpdate.data]).then(function () {
                    self.action = 'list';
                    self.$message.success('成功');
                    self.loadData();
                });
            },
            switchDisplay: function (row) {
                let self = this;
                axios.post(self.apiUpdate, [{id: row.id, display: row.display}]).then(function () {
                    self.loadData();
                });
            },
            doDelete: function (scope) {
                let self = this;
                this.$confirm('是否删除？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'error'
                }).then(() => {
                    axios.post(self.apiDelete, {id: scope.row.id}).then(function () {
                        self.$message.success('成功');
                        self.loadData();
                    });
                }).catch(() => {
                });
            },
            preview: function (scope) {
                window.open(scope.row.url);
            }
        }
    }
</script>

<style scoped>
    .search > .el-input {
        width: 195px;
    }

    .el-dialog .el-select {
        width: 100%;
    }
</style>