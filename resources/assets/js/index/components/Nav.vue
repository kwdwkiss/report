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
                <a class="navbar-brand" href="/#/">宏海网络</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li><a href="/#/">账号查询</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                            aria-haspopup="true" aria-expanded="false">文章资讯<span
                                class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li v-for="(item,index) in page.menu" :key="index"><a :href="item.url">{{item.name}}</a>
                            </li>
                        </ul>
                    </li>
                    <!--<li><a href="#">电商导航</a></li>-->
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
                                <li><a href="javascript:" @click="userModify">个人资料</a></li>
                                <li><a href="javascript:" @click="userMerchantModify">店铺资料</a></li>
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
                                        <span>1、1元=100积分，最低充值金额1元，请用支付宝扫描下方二维码，<span class="text-danger">备注填写注册手机号：{{user.mobile}}（否则不能自动到账）</span></span>
                                    </div>
                                    <div class="row">
                                        <span>2、完成支付宝转账后，请点击充值完成按钮，耐心等待积分到账</span>
                                    </div>
                                    <div class="row">
                                        <span>3、若5分钟后未能到账，或忘记备注手机号，请联系充值客服，微信号：ywh171337832</span>
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
                            <tr v-for="(item, index) in recharge.data" :key="index">
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
                            <tr v-for="(item, index) in notification.data" :key="index">
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
                        <h4 class="modal-title">登录</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
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
                                        <a href="javascript:" @click="forgetPassword">忘记密码</a>
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
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade register-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">注册</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
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
                                           placeholder="填写邀请人注册手机号，若无则不用填写" name="inviter"
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

        <div class="modal fade forget-password-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">忘记密码</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">手机号</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入手机号"
                                           name="mobile"
                                           v-model="forgetPasswordForm.mobile">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">新密码</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control"
                                           placeholder="密码必须包含字母、数字、符号两种组合且长度为8-16" name="password"
                                           v-model="forgetPasswordForm.password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">短信验证码</label>
                                <div class="col-sm-6">
                                    <input class="form-control" placeholder="请输入短信验证码"
                                           v-model="forgetPasswordForm.code">
                                </div>
                                <span class="col-sm-3">
                                    <button type="button" class="btn btn-success" @click="sendSms"
                                            v-bind:disabled="smsDisable">{{smsText}}</button>
                                </span>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <label>
                                        <input type="checkbox" v-model="forgetPasswordForm.remember"> 记住我
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <a class="btn btn-primary" @click="doForgetPassword">确认</a>
                                    &nbsp;
                                    <a class="btn btn-success" @click="login">返回登录</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade user-modify-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">个人资料</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">会员类型</label>
                                <div class="col-sm-9">
                                    <label class="control-label pull-left">{{userModifyForm.type_label}}</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">QQ</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入QQ号"
                                           v-model="userModifyForm.qq">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">微信</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入微信号"
                                           v-model="userModifyForm.wx">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">旺旺</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入旺旺号"
                                           v-model="userModifyForm.ww">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">支付宝</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入支付宝"
                                           v-model="userModifyForm._profile.alipay">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">姓名</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入姓名"
                                           v-model="userModifyForm._profile.name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">年龄</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入年龄"
                                           v-model="userModifyForm._profile.age">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">性别</label>
                                <div class="col-sm-9">
                                    <select class="form-control" v-model="userModifyForm._profile.gender">
                                        <option value="0">未知</option>
                                        <option value="1">男</option>
                                        <option value="2">女</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">职业</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入职业"
                                           name="mobile"
                                           v-model="userModifyForm._profile.occupation">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">省</label>
                                <div class="col-sm-9">
                                    <select class="form-control" v-model="userModifyForm._profile.province"
                                            @change="provinceSelect">
                                        <option v-for="item in provinces" :key="item" :value="item">{{item}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">市</label>
                                <div class="col-sm-9">
                                    <select class="form-control" v-model="userModifyForm._profile.city">
                                        <option v-for="item in cities" :key="item" :value="item">{{item}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <label v-if="userModifyForm._profile.user_lock"
                                           class="text-warning">个人资料锁定，修改请联系客服</label>
                                    <a v-if="!userModifyForm._profile.user_lock" class="btn btn-success"
                                       @click="doUserModify">提交</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade user-merchant-modify-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            &times;
                        </button>
                        <h4 class="modal-title">商户资料</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" role="form">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">店铺类型</label>
                                <div class="col-sm-9">
                                    <select class="form-control" v-model="userMerchantForm.type">
                                        <option value="1">天猫店</option>
                                        <option value="2">企业淘宝店</option>
                                        <option value="3">个人淘宝店</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">店铺名称</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入店铺名称"
                                           v-model="userMerchantForm.name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">商品类型</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入商品类型"
                                           v-model="userMerchantForm.goods_type">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">店铺网址</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入店铺网址"
                                           v-model="userMerchantForm.url">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">店铺信誉</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="个人淘宝店才需要填写"
                                           v-model="userMerchantForm.credit">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">掌柜旺旺</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" placeholder="请输入掌柜旺旺号"
                                           v-model="userMerchantForm.manager">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <label v-if="userMerchantForm.user_lock" class="text-warning">店铺资料锁定，修改请联系客服</label>
                                    <a v-if="!userMerchantForm.user_lock" class="btn btn-success"
                                       @click="doUserMerchantModify">提交</a>
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
import cityData from "../../city.json";
export default {
  name: "nav",
  data: function() {
    return {
      smsForm: {},
      smsText: "发送短信",
      smsDisable: false,
      smsTimer: 60,
      smsHandle: null,
      registerForm: {},
      loginForm: {},
      forgetPasswordForm: {},
      rechargeForm: { mount: "" },
      notificationItem: { data: {} },
      userModifyForm: { _profile: {} },
      provinces: Object.keys(cityData),
      cities: [],
      userMerchantForm: {}
    };
  },
  computed: {
    user: function() {
      return this.$store.state.user;
    },
    page: function() {
      return this.$store.state.page;
    },
    unreadNotification: function() {
      return this.$store.state.unreadNotification;
    },
    notification: function() {
      return this.$store.state.notification;
    },
    recharge: function() {
      return this.$store.state.recharge;
    }
  },
  created: function() {
    let self = this;
    this.$store.commit("unreadNotification", {
      callback: function() {
        self.notificationList();
      }
    });
  },
  methods: {
    initCities: function(province) {
      this.cities = Object.keys(_.get(cityData, province, []));
    },
    provinceSelect: function(event) {
      this.initCities(event.target.value);
      this.userModifyForm._profile.city = this.cities[0];
    },
    doLogout: function() {
      let self = this;
      axios.get(api.userLogout).then(function(res) {
        self.$store.commit("user", {
          callback: function() {
            self.$router.go(0);
          }
        });
      });
    },
    login: function() {
      $(".modal").modal("hide");
      $(".login-dialog").modal("show");
      this.loginForm = {
        mobile: "",
        password: "",
        remember: true
      };
    },
    doLogin: function() {
      let self = this;
      axios.post(api.userLogin, self.loginForm).then(function(res) {
        self.$message.success("成功");
        $(".modal").modal("hide");
        self.$store.commit("user", {
          callback: function() {
            self.$router.push("/");
          }
        });
      });
    },
    register: function() {
      $(".modal").modal("hide");
      $(".register-dialog").modal("show");
      this.registerForm = {
        mobile: "",
        password: "",
        inviter: "",
        code: "",
        remember: true
      };
      this.smsForm = this.registerForm;
      this.smsInit();
    },
    doRegister: function() {
      let self = this;
      axios.post(api.userRegister, self.registerForm).then(function(res) {
        self.$message.success("成功");
        $(".modal").modal("hide");
        self.$store.commit("user");
      });
    },
    forgetPassword: function() {
      $(".modal").modal("hide");
      $(".forget-password-dialog").modal("show");
      this.forgetPasswordForm = {
        mobile: "",
        password: "",
        code: "",
        remember: true
      };
      this.smsForm = this.forgetPasswordForm;
      this.smsInit();
    },
    doForgetPassword: function() {
      let self = this;
      axios
        .post(api.userForgetPassword, self.forgetPasswordForm)
        .then(function(res) {
          self.$message.success("成功");
          $(".modal").modal("hide");
          self.$store.commit("user");
        });
    },
    smsInit: function() {
      clearInterval(this.smsHandle);
      this.smsText = "发送短信";
      this.smsTimer = 60;
      this.smsDisable = false;
    },
    sendSms: function() {
      let self = this;
      axios
        .post(api.userSms, { mobile: self.smsForm.mobile })
        .then(function(res) {
          self.$message.success("短信发送成功，请耐心等候，如有疑问请联系客服");
          self.smsDisable = true;
          self.smsHandle = setInterval(function() {
            if (self.smsTimer > 0) {
              self.smsText = "还有" + self.smsTimer + "秒";
              self.smsTimer--;
            } else {
              self.smsInit();
            }
          }, 1000);
        });
    },
    notificationPaginate: function(page) {
      this.$store.commit("notification", { page: page });
    },
    notificationList: function() {
      $(".notification-dialog").modal("show");
      this.$store.commit("notification");
    },
    notificationDetail: function(item) {
      $(".notification-detail-dialog").modal("show");
      this.notificationItem = item;

      let self = this;
      axios.post(api.userReadNotification, item).then(function(res) {
        self.$store.commit("notification");
        self.$store.commit("unreadNotification");
      });
    },
    rechargeDialog: function() {
      $(".recharge-dialog").modal("show");
    },
    doRecharge: function() {
      $(".recharge-dialog").modal("hide");
      let self = this;
      let count = 5;
      self.$store.commit("user");
      let handle = setInterval(function() {
        if (count <= 0) {
          clearInterval(handle);
        } else {
          self.$store.commit("user");
          count--;
        }
      }, 10000);
    },
    rechargeList: function() {
      $(".recharge-list-dialog").modal("show");
      this.$store.commit("recharge");
    },
    rechargePaginate: function() {},
    userModify: function() {
      this.initCities(this.user._profile.province);
      this.userModifyForm = _.cloneDeep(this.$store.state.user);
      $(".user-modify-dialog").modal("show");
    },
    doUserModify: function() {
      let self = this;
      axios.post(api.userModify, self.userModifyForm).then(function(res) {
        self.$message.success("成功");
        self.$store.commit("user");
        $(".user-modify-dialog").modal("hide");
      });
    },
    userMerchantModify: function() {
      this.userMerchantForm = _.assign(
        {},
        _.cloneDeep(this.$store.state.user._merchant)
      );
      $(".user-merchant-modify-dialog").modal("show");
    },
    doUserMerchantModify: function() {
      let self = this;
      axios
        .post(api.userMerchantModify, self.userMerchantForm)
        .then(function(res) {
          self.$message.success("成功");
          self.$store.commit("user");
          $(".user-merchant-modify-dialog").modal("hide");
        });
    }
  }
};
</script>

<style scoped>
</style>