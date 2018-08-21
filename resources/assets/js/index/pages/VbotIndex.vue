<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5>微信清粉工具——任务列表</h5>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <div class="row">
                        <button class="btn btn-primary" @click="createDialog">创建任务</button>
                        <el-pagination class="pull-right" layout="prev, pager, next"
                                       :total="dataList.meta.total"
                                       :page-size="dataList.meta.per_page"
                                       @current-change="paginate"></el-pagination>
                    </div>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>名称</th>
                            <th>状态</th>
                            <th class="hidden-xs">创建时间</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in dataList.data">
                            <td>{{item.id}}</td>
                            <td>{{item.name}}</td>
                            <td>{{item.status_label}}</td>
                            <td class="hidden-xs">{{item.created_at}}</td>
                            <td>
                                <button class="btn btn-warning" @click="doAdmin(item)">管理</button>
                                <button class="btn btn-danger" @click="doDelete(item)">删除</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="create-dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">创建任务</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal">
                            <div class="form-group">
                                <label class="col-md-2 control-label">名称</label>
                                <div class="col-md-8">
                                    <input class="form-control" type="text" v-model="form.name">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-offset-2 col-md-8">
                                    <button class="btn btn-success" @click="doCreate">提交</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <hr>
        <div class="row hidden-md hidden-lg hidden-sm">
            <a :href="page.mobile_ad.wx_clear_friends.url">
                <img :src="page.mobile_ad.wx_clear_friends.img_src" style="width: 100%;max-height: 100px">
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "VbotIndex",
        data: function () {
            return {
                dataList: {meta: {}},
                search: {order_query: {}},
                form: {},
            }
        },
        computed: {
            page: function () {
                return this.$store.state.page;
            },
        },
        mounted: function () {
            this.loadData();
        },
        methods: {
            paginate: function (page) {
                this.search.page = page;
                this.loadData();
            },
            loadData: function () {
                let self = this;
                axios.get(api.userVbotIndex, {params: self.search}).then(function (res) {
                    self.dataList = res.data;
                });
            },
            createDialog: function () {
                $('#create-dialog').modal('show');
            },
            doCreate: function () {
                let self = this;
                axios.post(api.userVbotCreate, self.form).then(function (res) {
                    $('#create-dialog').modal('hide');
                    self.$message.success('创建成功');
                    self.loadData();
                });
            },
            doDelete: function (item) {
                let self = this;
                axios.post(api.userVbotDelete, {id: item.id}).then(function (res) {
                    self.$message.success('删除成功');
                    self.loadData();
                });
            },
            doAdmin: function (item) {
                this.$router.push({name: 'vbotDetail', params: {id: item.id}})
            }
        },
    }
</script>

<style scoped>
    @media (max-width: 768px) {
        .panel-body {
            padding: 15px 0px;
        }
    }
</style>