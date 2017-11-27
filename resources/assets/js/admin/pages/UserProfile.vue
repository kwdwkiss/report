<template>
    <div>
        <el-row>
            <el-input v-model="search.mobile" placeholder="手机号" style="width: 150px"></el-input>
            <el-input v-model="search.qq" placeholder="QQ" style="width: 150px"></el-input>
            <el-input v-model="search.wx" placeholder="微信" style="width: 150px"></el-input>
            <el-button type="primary" @click="loadData">搜索</el-button>
            <el-button @click="reset">重置</el-button>
        </el-row>

        <el-row>
            <el-button type="primary" @click="openCreateDialog">添加</el-button>

            <el-pagination layout="prev, pager, next"
                           :total="dataList.meta.total"
                           :page-size="dataList.meta.per_page"
                           @current-change="paginate"></el-pagination>
        </el-row>

        <el-row>
            <el-table :data="dataList.data" stripe>
                <el-table-column prop="id" label="ID" min-width="50"></el-table-column>
                <el-table-column prop="mobile" label="手机号" min-width="120"></el-table-column>
                <el-table-column prop="qq" label="QQ" min-width="100"></el-table-column>
                <el-table-column prop="wx" label="微信" min-width="100"></el-table-column>
                <el-table-column prop="name" label="姓名" min-width="80"></el-table-column>
                <el-table-column prop="age" label="年龄" min-width="60"></el-table-column>
                <el-table-column label="性别" min-width="60">
                    <template slot-scope="scope">
                        {{genderLabel[scope.row.gender]}}
                    </template>
                </el-table-column>
                <el-table-column prop="occupation" label="职业" min-width="80"></el-table-column>
                <el-table-column prop="province" label="省" min-width="80"></el-table-column>
                <el-table-column prop="city" label="市" min-width="80"></el-table-column>
                <el-table-column label="操作" min-width="200">
                    <template slot-scope="scope">
                        <el-button type="warning" @click="openUpdateDialog(scope)">修改</el-button>
                        <el-button type="danger" @click="openDeleteDialog(scope)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
        </el-row>

        <el-dialog title="创建" :visible.sync="dialogCreate.display">
            <el-form ref="createForm" :model="dialogCreate.data" :rules="rules">
                <el-form-item label="标签" labelWidth="100px">
                    <el-select v-model="dialogCreate.data.tags" multiple>
                        <el-option v-for="item in tagList" :key="item.id" :value="item.id"
                                   :label="item.name"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item prop="mobile" label="手机号" labelWidth="100px">
                    <el-input v-model="dialogCreate.data.mobile"></el-input>
                </el-form-item>
                <el-form-item prop="qq" label="QQ" labelWidth="100px">
                    <el-input v-model="dialogCreate.data.qq"></el-input>
                </el-form-item>
                <el-form-item prop="wx" label="微信" labelWidth="100px">
                    <el-input v-model="dialogCreate.data.wx"></el-input>
                </el-form-item>
                <el-form-item prop="name" label="姓名" labelWidth="100px">
                    <el-input v-model="dialogCreate.data.name"></el-input>
                </el-form-item>
                <el-form-item prop="age" label="年龄" labelWidth="100px">
                    <el-input v-model.number="dialogCreate.data.age"></el-input>
                </el-form-item>
                <el-form-item label="性别" labelWidth="100px">
                    <el-select v-model="dialogCreate.data.gender">
                        <el-option value="1" label="男"></el-option>
                        <el-option value="2" label="女"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="职业" labelWidth="100px">
                    <el-input v-model="dialogCreate.data.occupation"></el-input>
                </el-form-item>
                <el-form-item label="省" labelWidth="100px">
                    <el-select v-model="dialogCreate.data.province" @change="provinceSelect">
                        <el-option v-for="item in provinces" :key="item" :value="item" :label="item"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="市" labelWidth="100px">
                    <el-select v-model="dialogCreate.data.city">
                        <el-option v-for="item in cities" :key="item" :value="item" :label="item"></el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogCreate.display = false">取 消</el-button>
                <el-button type="primary" @click="doCreate">确 定</el-button>
            </div>
        </el-dialog>

        <el-dialog title="更新" :visible.sync="dialogUpdate.display">
            <el-form>
                <el-form-item label="标签" labelWidth="100px">
                    <el-select v-model="dialogUpdate.data.tags" multiple>
                        <el-option v-for="item in tagList" :key="item.id" :value="item.id"
                                   :label="item.name"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="手机号" labelWidth="100px">
                    <el-input v-model="dialogUpdate.data.mobile"></el-input>
                </el-form-item>
                <el-form-item label="QQ" labelWidth="100px">
                    <el-input v-model="dialogUpdate.data.qq"></el-input>
                </el-form-item>
                <el-form-item label="微信" labelWidth="100px">
                    <el-input v-model="dialogUpdate.data.wx"></el-input>
                </el-form-item>
                <el-form-item label="姓名" labelWidth="100px">
                    <el-input v-model="dialogUpdate.data.name"></el-input>
                </el-form-item>
                <el-form-item label="年龄" labelWidth="100px">
                    <el-input v-model="dialogUpdate.data.age"></el-input>
                </el-form-item>
                <el-form-item label="性别" labelWidth="100px">
                    <el-select v-model="dialogUpdate.data.gender">
                        <el-option :value="1" label="男"></el-option>
                        <el-option :value="2" label="女"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="职业" labelWidth="100px">
                    <el-input v-model="dialogUpdate.data.occupation"></el-input>
                </el-form-item>

                <el-form-item label="省" labelWidth="100px">
                    <el-select v-model="dialogUpdate.data.province" @change="provinceSelect">
                        <el-option v-for="item in provinces" :key="item" :value="item" :label="item"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="市" labelWidth="100px">
                    <el-select v-model="dialogUpdate.data.city">
                        <el-option v-for="item in cities" :key="item" :value="item" :label="item"></el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogUpdate.display = false">取 消</el-button>
                <el-button type="primary" @click="doUpdate">确 定</el-button>
            </div>
        </el-dialog>

        <el-dialog title="删除" :visible.sync="dialogDelete.display">
            <label>是否删除？</label>
            <div slot="footer" class="dialog-footer">
                <el-button @click="dialogDelete.display = false">取 消</el-button>
                <el-button type="primary" @click="doDelete">确 定</el-button>
            </div>
        </el-dialog>

    </div>
