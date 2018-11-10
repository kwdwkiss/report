<template>
    <div>
        <div class="row hidden-md hidden-lg hidden-sm">
            <a :href="page.mobile_ad.report_search_top.url">
                <img :src="page.mobile_ad.report_search_top.img_src" style="width: 100%;max-height: 80px">
            </a>
        </div>

        <div class="row hidden-xs dashboard">
            <div class="col-sm-4">
                <p>恶意账号：<span class="text-warning">{{page.report_num}}</span></p>
            </div>
            <div class="col-sm-4">
                <p>最新举报：<span class="text-danger">{{page.last_24_report_num}}</span></p>
            </div>
            <div class="col-sm-4">
                <p>网站会员：<span class="text-success">{{page.auth_member_num}}</span></p>
            </div>
        </div>

        <div class="row search">
            <div class="hidden-xs hidden-sm col-md-2">
                <label><a class="text-primary" href="javascript:" @click="isSearch=false">账号查询</a></label>
            </div>
            <div class="col-xs-12 col-md-4">
                <input class="form-control" v-model="searchParams.name" name="name" type="text"
                       placeholder="请输入QQ、旺旺、微信、IS等各类账号">
            </div>
            <div class="col-xs-4 col-md-2">
                <button @click="doSearch" class="form-control btn btn-success">查询</button>
            </div>
            <div class="col-xs-4 col-md-2">
                <button @click="go('accountReportCreate')" class="form-control btn btn-danger">投诉举报</button>
            </div>
            <div class="col-xs-4 col-md-2">
                <button @click="go('accountFavorCreate')" class="form-control btn btn-primary">点赞</button>
            </div>
        </div>

        <div v-if="!isSearch" class="row report-data">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>账号类型</th>
                        <th>账号</th>
                        <th>举报类型</th>
                        <th class="hidden-xs">举报者IP</th>
                        <th class="hidden-xs">举报时间</th>
                        <th>详情</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(item,index) in page.last_4_report_data" :key="index">
                        <td>{{item.account_type_label}}</td>
                        <td>{{item.account_name}}</td>
                        <td>{{item.type_label}}</td>
                        <td class="hidden-xs">{{item.ip}}</td>
                        <td class="hidden-xs">{{item.created_at}}</td>
                        <td>
                            <button class="btn btn-primary" @click="detailReport(item)">查看</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="name" class="row search-data">
            <div>
                <div class="row" v-if="searchUser.length>0">
                    <div class="col-md-12 text-warning">
                        会员信息：
                    </div>
                </div>
                <div class="row" v-for="(item,index) in searchUser" :key="'search_user'+index"
                     v-bind:class="{'text-danger':!item.is_auth,'text-success':item.is_auth}">
                    <div>
                        <p class="col-xs-6 col-sm-4">
                            会员审核:
                            <strong class="bold" v-if="!item.is_auth">
                                （未审核）
                            </strong>
                            <strong class="bold" v-if="item.is_auth">
                                （实名认证）
                            </strong>
                        </p>
                        <p class="col-xs-6 col-sm-4">会员编号：{{item.id}}</p>
                        <p class="col-xs-6 col-sm-4">会员类型：{{item.type_label}}</p>
                        <!--<p class="col-xs-6 col-sm-4">保证金：{{item._profile.deposit}}元</p>-->
                        <p class="col-xs-6 col-sm-4">账号：{{item.mobile}}</p>
                        <p class="col-xs-6 col-sm-4">QQ：{{item.qq}}</p>
                        <p class="col-xs-6 col-sm-4">微信：{{item.wx}}</p>
                        <!--<p class="col-xs-6 col-sm-4">旺旺：{{item.ww}}</p>-->
                        <!--<p class="col-xs-6 col-sm-4">京东：{{item.jd}}</p>-->
                        <!--<p class="col-xs-6 col-sm-4">IS：{{item.is}}</p>-->
                        <!--<p class="col-xs-6 col-sm-4">姓名：{{item._profile.name}}</p>-->
                        <!--<p class="col-xs-6 col-sm-4">性别：{{item._profile.gender_label}}</p>-->
                        <p class="col-xs-6 col-sm-4">地址：{{item._profile.address}}</p>
                        <p class="col-xs-6 col-sm-4 text-primary">备注：{{item._profile.remark}}</p>
                    </div>
                </div>
                <div class="row" v-if="account_favors.length>0">
                    <div class="col-md-12 text-warning">
                        点赞信息：
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-md-4 text-primary" v-for="item in account_favors_total">
                        {{item.account_type_label}}:{{item.account_name}}&nbsp;点赞{{item.total}}次
                    </div>
                </div>
                <div class="table-responsive" v-if="account_favors.length>0">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th>账号类型</th>
                            <th>账号</th>
                            <th class="hidden-xs">点赞者ip</th>
                            <th>点赞时间</th>
                        </tr>
                        </thead>
                        <tbody class="text-primary">
                        <tr v-for="(item,index) in account_favors" :key="'account_favors'+index">
                            <td>{{item.account_type_label}}</td>
                            <td>{{item.account_name}}</td>
                            <td class="hidden-xs">{{item.ip}}</td>
                            <td>{{item.created_at}}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="row">
                    <div class="col-md-12 text-warning">查询结果：</div>
                </div>
                <div class="row" v-if="account_reports.length==0">
                    <p class="col-md-12 text-primary">
                        账号:<span class="text-warning">{{name}}</span> 暂无任何用户举报记录，请自行甄别合作。
                    </p>
                </div>
                <div class="table-responsive" v-if="account_reports.length>0">
                    <table class="table table-striped table-hover">
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
                        <tr v-for="(item,index) in account_reports" :key="'account_reports'+index" style="color: red">
                            <td>{{item.account_type_label}}</td>
                            <td>{{item.account_name}}</td>
                            <td>{{item.type_label}}</td>
                            <td class="hidden-xs">{{item.ip}}</td>
                            <td>{{item.created_at}}</td>
                            <td>
                                <button class="btn btn-primary" @click="detailSearch(item)">查看</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
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
                                <div class="col-sm-9 form-control-static">
                                    {{reportData.account_type_label}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">投诉账号</label>
                                <div class="col-sm-9 form-control-static">
                                    {{reportData.account_name}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">投诉类型</label>
                                <div class="col-sm-9 form-control-static">
                                    {{reportData.type_label}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">ip</label>
                                <div class="col-sm-9 form-control-static">
                                    {{reportData.ip}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">图片</label>
                                <div class="col-sm-9">
                                    <a v-if="reportData.attachment" :href="reportData.attachment.url"
                                       target="_blank">
                                        <img :src="reportData.attachment.url" alt=""
                                             style="max-height: 200px;max-width: 400px">
                                    </a>
                                    <a v-if="reportData.attachment1" :href="reportData.attachment1.url"
                                       target="_blank">
                                        <img :src="reportData.attachment1.url" alt=""
                                             style="max-height: 200px;max-width: 400px">
                                    </a>
                                    <a v-if="reportData.attachment2" :href="reportData.attachment2.url"
                                       target="_blank">
                                        <img :src="reportData.attachment2.url" alt=""
                                             style="max-height: 200px;max-width: 400px">
                                    </a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">情况描述</label>
                                <div class="col-sm-9 form-control-static">
                                    {{reportData.description}}
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
                                <div class="col-sm-9 form-control-static">
                                    {{reportData.account_type_label}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">投诉账号</label>
                                <div class="col-sm-9 form-control-static">
                                    {{reportData.account_name}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">投诉类型</label>
                                <div class="col-sm-9 form-control-static">
                                    {{reportData.type_label}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">ip</label>
                                <div class="col-sm-9 form-control-static">
                                    {{reportData.ip}}
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">图片</label>
                                <div class="col-sm-9">
                                    <a v-if="reportData.attachment" :href="reportData.attachment.url"
                                       target="_blank">
                                        <img :src="reportData.attachment.url" alt=""
                                             style="max-height: 200px;max-width: 400px">
                                    </a>
                                    <a v-if="reportData.attachment1" :href="reportData.attachment1.url"
                                       target="_blank">
                                        <img :src="reportData.attachment1.url" alt=""
                                             style="max-height: 200px;max-width: 400px">
                                    </a>
                                    <a v-if="reportData.attachment2" :href="reportData.attachment2.url"
                                       target="_blank">
                                        <img :src="reportData.attachment2.url" alt=""
                                             style="max-height: 200px;max-width: 400px">
                                    </a>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">情况描述</label>
                                <div class="col-sm-9 form-control-static">
                                    {{reportData.description}}
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

        <middle-ad></middle-ad>

        <article-data></article-data>

        <bottom-ad></bottom-ad>

        <div class="row hidden-md hidden-lg hidden-sm">
            <a :href="page.mobile_ad.report_search.url">
                <img :src="page.mobile_ad.report_search.img_src" style="width: 100%;max-height: 80px">
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "index",
        data: function () {
            return {
                isSearch: false,
                reportData: {},
                searchParams: {},
            };
        },
        computed: {
            page: function () {
                return this.$store.state.page;
            },
            user: function () {
                return this.$store.state.user;
            },
            name: function () {
                return this.$store.state.searchResult.name;
            },
            accounts: function () {
                return this.$store.state.searchResult.accounts;
            },
            account_reports: function () {
                return this.$store.state.searchResult.account_reports;
            },
            searchUser: function () {
                return this.$store.state.searchResult.user;
            },
            account_favors: function () {
                return this.$store.state.searchResult.account_favors;
            },
            account_favors_total: function () {
                return this.$store.state.searchResult.account_favors_total;
            }
        },
        created: function () {
            this.initSearchParams();
        },
        methods: {
            detailReport: function (item) {
                if (!this.user.report_detail_enable) {
                    this.$message.error(this.user.report_detail_label);
                    return;
                }
                this.reportData = item;
                $('.report-data-dialog').modal('show');
            },
            detailSearch: function (item) {
                if (!this.user.report_detail_enable) {
                    this.$message.error(this.user.report_detail_label);
                    return;
                }
                this.reportData = item;
                $('.search-detail-dialog').modal('show');
            },
            initSearchParams: function () {
                this.searchParams = {name: ""};
            },
            doSearch: function () {
                let self = this;
                let account_type = this.searchParams.account_type;
                let name = this.searchParams.name;
                this.$store.commit("searchResult", {
                    account_type: account_type, name: name,
                    callback: function () {
                        self.$store.commit("user");
                    }
                });
                this.isSearch = true;
            },
            go: function (name) {
                this.$router.push({name: name});
            }
        }
    };
</script>

<style scoped>
    .dashboard {
        padding: 10px 0;
        font-size: 20px;
        font-weight: bold;
        text-align: center;
    }

    .search > div {
        margin: 10px 0;
    }

    .search {
        text-align: center;
    }

    .search label {
        margin: 0;
        line-height: 36px;
        font-size: 20px;
        font-weight: bold;
        text-align: center;
    }

    @media (min-width: 768px) {
        .report-data {
            font-size: 16px;
        }
    }

    @media (max-width: 768px) {
        .report-data {
            font-size: 12px;
        }
    }

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

    th,
    td {
        text-align: center;
    }
</style>