<template>
    <div class="row search-data">
        <div>
            <p class="col-xs-12" v-if="searchResult.type===1" style="color: blue">
                无{{$store.state.searchResult.name}}账号信息，如果确认是恶意号码，请到下方添加！</p>
            <table class="table table-striped table-hover" v-if="searchResult.type===2">
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
                <tr v-for="item in searchResult.account_reports" style="color: red">
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
            <div v-if="searchResult.type===3" style="color: green">
                <p class="col-xs-6">账号:{{searchResult.account.name}}</p>
                <p class="col-xs-6" style="color:blue">认证:{{searchResult.account.status_label}}</p>
                <p class="col-xs-6">认证时间:{{searchResult.account.created_at}}</p>
                <p class="col-xs-6">建议合作金额:{{searchResult.account.auth_cash}}</p>
                <p class="col-xs-6">常用地址:{{searchResult.account.address}}</p>
                <p class="col-xs-6">备注:{{searchResult.account.remark}}</p>
                <p class="col-xs-6">如发现此账号有恶意行为，请用户立即联系网站客服处理</p>
            </div>
            <div v-if="searchResult.type===4" style="color: red">
                <p class="col-xs-12">{{searchResult.name}}已被多数用户举报为恶意号码，请用户谨慎合作</p>
                <p class="col-xs-6">备注:{{searchResult.account.remark}}</p>
                <h1 class="col-xs-6">危险！</h1>
            </div>
        </div>

        <div class="modal fade search-result-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
                                    <img v-if="reportData.attachment" :src="reportData.attachment.url" alt=""
                                         style="max-height: 200px">
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
        name: "search",
        computed: {
            searchResult: function () {
                return this.$store.state.searchResult
            }
        },
        data: function () {
            return {
                reportData: {},
            }
        },
        methods: {
            detail: function (item) {
                this.reportData = item;
                $(".search-result-dialog").modal('show');
            },
            close: function () {
                $(".search-result-dialog").modal('hide');
            }
        }
    }
</script>

<style scoped>
    .search-data {
        font-size: 16px;
        font-weight: 600;
    }

    .search-data p {
        margin: 5px 0;
    }

    .search-result-dialog .form-group > div {
        padding-top: 7px;
    }
</style>