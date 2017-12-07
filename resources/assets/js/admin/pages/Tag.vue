<template>
    <div>
        <el-row>
            <el-button type="success" @click="openCreateDialog">添加</el-button>

            <el-pagination layout="prev, pager, next"
                           :total="dataList.meta.total"
                           :page-size="dataList.meta.per_page"
                           @current-change="paginate"></el-pagination>
        </el-row>
        <el-row>
            <el-table :data="dataList.data" stripe>
                <el-table-column prop="name" label="名称" width="200"></el-table-column>
                <el-table-column prop="group_id" label="分组" width="200"></el-table-column>
                <el-table-column label="操作">
                    <template slot-scope="scope">
                        <el-button type="warning" @click="openUpdateDialog(scope)">
                            修改
                        </el-button>
                        <el-button type="danger" @click="doDelete(scope)">
                            删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-row>

        <el-dialog title="创建" :visible.sync="dialogCreate.display">
            <el-form>
                <el-form-item label="名称" labelWidth="100px">
                    <el-input v-model="dialogCreate.data.name"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogCreate.display = false">取 消</el-button>
                <el-button type="primary" @click="doCreate">确 定</el-button>
            </div>
        </el-dialog>

        <el-dialog title="更新" :visible.sync="dialogUpdate.display">
            <el-form>
                <el-form-item label="名称" labelWidth="100px">
                    <el-input v-model="dialogUpdate.data.name"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogUpdate.display = false">取 消</el-button>
                <el-button type="primary" @click="doUpdate">确 定</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                apiList: api.tagList,
                apiCreate: api.tagCreate,
                apiUpdate: api.tagUpdate,
                apiDelete: api.tagDelete,
                dataList: {
                    meta: {},
                    search: {}
                },
                dialogCreate: {
                    display: false,
                    data: {}
                },
                dialogUpdate: {
                    display: false,
                    data: {}
                },
            }
        },
        created: function () {
            this.loadData();
        },
        methods: {
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
            openCreateDialog: function () {
                this.dialogCreate.data = {};
                this.dialogCreate.display = true
            },
            doCreate: function () {
                let self = this;
                axios.post(self.apiCreate, self.dialogCreate.data).then(function () {
                    self.dialogCreate.display = false;
                    self.$message.success('成功');
                    self.loadData();
                })
            },
            openUpdateDialog: function (scope) {
                this.dialogUpdate.data = Object.assign({}, scope.row);
                this.dialogUpdate.display = true;
            },
            doUpdate: function () {
                let self = this;
                axios.post(self.apiUpdate, self.dialogUpdate.data).then(function () {
                    self.dialogUpdate.display = false;
                    self.$message.success('成功');
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
            }
        }
    }
</script>

<style scoped>

</style>