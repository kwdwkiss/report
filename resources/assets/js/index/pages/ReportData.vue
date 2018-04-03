<template>
    <div class="row report-data">
        <div>
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>账号类型</th>
                    <th>账号</th>
                    <th>举报类型</th>
                    <th>举报者IP</th>
                    <th>举报时间</th>
                    <th>详情</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="item in $store.state.page.last_4_report_data">
                    <td>{{item.account_type_label}}</td>
                    <td>{{item.account_name}}</td>
                    <td>{{item.type_label}}</td>
                    <td>{{item.ip}}</td>
                    <td>{{item.created_at}}</td>
                    <td>
                        <button class="btn btn-primary" @click="detail(item)">查看</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="modal fade report-data-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
                                <div class="col-sm-9">
                                    <span class="">{{reportData.account_type_label}}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">投诉账号</label>
                                <div class="col-sm-9">
                                    <span>{{reportData.account_name}}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">投诉类型</label>
                                <div class="col-sm-9">
                                    <span>{{reportData.type_label}}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">ip</label>
                                <div class="col-sm-9">
                                    <span>{{reportData.ip}}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">图片</label>
                                <div class="col-sm-9">
                                    <a v-if="reportData.attachment" href="reportData.attachment.url" target="_blank">
                                        <img :src="reportData.attachment.url" alt=""
                                             style="max-height: 200px;max-width: 400px">
                                    </a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">情况描述</label>
                                <div class="col-sm-9">
                                    <span>{{reportData.description}}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="button" class="btn btn-primary" @click="close">关闭</button>
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
        name: "report-data",
        data: function () {
            return {
                reportData: {},
            }
        },
        methods: {
            detail: function (item) {
                this.reportData = item;
                $(".report-data-dialog").modal('show');
            },
            close: function () {
                $(".report-data-dialog").modal('hide');
            }
        }
    }
</script>

<style scoped>
    .report-data-dialog .form-group > div {
        padding-top: 7px;
    }
</style>