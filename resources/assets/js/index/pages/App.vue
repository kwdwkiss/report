<template>
    <div>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">宏海网络</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse in" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <!--<li><a href="#">账号查询</a></li>-->
                        <!--<li><a href="#">电商导航</a></li>-->
                        <!--<li><a href="#">网络兼职</a></li>-->
                        <!--<li><a href="#">电商干货</a></li>-->
                        <!--<li><a href="#">电商服务</a></li>-->
                        <!--<li><a href="#">关于我们</a></li>-->
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <template v-if="!user">
                            <li><a href="javascript:" @click="login">登录</a></li>
                            <li><a href="javascript:" @click="register">注册</a></li>
                        </template>
                        <template v-if="user">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">积分:{{user._profile.amount}}<span
                                        class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:" @click="rechargeDialog">充值</a></li>
                                    <li><a href="javascript:" @click="rechargeList">充值记录</a></li>
                                </ul>
                            </li>
                            <li><a href="javascript:"
                                   @click="notificationList">通知
                                <template v-if="unreadNotification.meta.total">
                                    （未读{{unreadNotification.meta.total}}）
                                </template>
                            </a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">{{user.mobile}} <span
                                        class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <!--<li><a href="#">Action</a></li>-->
                                    <!--<li role="separator" class="divider"></li>-->
                                    <li><a href="javascript:" @click="doLogout">注销</a></li>
                                </ul>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </nav>

        <div v-if="user" class="modal fade recharge-dialog" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">积分充值</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">充值说明</label>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <span>1、1元=100积分，最低充值金额1元，请用支付宝扫描下方二维码，<span class="text-danger">备注填写注册手机号：{{user.mobile}}（否则不能自动到账）。</span></span>
                                    </div>
                                    <div class="row">
                                        <span>2、完成支付宝转账后，请点击充值完成按钮，耐心等待积分到账。</span>
                                    </div>
                                    <div class="row">
                                        <span>3、若5分钟后未能到账，或忘记备注手机号，请联系充值客服，微信号：ywh171337832。</span>
                                    </div>
                                    <div class="row">
                                        <img style="width: 200px" src="/images/tbpzw_alipay.png" alt="">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <a class="btn btn-success" @click="doRecharge">充值完成</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="user" class="modal fade recharge-list-dialog" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" style="width: 850px">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">充值记录</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-responsive table-striped">
                            <thead>
                            <tr>
                                <th style="width: 130px">系统订单号</th>
                                <th style="width: 90px">支付类型</th>
                                <th style="width: 260px">外部订单号</th>
                                <th style="width: 100px">金额（元）</th>
                                <th style="width: 70px">状态</th>
                                <th style="width: 180px">创建时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in recharge.data">
                                <td>{{item.bill_no}}</td>
                                <td>{{item.pay_type_label}}</td>
                                <td>{{item.pay_no}}</td>
                                <td>{{item.money}}</td>
                                <td>{{item.status_label}}</td>
                                <td>{{item.created_at}}</td>
                            </tr>
                            </tbody>
                        </table>
                        <el-pagination layout="prev, pager, next"
                                       :total="recharge.meta.total"
                                       :page-size="recharge.meta.per_page"
                                       @current-change="rechargePaginate"></el-pagination>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade notification-dialog" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">系统通知</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-responsive table-striped">
                            <thead>
                            <tr>
                                <th style="width: 60%">通知</th>
                                <th style="width: 10%">查阅</th>
                                <th style="width: 30%">时间</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in notification.data">
                                <td><a href="javascript:" @click="notificationDetail(item)">{{item.data.name}}</a></td>
                                <td>
                                    <template v-if="item.read_at">
                                        已读
                                    </template>
                                    <template v-else>
                                        未读
                                    </template>
                                </td>
                                <td>{{item.created_at}}</td>
                            </tr>
                            </tbody>
                        </table>
                        <el-pagination layout="prev, pager, next"
                                       :total="notification.meta.total"
                                       :page-size="notification.meta.per_page"
                                       @current-change="notificationPaginate"></el-pagination>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade notification-detail-dialog" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">通知详情</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <h4 class="text-center">{{notificationItem.data.name}}</h4>
                        </div>
                        <div class="row">
                            {{notificationItem.data.content}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade login-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">{{dialogTitle}}</h4>
                    </div>
                    <div class="modal-body">
                        <form v-if="loginStatus=='login'" class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">手机号</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入手机号"
                                           name="mobile"
                                           v-model="loginForm.mobile">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">密码</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" placeholder="请输入密码"
                                           name="password"
                                           v-model="loginForm.password">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" v-model="loginForm.remember"> 记住我
                                        </label>
                                        &nbsp;
                                        <a href="javascript:" @click="register">忘记密码</a>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <a class="btn btn-success" @click="doLogin">登录</a>
                                    &nbsp;
                                    <a class="btn btn-primary" @click="register">注册</a>
                                </div>
                            </div>
                        </form>
                        <form v-if="loginStatus=='register'" class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">手机号</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入手机号"
                                           name="mobile"
                                           v-model="registerForm.mobile">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">新密码</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control"
                                           placeholder="密码必须包含字母、数字、符号两种组合且长度为8-16" name="password"
                                           v-model="registerForm.password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">邀请人</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control"
                                           placeholder="填写邀请人注册手机号" name="inviter"
                                           v-model="registerForm.inviter">
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
                                    <label>
                                        <input type="checkbox" v-model="registerForm.remember"> 记住我
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <a class="btn btn-primary" @click="doRegister">确认</a>
                                    &nbsp;
                                    <a class="btn btn-success" @click="login">返回登录</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="old-root">
            <pop-window></pop-window>

            <div class="row hidden-xs hidden-sm ad">
                <div class="col-md-6" v-for="item in page.ad_top">
                    <a target="_blank" :href="item.url">
                        <img :src="item.img_src">
                    </a>
                </div>
            </div>
            <div class="row hidden-xs hidden-sm ad">
                <div class="col-md-3" v-for="item in page.ad_second">
                    <a target="_blank" :href="item.url">
                        <img :src="item.img_src">
                    </a>
                </div>
            </div>

            <div class="row logo">
                <div class="col-xs-6">
                    <a href="">
                        <img src="/images/logo.jpg">
                    </a>
                </div>
                <div class="col-xs-6">
                    <div class="service-qq">
                        <ul>
                            <li class="col-xs-12">QQ客服：</li>
                            <li class="col-xs-6" v-for="item in page.service_qq">{{item.name}}</li>
                        </ul>
                    </div>
                    <div class="service-wx">
                        <ul>
                            <li>微信<br/>客服：</li>
                            <li v-for="item in page.service_wx">
                                <img :src="item.name" alt="">
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row old-nav">
                <ul>
                    <a :href="item.url" v-for="item in page.menu">
                        <li>{{item.name}}</li>
                    </a>
                </ul>
            </div>

            <div class="row notice">
                <span>公告：{{page.notice.title}}</span>
                <span><a :href="page.notice.moreUrl">更多</a></span>
            </div>

            <router-view></router-view>

            <div class="row hidden-xs hidden-sm ad">
                <div class="col-xs-3" v-for="item in page.ad_foot">
                    <a target="_blank" :href="item.url">
                        <img :src="item.img_src">
                    </a>
                </div>
            </div>

            <hr>

            <div class="row copyright">
                <div>
                    <p>
                        <a href="http://www.cnzz.com/stat/website.php?web_id=1271314784" target="_blank"
                           title="站长统计">站长统计</a>
                        |
                        Copyright©2015-2020 www.tbpzw.com .All Rights Reserved ICP证：桂ICP备14007039号
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import PopWindow from '../components/PopWindow'

    export default {
        components: {PopWindow},
        name: "app",
        data: function () {
            return {
                dialogTitle: '登录',
                smsText: '发送短信',
                smsDisable: false,
                smsTimer: 60,
                smsHandle: null,
                loginStatus: null,
                registerForm: {
                    mobile: '',
                    password: '',
                    code: '',
                    remember: true
                },
                loginForm: {
                    mobile: '',
                    password: '',
                    remember: true
                },
                rechargeForm: {
                    mount: '',
                },
                notificationItem: {data: {}}
            }
        },
        computed: {
            page: function () {
                return this.$store.state.page;
            },
            user: function () {
                return this.$store.state.user;
            },
            unreadNotification: function () {
                return this.$store.state.unreadNotification;
            },
            notification: function () {
                return this.$store.state.notification;
            },
            recharge: function () {
                return this.$store.state.recharge;
            }
        },
        created: function () {
            let self = this;
            this.$store.commit('unreadNotification', {
                callback: function () {
                    self.notificationList();
                }
            });
        },
        methods: {
            login: function () {
                $(".login-dialog").modal('show');
                this.loginStatus = 'login';
                this.dialogTitle = '登录';
            },
            doLogin: function () {
                let self = this;
                axios.post(api.userLogin, self.loginForm).then(function (res) {
                    self.$store.commit('user');
                    $(".login-dialog").modal('hide');
                });
            },
            doLogout: function () {
                let self = this;
                axios.get(api.userLogout).then(function (res) {
                    self.$store.commit('user');
                });
            },
            register: function () {
                $(".login-dialog").modal('show');
                this.loginStatus = 'register';
                this.dialogTitle = '注册&找回密码';
                this.registerInit();
            },
            registerInit: function () {
                this.registerForm.mobile = '';
                this.registerForm.password = '';
                this.registerForm.code = '';
                this.smsInit();
            },
            doRegister: function () {
                let self = this;
                axios.post(api.userRegister, self.registerForm).then(function (res) {
                    self.$message.success('成功');
                    $(".login-dialog").modal('hide');
                    self.$store.commit('user');
                });
            },
            smsInit: function () {
                clearInterval(this.smsHandle);
                this.smsText = '发送短信';
                this.smsTimer = 60;
                this.smsDisable = false;
            },
            sendSms: function () {
                let self = this;
                axios.post(api.userSms, self.registerForm).then(function (res) {
                    self.$message.success('短信发送成功，请耐心等候，如有疑问请联系客服');
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
            },
            notificationPaginate: function (page) {
                this.$store.commit('notification', {page: page});
            },
            notificationList: function () {
                $('.notification-dialog').modal('show');
                this.$store.commit('notification');
            },
            notificationDetail: function (item) {
                $('.notification-detail-dialog').modal('show');
                this.notificationItem = item;

                let self = this;
                axios.post(api.userReadNotification, item).then(function (res) {
                    self.$store.commit('notification');
                    self.$store.commit('unreadNotification');
                });
            },
            rechargeDialog: function () {
                $('.recharge-dialog').modal('show');
            },
            doRecharge: function () {
                $('.recharge-dialog').modal('hide');
                let self = this;
                let count = 5;
                self.$store.commit('user');
                let handle = setInterval(function () {
                    if (count <= 0) {
                        clearInterval(handle);
                    } else {
                        self.$store.commit('user');
                        count--;
                    }
                }, 10000);
            },
            rechargeList: function () {
                $('.recharge-list-dialog').modal('show');
                this.$store.commit('recharge');
            },
            rechargePaginate: function () {

            },
        }
    }
</script>

<style>
    body {
        margin: 0 auto;
        width: 970px;
        min-height: 400px;
        background-color: #fff;
        color: #222
    }

    .old-root {
        padding-top: 51px;
    }

    .old-root ul {
        padding-left: 0;
        margin-bottom: 0;
    }

    .old-root li {
        list-style: none;
    }

    .old-root th, .old-root td {
        text-align: center;
    }

    .old-root p {
        margin-bottom: 0;
    }

    .old-root input {
        line-height: normal;
    }

    .old-root a {
        color: #222;
    }

    .row {
        margin: 10px 0;
    }

    .row > div {
        padding: 0 5px;
    }

    .ad img, .logo img {
        width: 100%;
        height: 80px;
    }

    .old-nav {
        background-color: #e6e6e6;
        color: #505050;
        line-height: 40px;
        font-size: 16px;
        font-weight: 600;
    }

    .old-nav li {
        display: inline-block;
        float: left;
        padding: 0 35px;
    }

    .old-nav li:hover {
        background-color: #41A51D;
        color: #fff;
    }

    .notice {
        font-size: 16px;
        font-weight: 600;
        color: red;
    }

    .service-qq {
        float: right;
        width: 210px;
        color: green;
        font-size: 16px;
        font-weight: 600;
    }

    .service-wx {
        float: right;
        width: 250px;
        color: green;
        font-size: 16px;
        font-weight: 600;
    }

    .service-wx li {
        margin: 0 5px;
        float: left;
    }

    .service-wx img {
        height: 80px;
        width: 80px;
    }

    .copyright {
        text-align: center;
    }
</style>