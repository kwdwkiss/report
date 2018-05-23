<template>
    <div>
        <el-row>
            <el-select v-model="search.type" placeholder="会员类型">
                <el-option v-for="item in userTypeList" :key="item.id" :value="item.id"
                           :label="item.name"></el-option>
            </el-select>
            <el-input v-model="search.mobile" placeholder="手机号" style="width: 150px"></el-input>
            <el-input v-model="search.qq" placeholder="QQ" style="width: 150px"></el-input>
            <el-input v-model="search.wx" placeholder="微信" style="width: 150px"></el-input>
            <el-input v-model="search.ww" placeholder="旺旺" style="width: 150px"></el-input>
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
                            <el-form-item label="个人资料锁定">
                                <el-switch v-model="props.row._profile.user_lock" :active-value="1"
                                           :inactive-value="0"
                                           @change="doUpdateRow(props.row)">
                                </el-switch>
                            </el-form-item>
                        </el-form>
                        <template v-if="props.row._merchant">
                            <hr>
                            <el-form label-position="left" inline class="demo-table-expand">
                                <el-form-item label="店铺类型">
                                    <span>{{ props.row._merchant.type_label }}</span>
                                </el-form-item>
                                <el-form-item label="店铺名称">
                                    <span>{{ props.row._merchant.name }}</span>
                                </el-form-item>
                                <el-form-item label="商品类型">
                                    <span>{{ props.row._merchant.goods_type }}</span>
                                </el-form-item>
                                <el-form-item label="店铺网址">
                                    <span>{{ props.row._merchant.url }}</span>
                                </el-form-item>
                                <el-form-item label="店铺信誉">
                                    <span>{{ props.row._merchant.credit }}</span>
                                </el-form-item>
                                <el-form-item label="店铺掌柜">
                                    <span>{{ props.row._merchant.manager }}</span>
                                </el-form-item>
                                <el-form-item label="店铺资料锁定">
                                    <el-switch v-model="props.row._merchant.user_lock" :active-value="1"
                                               :inactive-value="0"
                                               @change="doUpdateRow(props.row)">
                                    </el-switch>
                                </el-form-item>
                            </el-form>
                        </template>
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
                apiList: api.userList,
                apiUpdate: api.userUpdate,
                apiDelete: api.userDelete,
                dataList: {meta: {}},
                search: {},
            }
        },
        computed: {
            userTypeList: function () {
                return this.$store.state.taxonomy.user_type;
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
                this.$router.push({name: 'user_create'});
            },
            doUpdate: function (row) {
                this.$router.push({name: 'user_update', params: {id: row.id}});
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
            doUpdateRow: function (row) {
                let self = this;
                axios.post(self.apiUpdate, row).then(function () {
                    self.$message.success('成功');
                    self.loadData();
                });
            },
            doMerchantModify: function (row) {
                let self = this;
                axios.post(api.adminUserMerchantModify, row).then(function () {
                    self.$message.success('成功');
                    self.loadData();
                });
            },
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