<template>
    <div>
        <el-row>
            <el-button type="primary" @click="dialogCreate.display=true">添加</el-button>

            <el-pagination layout="prev, pager, next"
                           :total="dataList.meta.total"
                           :page-size="dataList.meta.per_page"
                           @current-change="paginate"></el-pagination>
        </el-row>
        <el-row>
            <el-table :data="dataList.data" stripe>
                <el-table-column prop="name" label="名称" width="200"></el-table-column>
                <el-table-column prop="email" label="邮箱" width="200"></el-table-column>
                <el-table-column label="操作">
                    <template slot-scope="scope">
                        <el-button type="warning" @click="dialogUpdate.row=scope.row;dialogUpdate.display=true">
                            修改密码
                        </el-button>
                        <el-button type="danger" @click="dialogDelete.row=scope.row;dialogDelete.display=true">
                            删除
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-row>

        <el-dialog title="创建" :visible.sync="dialogCreate.display">
            <el-form>
                <el-form-item label="用户名" labelWidth="100px">
                    <el-input v-model="dialogCreate.data.name"></el-input>
                </el-form-item>
                <el-form-item label="密码" labelWidth="100px">
                    <el-input type="password" v-model="dialogCreate.data.password"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogCreate.display = false">取 消</el-button>
                <el-button type="primary" @click="dataCreate">确 定</el-button>
            </div>
        </el-dialog>

        <el-dialog title="更新" :visible.sync="dialogUpdate.display">
            <el-form>
                <el-form-item label="密码" labelWidth="100px">
                    <el-input type="password" v-model="dialogUpdate.data.password"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogUpdate.display = false">取 消</el-button>
                <el-button type="primary" @click="dataUpdate">确 定</el-button>
            </div>
        </el-dialog>

        <el-dialog title="删除" :visible.sync="dialogDelete.display">
            <label>是否删除？</label>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogDelete.display = false">取 消</el-button>
                <el-button type="primary" @click="dataDelete">确 定</el-button>
            </div>
        </el-dialog>

    </div>
</template>

<script>
    export default {
        data: function () {
            return {
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
                dialogDelete: {
                    display: false
                },
            }
        },
        created: function () {
            this.loadData();
        },
        methods: {
            loadData: function (params) {
                let self = this;
                axios.get(api.adminAdminIndex, {
                    params: _.merge(self.dataList.search, params)
                }).then(function (res) {
                    self.dataList = res.data;
                });
            },
            paginate: function (page) {
                _.merge(this.dataList, {search: {page: page}});
                this.loadData();
            },
            dataCreate: function () {
                let self = this;
                axios.post(api.adminAdminCreate, self.dialogCreate.data).then(function () {
                    self.dialogCreate.data = {};
                    self.dialogCreate.display = false;
                    self.$message.success('成功');
                    self.loadData();
                })
            },
            dataUpdate: function () {
                let self = this;
                axios.post(api.adminAdminUpdate, _.assign(
                    {id: self.dialogUpdate.row.id},
                    self.dialogUpdate.data
                )).then(function () {
                    self.dialogUpdate.data = {};
                    self.dialogUpdate.display = false;
                    self.$message.success('成功');
                    self.loadData();
                });
            },
            dataDelete: function () {
                let self = this;
                axios.post(api.adminAdminDelete, {id: self.dialogDelete.row.id}).then(function () {
                    self.dialogDelete.display = false;
                    self.$message.success('成功');
                    self.loadData();
                });
            }
        }
    }
</script>

<style scoped>

</style>