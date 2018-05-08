<template>
    <div>
        <div v-show="action=='list'">
            <el-row>
                <el-select v-model="search.type" placeholder="会员类型">
                    <el-option v-for="item in userTypeList" :key="item.id" :value="item.id"
                               :label="item.name"></el-option>
                </el-select>
                <el-input v-model="search.mobile" placeholder="手机号" style="width: 150px"></el-input>
                <el-input v-model="search.qq" placeholder="QQ" style="width: 150px"></el-input>
                <el-input v-model="search.wx" placeholder="微信" style="width: 150px"></el-input>
                <el-input v-model="search.ww" placeholder="旺旺" style="width: 150px"></el-input>
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
                    <el-table-column type="expand">
                        <template slot-scope="props">
                            <el-form label-position="left" inline class="demo-table-expand">
                                <el-form-item label="积分">
                                    <span>{{ props.row._profile.amount }}</span>
                                </el-form-item>
                                <el-form-item label="邀请人">
                                    <span>{{ props.row._profile.inviter }}</span>
                                </el-form-item>
                                <el-form-item label="姓名">
                                    <span>{{ props.row._profile.name }}</span>
                                </el-form-item>
                                <el-form-item label="年龄">
                                    <span>{{ props.row._profile.age }}</span>
                                </el-form-item>
                                <el-form-item label="性别">
                                    <span>{{ props.row._profile.gender_label }}</span>
                                </el-form-item>
                                <el-form-item label="职业">
                                    <span>{{ props.row._profile.occupation }}</span>
                                </el-form-item>
                                <el-form-item label="省">
                                    <span>{{ props.row._profile.province }}</span>
                                </el-form-item>
                                <el-form-item label="市">
                                    <span>{{ props.row._profile.city }}</span>
                                </el-form-item>
                                <el-form-item label="支付宝">
                                    <span>{{ props.row._profile.alipay }}</span>
                                </el-form-item>
                                <el-form-item label="备注">
                                    <span>{{ props.row._profile.remark }}</span>
                                </el-form-item>
                            </el-form>
                        </template>
                    </el-table-column>
                    <el-table-column prop="id" label="ID" min-width="100"></el-table-column>
                    <el-table-column prop="type_label" label="会员类型" min-width="100"></el-table-column>
                    <el-table-column prop="mobile" label="手机号" min-width="120"></el-table-column>
                    <el-table-column prop="qq" label="QQ" min-width="120"></el-table-column>
                    <el-table-column prop="wx" label="微信" min-width="120"></el-table-column>
                    <el-table-column prop="ww" label="旺旺" min-width="120"></el-table-column>
                    <el-table-column prop="_profile.amount" label="积分" min-width="100"></el-table-column>
                    <el-table-column prop="created_at" label="创建时间" min-width="180"></el-table-column>
                    <el-table-column label="操作" min-width="200">
                        <template slot-scope="scope">
                            <el-button type="warning" @click="openUpdateDialog(scope)">修改</el-button>
                            <el-button type="danger" @click="doDelete(scope)">删除</el-button>
                        </template>
                    </el-table-column>
                </el-table>
            </el-row>
        </div>

        <div v-show="action=='create'">
            <div class="panel">
                <div class="panel-heading">创建</div>
                <div class="panel-body">
                    <el-form ref="createForm" :model="createData" :rules="rules">
                        <el-form-item prop="type" label="会员类型" labelWidth="100px">
                            <el-select v-model="createData.type">
                                <el-option v-for="item in userTypeList" :key="item.id" :value="item.id"
                                           :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item prop="mobile" label="手机号" labelWidth="100px">
                            <el-input v-model="createData.mobile"></el-input>
                        </el-form-item>
                        <el-form-item prop="qq" label="QQ" labelWidth="100px">
                            <el-input v-model="createData.qq"></el-input>
                        </el-form-item>
                        <el-form-item prop="wx" label="微信" labelWidth="100px">
                            <el-input v-model="createData.wx"></el-input>
                        </el-form-item>
                        <el-form-item prop="ww" label="旺旺" labelWidth="100px">
                            <el-input v-model="createData.ww"></el-input>
                        </el-form-item>
                        <el-form-item prop="remark" label="支付宝" labelWidth="100px">
                            <el-input v-model="createData._profile.alipay"></el-input>
                        </el-form-item>
                        <el-form-item prop="name" label="姓名" labelWidth="100px">
                            <el-input v-model="createData._profile.name"></el-input>
                        </el-form-item>
                        <el-form-item prop="age" label="年龄" labelWidth="100px">
                            <el-input v-model.number="createData._profile.age"></el-input>
                        </el-form-item>
                        <el-form-item label="性别" labelWidth="100px">
                            <el-select v-model="createData._profile.gender">
                                <el-option value="1" label="男"></el-option>
                                <el-option value="2" label="女"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="职业" labelWidth="100px">
                            <el-input v-model="createData._profile.occupation"></el-input>
                        </el-form-item>
                        <el-form-item label="省" labelWidth="100px">
                            <el-select v-model="createData._profile.province" @change="provinceSelect">
                                <el-option v-for="item in provinces" :key="item" :value="item"
                                           :label="item"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="市" labelWidth="100px">
                            <el-select v-model="createData._profile.city">
                                <el-option v-for="item in cities" :key="item" :value="item" :label="item"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item prop="remark" label="备注" labelWidth="100px">
                            <el-input v-model="createData._profile.remark"></el-input>
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
                <div class="panel-heading">更新</div>
                <div class="panel-body">
                    <el-form>
                        <el-form-item prop="type" label="会员类型" labelWidth="100px">
                            <el-select v-model="updateData.type">
                                <el-option v-for="item in userTypeList" :key="item.id" :value="item.id"
                                           :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="手机号" labelWidth="100px">
                            <el-input v-model="updateData.mobile"></el-input>
                        </el-form-item>
                        <el-form-item label="QQ" labelWidth="100px">
                            <el-input v-model="updateData.qq"></el-input>
                        </el-form-item>
                        <el-form-item label="微信" labelWidth="100px">
                            <el-input v-model="updateData.wx"></el-input>
                        </el-form-item>
                        <el-form-item label="旺旺" labelWidth="100px">
                            <el-input v-model="updateData.ww"></el-input>
                        </el-form-item>
                        <el-form-item prop="remark" label="支付宝" labelWidth="100px">
                            <el-input v-model="updateData._profile.alipay"></el-input>
                        </el-form-item>
                        <el-form-item label="姓名" labelWidth="100px">
                            <el-input v-model="updateData._profile.name"></el-input>
                        </el-form-item>
                        <el-form-item label="年龄" labelWidth="100px">
                            <el-input v-model="updateData._profile.age"></el-input>
                        </el-form-item>
                        <el-form-item label="性别" labelWidth="100px">
                            <el-select v-model="updateData._profile.gender">
                                <el-option :value="0" label="未知"></el-option>
                                <el-option :value="1" label="男"></el-option>
                                <el-option :value="2" label="女"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="职业" labelWidth="100px">
                            <el-input v-model="updateData._profile.occupation"></el-input>
                        </el-form-item>

                        <el-form-item label="省" labelWidth="100px">
                            <el-select v-model="updateData._profile.province" @change="provinceSelect">
                                <el-option v-for="item in provinces" :key="item" :value="item"
                                           :label="item"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="市" labelWidth="100px">
                            <el-select v-model="updateData._profile.city">
                                <el-option v-for="item in cities" :key="item" :value="item" :label="item"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item prop="remark" label="备注" labelWidth="100px">
                            <el-input v-model="updateData._profile.remark"></el-input>
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
    import cityData from '../../city.json';

    export default {
        data: function () {
            return {
                action: 'list',
                apiList: api.userList,
                apiCreate: api.userCreate,
                apiUpdate: api.userUpdate,
                apiDelete: api.userDelete,
                tagList: [],
                userTypeList: store.state.taxonomy.user_type,
                provinces: Object.keys(cityData),
                cities: [],
                dataList: {
                    meta: {},
                },
                search: {},
                createData: {tags: [], city: '', type: 0, _profile: {}},
                updateData: {tags: [], city: '', type: 0, _profile: {}},
                rules: {
                    mobile: [
                        {required: true, message: '手机号不能为空', trigger: 'blur'},
                    ],
                }
            }
        },
        created: function () {
            //this.loadTagList();
            this.loadData();
        },
        methods: {
            provinceSelect: function (value) {
                this.cities = Object.keys(cityData[value]);
                this.createData.city = this.cities[0];
                this.updateData.city = this.cities[0];
            },
            loadTagList: function () {
                let self = this;
                axios.get(api.tagList).then(function (res) {
                    self.tagList = res.data.data;
                });
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
                let type = this.userTypeList.length > 0 ? this.userTypeList[0].id : 0;
                this.createData = {tags: [], city: '', type: type, _profile: {}};
                this.action = 'create';
            },
            doCreate: function () {
                let self = this;
                self.$refs.createForm.validate((valid) => {
                    if (valid) {
                        axios.post(self.apiCreate, self.createData).then(function () {
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
                this.updateData = Object.assign({}, scope.row);
                this.action = 'update';
            },
            doUpdate: function () {
                let self = this;
                axios.post(self.apiUpdate, self.updateData).then(function () {
                    self.action = 'list';
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