</template>

<script>
    import cityData from '../../city.json';

    export default {
        data: function () {
            return {
                tagList: [],
                provinces: Object.keys(cityData),
                cities: [],
                genderLabel: {
                    0: '未知',
                    1: '男',
                    2: '女',
                },
                dataList: {
                    meta: {},
                },
                search: {},
                dialogCreate: {
                    display: false,
                    data: {
                        tags: []
                    }
                },
                dialogUpdate: {
                    display: false,
                    data: {
                        tags: []
                    }
                },
                dialogDelete: {
                    display: false
                },
                rules: {
                    name: [
                        {required: true, message: '姓名不能为空', trigger: 'blur'},
                    ],
                    mobile: [
                        {required: true, message: '手机号不能为空', trigger: 'blur'},
                    ],
                    qq: [
                        {required: true, message: 'QQ不能为空', trigger: 'blur'},
                    ],
                    wx: [
                        {required: true, message: '微信不能为空', trigger: 'blur'},
                    ],
                }
            }
        },
        created: function () {
            this.loadTagList();
            this.loadData();
        },
        methods: {
            provinceSelect: function (value) {
                this.cities = Object.keys(cityData[value]);
                this.dialogCreate.data.city = this.cities[0];
                this.dialogUpdate.data.city = this.cities[0];
            },
            loadTagList: function () {
                let self = this;
                axios.get(api.tagList).then(function (res) {
                    self.tagList = res.data.data;
                });
            },
            loadData: function () {
                let self = this;
                axios.get(api.userProfileList, {params: self.search}).then(function (res) {
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
                this.dialogCreate.data = {
                    tags: [],
                    city: ''
                };
                this.dialogCreate.display = true
            },
            doCreate: function () {
                let self = this;
                self.$refs.createForm.validate((valid) => {
                    if (valid) {
                        axios.post(api.userProfileCreate, self.dialogCreate.data).then(function () {
                            self.dialogCreate.display = false;
                            self.$message.success('成功');
                            self.loadData();
                        })
                    } else {
                        return false;
                    }
                });
            },
            openUpdateDialog: function (scope) {
                this.dialogUpdate.data = Object.assign({}, scope.row);
                this.dialogUpdate.display = true;
            },
            doUpdate: function () {
                let self = this;
                axios.post(api.userProfileUpdate, self.dialogUpdate.data).then(function () {
                    self.dialogUpdate.display = false;
                    self.$message.success('成功');
                    self.loadData();
                });
            },
            openDeleteDialog: function (scope) {
                this.dialogDelete.data = Object.assign({}, scope.row);
                this.dialogDelete.display = true;
            },
            doDelete: function () {
                let self = this;
                axios.post(api.userProfileDelete, {id: self.dialogDelete.data.id}).then(function () {
                    self.dialogDelete.display = false;
                    self.$message.success('成功');
                    self.loadData();
                });
            }
        }
    }
</script>

<style scoped>
    .el-dialog .el-select {
        width: 100%;
    }
</style>