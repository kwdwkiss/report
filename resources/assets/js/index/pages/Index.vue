<template>
    <div>
        <div class="row search">
            <div class="col-xs-6">
                <select v-model="searchParams.account_type">
                    <option v-for="item in $store.state.taxonomy.account_type" :value="item.id">{{item.name}}
                    </option>
                </select>
                <input v-model="searchParams.name" name="name" type="text" placeholder="请输入账号">
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

        <router-view></router-view>

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
                    <p>{{item.type}}<a :href="item.url">更多</a></p>
                    <p v-for="subItem in item.data">
                        <a class="article-title" target="_blank" :href="subItem.url">{{subItem.title}}</a>
                        <span class="pull-right">{{subItem.created_at}}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="row report-form">
            <div>
                <span>账号类型</span>
                <select v-model="reportParams.account_type" name="account_type">
                    <option v-for="item in $store.state.taxonomy.account_type" :value="item.id">{{item.name}}
                    </option>
                </select>
                <input v-model="reportParams.name" name="name" type="text" placeholder="投诉账号">
                <span>投诉类型</span>
                <select v-model="reportParams.report_type" name="report_type">
                    <option v-for="item in $store.state.taxonomy.report_type" :value="item.id">{{item.name}}</option>
                </select>
                <img :src="captcha_src" alt="" @click="doCaptcha">
                <input v-model="reportParams.captcha" name="captcha" type="text" placeholder="请输入验证码">
                <button @click="doReport" class="btn btn-danger">投诉举报</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "index",
        computed: {
            page: function () {
                return this.$store.state.page;
            },
        },
        data: function () {
            return {
                searchParams: {
                    account_type: store.state.taxonomy.account_type[0].id,
                    name: ''
                },
                reportParams: {
                    account_type: store.state.taxonomy.account_type[0].id,
                    report_type: store.state.taxonomy.report_type[0].id,
                    name: '',
                    captcha: ''
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
                axios.post(api.indexSearch, self.searchParams).then(function (res) {
                    self.$store.commit('searchResult', res.data.data);
                    self.$router.push('/search');
                    self.$message.success('成功');
                }).catch(function () {
                    self.$router.push('/');
                });
            },
            doReport: function () {
                let self = this;
                axios.post(api.indexReport, self.report).then(function () {
                    self.report = {
                        account_type: store.state.taxonomy.account_type[0].id,
                        report_type: store.state.taxonomy.report_type[0].id
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
        height: 32px;
    }

    .article-data p:first-child {
        background-color: #f5f5f5;
    }

    .article-data p:first-child > a {
        float: right;
    }

    .article-data .article-title {
        display: inline-block;
        width: 380px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
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

    .report-form input[name=captcha] {
        width: 100px;
    }
</style>