<template>
    <div>
        <div class="row search">
            <div class="pull-left">
                <select v-model="searchParams.account_type">
                    <option v-for="item in $store.state.taxonomy.account_type" :value="item.id">{{item.name}}
                    </option>
                </select>
                <input v-model="searchParams.name" name="name" type="text" placeholder="请输入账号">
                <button @click="doSearch" class="btn btn-success">查询</button>
                <button v-if="!user" @click="login" class="btn btn-primary">登陆</button>
                <span v-if="user">{{user.mobile}}，欢迎你！<a href="javascript:" @click="doLogout">注销</a></span>
            </div>
            <div class="pull-right member-num">
                <p>网站实名认证会员：<span>{{page.auth_member_num}}</span>名会员</p>
            </div>
        </div>

        <div class="modal fade" id="login-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                    </div>
                    <div class="modal-body">
                        <form v-if="loginStatus=='login'" class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">手机号</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入手机号" name="mobile"
                                           v-model="loginForm.mobile">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">密码</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" placeholder="请输入密码" name="password"
                                           v-model="loginForm.password">
                                    <a href="javascript:" @click="register">忘记密码</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <a class="btn btn-primary" @click="doLogin">登录</a>
                                    <a class="btn btn-default" @click="register">注册</a>
                                </div>
                            </div>
                        </form>
                        <form v-if="loginStatus=='register'" class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">手机号</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入手机号" name="mobile"
                                           v-model="registerForm.mobile">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">密码</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control"
                                           placeholder="密码必须包含字母、数字、符号两种组合且长度为8-16" name="password"
                                           v-model="registerForm.password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">短信验证码</label>
                                <div class="col-sm-6">
                                    <input class="form-control" placeholder="请输入短信验证码"
                                           v-model="registerForm.code">
                                </div>
                                <span class="col-sm-3">
                                    <button type="button" class="btn btn-success" @click="sendSms"
                                            v-bind:disabled="smsDisable">{{smsText}}</button>
                                </span>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <a class="btn btn-primary" @click="doRegister">提交</a>
                                    <a class="btn btn-default" @click="loginStatus='login'">返回</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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
            user: function () {
                return this.$store.state.user;
            }
        },
        data: function () {
            return {
                smsText: '发送短信',
                smsDisable: false,
                smsTimer: 60,
                smsHandle: null,
                loginStatus: null,
                registerForm: {
                    mobile: '',
                    password: '',
                    code: '',
                },
                loginForm: {
                    mobile: '',
                    password: '',
                },
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
                }).catch(function () {
                    self.$router.push('/');
                });
            },
            doReport: function () {
                let self = this;
                axios.post(api.indexReport, self.reportParams).then(function () {
                    self.reportParams = {
                        account_type: store.state.taxonomy.account_type[0].id,
                        report_type: store.state.taxonomy.report_type[0].id
                    };
                    self.doCaptcha();
                    self.$message.success('成功');
                }).catch(function () {
                    self.doCaptcha();
                });
            },
            login: function () {
                $("#login-dialog").modal('show');
                this.loginStatus = 'login';
            },
            doLogin: function () {
                let self = this;
                axios.post(api.userLogin, self.loginForm).then(function (res) {
                    self.$store.commit('user');
                    $("#login-dialog").modal('hide');
                });
            },
            doLogout: function () {
                let self = this;
                axios.get(api.userLogout).then(function (res) {
                    self.$store.commit('user');
                });
            },
            register: function () {
                this.loginStatus = 'register';
            },
            registerInit: function () {
                this.registerForm.mobile = '';
                this.registerForm.password = '';
                this.registerForm.code = '';
            },
            doRegister: function () {
                let self = this;
                axios.post(api.userRegister, self.registerForm).then(function (res) {
                    self.$message.success('成功');
                    $("#login-dialog").modal('hide');
                    self.registerInit();
                    self.$store.commit('user');
                });
            },
            smsInit: function () {
                clearInterval(self.smsHandle);
                this.smsText = '发送短信';
                this.smsTimer = 60;
                this.smsDisable = false;
            },
            sendSms: function () {
                let self = this;
                axios.post(api.userSms, self.registerForm).then(function (res) {
                    self.smsDisable = true;
                    self.smsHandle = setInterval(function () {
                        if (self.smsTimer > 0) {
                            self.smsText = '还有' + self.smsTimer + '秒';
                            self.smsTimer--;
                        } else {
                            self.smsInit();
                        }
                    }, 1000);
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
        width: 60px;
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