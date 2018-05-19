<template>
    <div class="row search-data">
        <div>
            <div class="row">
                <div class="col-md-12 text-warning">查询结果：</div>
            </div>
            <div class="row" v-if="accounts.length==0">
                <p class="col-md-12 text-primary">
                    无<span class="text-warning">{{name}}</span>账号信息，如果确认是恶意号码，请投诉举报！
                </p>
            </div>
            <div v-if="accounts.length>0" class="row" v-for="(item,index) in accounts" :key="index">
                <div v-if="[103,105,106].indexOf(item.status)>-1" class="text-success">
                    <p class="col-sm-6">{{item.type_label}}账号:<span class="text-warning">{{item.name}}</span></p>
                    <p class="col-sm-6 text-primary">认证:{{item.status_label}}</p>
                    <p class="col-sm-6">认证时间:{{item.created_at}}</p>
                    <p class="col-sm-6">建议合作金额:{{item.auth_cash}}</p>
                    <p class="col-sm-6">常用地址:{{item.address}}</p>
                    <p class="col-sm-6">备注:{{item.remark}}</p>
                    <p class="col-sm-6">如发现此账号有恶意行为，请用户立即联系网站客服处理</p>
                </div>
                <div v-else-if="item.status==104" class="text-danger">
                    <p class="col-sm-6">{{item.type_label}}账号：{{item.name}}</p>
                    <p class="col-sm-6">已被多数用户举报为恶意号码，请用户谨慎合作，危险！</p>
                    <p class="col-sm-6">备注:{{item.remark}}</p>
                </div>
                <div v-else-if="item.status==101">
                    <p class="col-sm-6">
                        {{item.type_label}}账号:<span class="text-warning">{{item.name}}</span>无记录
                    </p>
                </div>
                <div v-else-if="item.status==102&&account_reports.length<1">
                    <p class="col-sm-6">
                        {{item.type_label}}账号:<span class="text-warning">{{item.name}}</span>无记录
                    </p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-hover" v-if="account_reports.length>0">
                    <thead>
                    <tr>
                        <th>账号类型</th>
                        <th>账号</th>
                        <th>举报类型</th>
                        <th class="hidden-xs">举报者IP</th>
                        <th>举报时间</th>
                        <th>详情</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item,index) in account_reports" :key="index" style="color: red">
                        <td>{{item.account_type_label}}</td>
                        <td>{{item.account_name}}</td>
                        <td>{{item.type_label}}</td>
                        <td class="hidden-xs">{{item.ip}}</td>
                        <td>{{item.created_at}}</td>
                        <td>
                            <button class="btn btn-primary" @click="detail(item)">查看</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade search-detail-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
                                    <a v-if="reportData.attachment" :href="reportData.attachment.url" target="_blank">
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
        name: "search",
        computed: {
            name: function () {
                return this.$store.state.searchResult.name;
            },
            accounts: function () {
                return this.$store.state.searchResult.accounts;
            },
            account_reports: function () {
                return this.$store.state.searchResult.account_reports;
            }
        },
        data: function () {
            return {
                reportData: {}
            };
        },
        methods: {
            detail: function (item) {
                this.reportData = item;
                $('.search-detail-dialog').modal('show');
            },
        }
    };
</script>

<style scoped>
    @media (min-width: 768px) {
        .search-data {
            font-size: 16px;
        }
    }

    @media (max-width: 768px) {
        .search-data {
            font-size: 12px;
        }
    }

    .search-data {
        font-weight: 600;
    }

    .search-data p {
        margin: 5px 0;
    }

    .search-result-dialog .form-group > div {
        padding-top: 7px;
    }

    th,
    td {
        text-align: center;
    }
</style>