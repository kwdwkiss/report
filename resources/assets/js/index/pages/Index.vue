<template>
    <div>
        <div class="row search">
            <div class="col-xs-6">
                <select v-model="search.account_type">
                    <option v-for="item in page.account_types" :value="item.id">{{item.name}}</option>
                </select>
                <input v-model="search.name" name="name" type="text" placeholder="请输入账号">
                <button @click="doSearch" class="btn btn-success">查询</button>
            </div>
            <div class="col-xs-6 member-num">
                <p>网站实名认证会员：<span>{{page.auth_member_num}}</span>名会员</p>
            </div>
        </div>

        <div class="row report-num">
            <p>
                本站目前已有<span>{{page.report_num}}</span>条恶意账号数据，最近24小时危险监测更新<span>{{page.last_24_report_num}}</span>条数据
            </p>
        </div>

        <div class="row search-data" v-show="searchData.type!==0">
            <div>
                <p class="col-xs-12" v-show="searchData.type===1" style="color: blue">
                    无{{searchData.name}}账号信息，如果确认是恶意号码，请到下方添加！</p>
                <table class="table table-striped table-hover" v-if="searchData.type===2">
                    <thead>
                    <tr>
                        <th>账号类型</th>
                        <th>账号</th>
                        <th>举报类型</th>
                        <th>举报者IP</th>
                        <th>举报时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in searchData.account_reports" style="color: red">
                        <td>{{item.account_type}}</td>
                        <td>{{item.account_name}}</td>
                        <td>{{item.type_label}}</td>
                        <td>{{item.ip}}</td>
                        <td>{{item.created_at}}</td>
                    </tr>
                    </tbody>
                </table>
                <div v-if="searchData.type===3" style="color: green">
                    <p class="col-xs-6">账号:{{searchData.account.name}}</p>
                    <p class="col-xs-6" style="color:blue">认证:{{searchData.account.status_label}}</p>
                    <p class="col-xs-6">认证时间:{{searchData.account.auth_at}}</p>
                    <p class="col-xs-6">建议合作金额:{{searchData.account.auth_cash}}</p>
                    <p class="col-xs-6">常用地址:{{searchData.account.address}}</p>
                    <p class="col-xs-6">备注:{{searchData.account.remark}}</p>
                    <p class="col-xs-6">如发现此账号有恶意行为，请用户立即联系网站客服处理</p>
                </div>
                <div v-if="searchData.type===4" style="color: red">
                    <p class="col-xs-12">{{searchData.name}}已被多数用户举报为恶意号码，请用户谨慎合作</p>
                    <p class="col-xs-6">备注:{{searchData.account.remark}}</p>
                    <h1 class="col-xs-6">危险！</h1>
                </div>
            </div>
        </div>

        <div class="row report-data" v-show="searchData.type===0">
            <div>
                <table class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th>账号类型</th>
                        <th>账号</th>
                        <th>举报类型</th>
                        <th>举报者IP</th>
                        <th>举报时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="item in page.last_4_report_data">
                        <td>{{item.account_type_label}}</td>
                        <td>{{item.account_name}}</td>
                        <td>{{item.type_label}}</td>
                        <td>{{item.ip}}</td>
                        <td>{{item.created_at}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row ad">
            <div class="col-xs-6" v-for="item in page.ad_third">
                <a target="_blank" :href="item.url">
                    <img :src="item.img_src">
                </a>
            </div>
        </div>

        <div class="row article-data">
            <div class="col-xs-6" v-for="item in page.article_data">
                <div>
                    <p>{{item.type}}<a target="_blank" :href="item.url">更多</a></p>
                    <p v-for="subItem in item.data"><a target="_blank" :href="subItem.url">{{subItem.title}}</a></p>
                </div>
            </div>
        </div>

        <div class="row report-form">
            <div>
                <span>账号类型</span>
                <select v-model="report.account_type" name="account_type">
                    <option v-for="item in page.account_types" :value="item.id">{{item.name}}</option>
                </select>
                <input v-model="report.name" name="name" type="text" placeholder="投诉账号">
                <span>投诉类型</span>
                <select v-model="report.report_type" name="report_type">
                    <option v-for="item in page.report_types" :value="item.id">{{item.name}}</option>
                </select>
                <img :src="captcha_src" alt="" @click="doCaptcha">
                <input v-model="report.captcha" name="captcha" type="text" placeholder="请输入验证码">
                <button @click="doReport" class="btn btn-danger">投诉举报</button>
            </div>
        </div>
    </div>
</template>

<script>
    let page = window.laravel;

    export default {
        name: "index",
        data: function () {
            return {
                page: page,
                search: {
                    account_type: page.account_types[0].id
                },
                searchData: {
                    account_reports: [],
                    account: {},
                    type: 0,
                },
                //type 0-不显示 1-显示无记录 2-显示记录列表 3-显示账号信息 4-显示骗子
                report: {
                    account_type: page.account_types[0].id,
                    report_type: page.report_types[0].id,
                },
                captcha_src: api.captcha + '?' + Date.parse(new Date())
            }
        },
        methods: {
            doCaptcha: function () {
                this.captcha_src = api.captcha + '?' + Date.parse(new Date());
            },
            doSearch: function () {
                let self = this;
                axios.post(api.indexSearch, self.search).then(function (res) {
                    self.searchData = res.data.data;
                    self.$message.success('成功');
                });
            },
            doReport: function () {
                let self = this;
                axios.post(api.indexReport, self.report).then(function () {
                    self.report = {
                        account_type: page.account_types[0].id,
                        report_type: page.report_types[0].id
                    };
                    self.doCaptcha();
                    self.$message.success('成功');
                }).catch(function () {
                    self.doCaptcha();
                });
            }
        }
    }
</script>

<style scoped>
    .search {
        background-color: #f5f5f5;
        line-height: 60px;
        font-size: 16px;
    }

    .search > div:first-child > * {
        margin: 0 5px;
        height: 35px;
    }

    .search select {
        width: 100px;
    }

    .search input[name=name] {
        width: 250px;
    }

    .search button {
        width: 80px;
    }

    .member-num {
        font-size: 16px;
        font-weight: 600;
    }

    .member-num span {
        color: green;
    }

    .report-num {
        font-size: 16px;
        font-weight: 600;
    }

    .report-num span {
        color: red;
    }

    .report-data {
        font-size: 16px;
    }

    .search-data {
        font-size: 16px;
        font-weight: 600;
    }

    .article-data > div {
        margin: 5px 0;
    }

    .article-data > div > div {
        border: 1px solid #9d9d9d;
        border-radius: 3px;
        height: 160px;
    }

    .article-data p {
        padding: 5px 10px;
    }

    .article-data p:first-child {
        background-color: #f5f5f5;
    }

    .article-data p:first-child > a {
        float: right;
    }

    .report-form {
        background-color: #f5f5f5;
        line-height: 60px;
    }

    .report-form > div > * {
        margin: 0 5px;
        height: 35px;
    }

    .report-form select {
        width: 100px;
    }

    .report-form input[name=name] {
        width: 200px;
    }

    input[name=captcha] {
        width: 100px;
    }
</style>