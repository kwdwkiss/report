<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">我的举报</div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-responsive table-striped">
                        <thead>
                        <tr>
                            <th style="width: 100px">账号类型</th>
                            <th style="width: 100px">账号</th>
                            <th style="width: 100px">举报类型</th>
                            <th style="width: 180px">创建时间</th>
                            <th style="width: 130px">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, index) in report.data" :key="index">
                            <td>{{item.account_type_label}}</td>
                            <td>{{item.account_name}}</td>
                            <td>{{item.type_label}}</td>
                            <td>{{item.created_at}}</td>
                            <td>
                                <button class="btn btn-primary" @click="detail(item)">查看</button>
                                <button class="btn btn-danger" @click="hide(item)">删除</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <el-pagination class="pull-right" layout="prev, pager, next"
                               :total="report.meta.total"
                               :page-size="report.meta.per_page"
                               @current-change="paginate"></el-pagination>
            </div>
        </div>

        <div class="modal fade detail-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">举报详情</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">账号类型</label>
                                <div class="col-sm-9 form-control-static">
                                    {{item.account_type_label}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">投诉账号</label>
                                <div class="col-sm-9 form-control-static">
                                    {{item.account_name}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">投诉类型</label>
                                <div class="col-sm-9 form-control-static">
                                    {{item.type_label}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">图片</label>
                                <div class="col-sm-9">
                                    <a v-if="item.attachment" :href="item.attachment.url"
                                       target="_blank">
                                        <img :src="item.attachment.url" alt=""
                                             style="max-height: 200px;max-width: 400px">
                                    </a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">情况描述</label>
                                <div class="col-sm-9 form-control-static">
                                    {{item.description}}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="button" class="btn btn-primary" data-dismiss="modal">关闭</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "UserReport",
        data: function () {
            return {
                item: {}
            }
        },
        computed: {
            report: function () {
                return this.$store.state.report;
            },
            user: function () {
                return this.$store.state.user;
            }
        },
        created: function () {
            this.list();
        },
        methods: {
            list: function () {
                this.$store.commit("report");
            },
            paginate: function (page) {
                this.$store.commit("report", {page: page});
            },
            detail: function (item) {
                this.item = item;
                $('.detail-dialog').modal('show');
            },
            hide: function (item) {
                let self = this;
                axios.post(api.userReportHide, {id: item.id}).then(function (res) {
                    self.$message.success('成功');
                    self.$store.commit('report');
                })
            }
        }
    }
</script>

<style scoped>
    @media (max-width: 768px) {
        .panel-body {
            padding: 15px 0px;
        }
    }
</style>