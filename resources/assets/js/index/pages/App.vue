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

        <pop-window></pop-window>

        <div class="old-root" v-if="user">
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

        <div class="container" v-if="!user" style="padding-top: 51px">
            <div class="row">
                <div data-v-6fe0ffc4="" class="article"><h2 data-v-6fe0ffc4="" class="title">新版直通车关键词优化诀窍——牢记三句金律</h2>
                    <div data-v-6fe0ffc4="" class="article-bar">2018-04-10</div>
                    <hr data-v-6fe0ffc4="">
                    <div data-v-6fe0ffc4=""><p><span style="font-family:宋体">关键词优化是直通车推广中最核心的工作，它直接关系到宝贝的点击率、转换率、</span>ROI<span
                            style="font-family:宋体">等核心指标，宝贝直通车推广的成败在于关键词的优化效果，小黑哥针对直通车关键词优化总结了“三句金律”，并总结了直通车不同推广阶段的优化策略。</span>
                    </p>
                        <p><span style="font-family:宋体"><br></span></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体">本文你将收获的内容：</span></span>
                        </p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体">（</span>1</span><span
                                style="word-wrap: break-word; font-weight: 700;"><span
                                style="font-family:宋体">）、三句金律内容</span></span></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体">（</span>2</span><span
                                style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体">）、直通车不同推广阶段的优化策略</span></span>
                        </p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体">（</span>3</span><span
                                style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体">）、</span>5</span><span
                                style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体">种关键词优化操作及优化原则</span></span>
                        </p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体">（</span>4</span><span
                                style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体">）、直通车推广前期、中期、后期的关键词优化实操技巧</span></span>
                        </p>
                        <p><br></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体"><span
                                style="color:#0000ff">一、三句金律</span></span></span></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="color:red"><span
                                style="font-family:宋体">第一句：三高词（点击率、转化率、</span></span><span style="color:red">ROI</span></span><span
                                style="word-wrap: break-word; font-weight: 700;"><span style="color:red"><span
                                style="font-family:宋体">高）提价，三低词（点击率、转化率低、</span></span><span
                                style="color:red">ROI</span></span><span
                                style="word-wrap: break-word; font-weight: 700;"><span style="color:red"><span
                                style="font-family:宋体">）降价</span></span></span></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="color:red"><span
                                style="font-family:宋体">第二句：三高词（飚升词、高转化词组的相关词、高转换词组的下拉提示词）加词，</span></span><span
                                style="color:red">&nbsp;</span></span><span
                                style="word-wrap: break-word; font-weight: 700;"><span style="color:red"><span
                                style="font-family:宋体">三无词（无展现、无点击、无转换）删词</span></span></span></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="color:red"><span
                                style="font-family:宋体">第三句：匹配调整，精词改泛，泛词改精，大促全泛</span></span></span></p>
                        <p><br></p>
                        <p><br><br></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span
                                style="font-family:宋体">三句金律的说明：</span></span></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span
                                style="font-family:宋体"><br></span></span></p>
                        <p>1<span
                                style="font-family:宋体">、什么样词算高，什么样词算低，需要自己制定一个基准线，比如点击率，一般以整个计划的平均点击率为基准线来优化，转化率和</span>ROI<span
                                style="font-family:宋体">一般根据自己产品的毛利润和点击成本，高于盈利的词为高，低于盈利的词为低。</span></p>
                        <p><br></p>
                        <p>2<span style="font-family:宋体">、三句金律是优化关键词的主线，但并不是一层不变，严格按此执行，有时候具体情况需要具体分析和对待，比如针对点击率，有时候点击率特别高，但是转化效果特别差，这时候需要降低出价以减少损失，针对转化率和</span>ROI<span
                                style="font-family:宋体">，在推广的前期，需要冲销量阶段，哪怕转化率和</span>ROI<span style="font-family:宋体">低到亏损线以下，我们还需要提高出价以冲击更高的销量，所以不同的推广阶段，不同的情况需要特殊对待，灵活运用，在常规情况下一般按这三条金律执行即可。</span>
                        </p>
                        <p><br></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体"><span
                                style="color:#0000ff">二、关键词优化的总体思维策略</span></span></span></p>
                        <p><span style="font-family:宋体">总体优化策略：</span></p>
                        <p><span style="font-family:宋体"><span style="color:#ff0000">前期：以优化点击率为中心</span></span></p>
                        <p><span style="color:#ff0000"></span></p>
                        <p><span style="font-family:宋体"><span style="color:#ff0000">中期：优化转换率为中心</span></span></p>
                        <p><span style="color:#ff0000"></span></p>
                        <p><span style="color:#ff0000"><span style="font-family:宋体">后期：优化</span>ROI<span
                                style="font-family:宋体">为中心</span></span></p>
                        <p><br></p>
                        <p><span style="font-family:宋体">提示：站在直通车的大局下的优化思路与站在整店总产出下的优化思路又是不同的，还需考虑直通车总花费与总产出的渐变关系，寻找利润置高点，后期小黑哥会有深入的分析文章。</span>
                        </p>
                        <p><br></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="color:#0000ff"><span
                                style="font-family:宋体">三、</span>5</span></span><span
                                style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体"><span
                                style="color:#0000ff">种关键词优化操作及优化原则</span></span></span></p>
                        <p><span style="font-family:宋体">关键词的</span>5<span style="font-family:宋体">种优化操作：提价，降价，册词，加词，改词（匹配方式）</span>
                        </p>
                        <p><span style="font-family:宋体"><br></span></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span
                                style="color:#ff0000">1</span></span><span
                                style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体"><span
                                style="color:#ff0000">、提价、降价的优化原则：</span></span></span></p>
                        <p><span style="font-family:宋体">点击率高的词，提高出价，获取更多的精准流量</span></p>
                        <p><span style="font-family:宋体">转换率高的词，提高出价，获取更多的成交</span></p>
                        <p><span style="font-family:宋体">点击率低的词，降低出价，降低点击成本</span></p>
                        <p><span style="font-family:宋体">转换率低的词，降低出价，降低推广成本</span></p>
                        <p><br></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span
                                style="color:#ff0000">2</span></span><span
                                style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体"><span
                                style="color:#ff0000">、删词、加词的优化原则：</span></span></span></p>
                        <p><span style="font-family:宋体">无展现词、无点击词，质量得分低无点击词、无成交词，都删除</span></p>
                        <p><span style="font-family:宋体">飚升词、高转换词组的相关词、高转换词组的下拉提示词，都添加进去</span></p>
                        <p><br></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span
                                style="color:#ff0000">3</span></span><span
                                style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体"><span
                                style="color:#ff0000">、改词（匹配方式）的优化原则：</span></span></span></p>
                        <p><span style="font-family:宋体">直通车前期，建议全部用精准匹配</span></p>
                        <p><span style="font-family:宋体">关键词本身是精准词，建议改成中心匹配或广泛匹配，关键词本身是宽泛词，建议用精准匹配</span></p>
                        <p><span style="font-family:宋体">活动、大促时，比如双</span>11<span style="font-family:宋体">、双</span>12<span
                                style="font-family:宋体">，建议全部改成广泛匹配，全方位引流</span></p>
                        <p><br></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体"><span
                                style="color:#0000ff">四、直通车不同阶段的优化方案</span></span></span></p>
                        <p><br></p>
                        <p><span style="color:#ff0000"><span style="word-wrap: break-word; font-weight: 700;">（一）&nbsp; &nbsp;&nbsp;&nbsp;</span></span><span
                                style="word-wrap: break-word; font-weight: 700;"><span
                                style="color:#ff0000">前期</span></span></p>
                        <p><br></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体"><span
                                style="color:#ff0000">优化原则：优化点击率为主，提高整体精准流量</span></span></span></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span
                                style="font-family:宋体">优化周期：</span>15</span><span
                                style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体">天内</span></span>
                        </p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span
                                style="font-family:宋体"><br></span></span></p>
                        <p><span style="color:#0000ff">1<span style="font-family:宋体">、提高出价</span></span></p>
                        <p><span style="font-family:宋体">低展现、高点击率的词；</span></p>
                        <p><span style="font-family:宋体">低展现、高转化率（高投入产出）的词；</span></p>
                        <p><span style="font-family:宋体">点击率高、展现少的词</span></p>
                        <p><span style="font-family:宋体">质量得分高，点击率低的词</span></p>
                        <p><br></p>
                        <p><span style="color:#0000ff">2<span style="font-family:宋体">、降低出价</span></span></p>
                        <p><span style="font-family:宋体">高展现、低点击率的词；</span></p>
                        <p><span style="font-family:宋体">高展现、低转化率（低投入产出）的词；</span></p>
                        <p><span style="font-family:宋体"><br></span></p>
                        <p><span style="color:#0000ff">3<span style="font-family:宋体">、删除关键词</span></span></p>
                        <p><span style="font-family:宋体">过去</span>7<span style="font-family:宋体">天无展现的关键词；</span></p>
                        <p><span style="font-family:宋体">过去</span>30<span style="font-family:宋体">天无点击的关键词；</span></p>
                        <p><span style="font-family:宋体">低投入产出的词，先压价观察，若投入产出还是很低，则果断删</span></p>
                        <p><br></p>
                        <p><span style="color:#0000ff">4<span style="font-family:宋体">、添加关键词</span></span></p>
                        <p><span style="font-family:宋体">飚升词（</span>TOP<span style="font-family:宋体">与数据魔方）</span></p>
                        <p><span style="font-family:宋体">淘宝首页搜索框下面的推荐词</span></p>
                        <p><span style="font-family:宋体">高转换词组的相关关键词</span></p>
                        <p><br></p>
                        <p><span style="color:#0000ff">5<span style="font-family:宋体">、调整匹配方式</span></span></p>
                        <p><span style="font-family:宋体">前期精准词，中期广泛词，后期根据转换情况</span></p>
                        <p><span style="font-family:宋体">双</span>11<span style="font-family:宋体">、双</span>12<span
                                style="font-family:宋体">活动期间广泛匹配引流</span></p>
                        <p><br></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体"><span
                                style="color:#ff0000">（二）、中期</span></span></span></p>
                        <p><br></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体"><span
                                style="color:#ff0000">优化原则：稳住点击率，以优化转换率为核心</span></span></span></p>
                        <p><br></p>
                        <p><span style="font-family:宋体">分析周期：</span>15<span style="font-family:宋体">天、</span>30<span
                                style="font-family:宋体">天时间段</span></p>
                        <p><span style="font-family:宋体">转换率高的词，赚钱词，提高出价</span></p>
                        <p><span style="font-family:宋体">转换率低的词，亏钱词，降低出价</span></p>
                        <p>15<span style="font-family:宋体">天、</span>30<span
                                style="font-family:宋体">天无转换的词，亏钱词，删除关键词</span></p>
                        <p><br></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="font-family:宋体"><span
                                style="color:#ff0000">（三）、后期</span></span></span></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="color:#ff0000"><span
                                style="font-family:宋体">优化原则：稳住点击率和转化率，以优化</span>ROI<span
                                style="font-family:宋体">为核心</span></span></span></p>
                        <p><span style="word-wrap: break-word; font-weight: 700;"><span style="color:#ff0000"><span
                                style="font-family:宋体"><br></span></span></span></p>
                        <p><span style="font-family:宋体">分析周期：</span>15<span style="font-family:宋体">天、</span>30<span
                                style="font-family:宋体">天</span></p>
                        <p><span style="font-family:宋体">在后期，要以盈利为主，所以要控制好</span>ROI<span style="font-family:宋体">，保证盈利，设定一个</span>ROI<span
                                style="font-family:宋体">值</span></p>
                        <p><span style="font-family:宋体">要实现直通车不亏本，也就是直通车的花费刚好产品的毛利润</span></p>
                        <p><span style="font-family:宋体">所以</span>ROI<span style="font-family:宋体">〉</span>(<span
                                style="font-family:宋体">客单价</span>/<span style="font-family:宋体">毛利润</span>)&nbsp;<span
                                style="font-family:宋体">才能实现直通车的盈利。</span></p>
                        <p><br></p>
                        <p>ROI<span style="font-family:宋体">〉</span>(<span style="font-family:宋体">客单价</span>/<span
                                style="font-family:宋体">毛利润</span>)<span style="font-family:宋体">的词，针对盈利词，提高出价</span></p>
                        <p>ROI<span style="font-family:宋体">〈</span>(<span style="font-family:宋体">客单价</span>/<span
                                style="font-family:宋体">毛利润</span>)<span style="font-family:宋体">的词，亏本词，降低出价</span></p>
                        <p>ROI<span style="font-family:宋体">＝</span>0<span style="font-family:宋体">的词，无成交词，删除关键词</span>
                        </p>
                        <p><br></p></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import PopWindow from '../components/PopWindow'
    import cityData from '../../city.json'

    export default {
        components: {PopWindow},
        name: "app",
        data: function () {
            return {
                smsForm: {},
                smsText: '发送短信',
                smsDisable: false,
                smsTimer: 60,
                smsHandle: null,
                registerForm: {},
                loginForm: {},
                forgetPasswordForm: {},
                rechargeForm: {mount: '',},
                notificationItem: {data: {}},
                userModifyForm: {_profile: {}},
                provinces: Object.keys(cityData),
                cities: [],
                userMerchantForm: {}
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
            initCities: function (province) {
                this.cities = Object.keys(_.get(cityData, province, []));
            },
            provinceSelect: function (event) {
                this.initCities(event.target.value);
                this.userModifyForm._profile.city = this.cities[0];
            },
            doLogout: function () {
                let self = this;
                axios.get(api.userLogout).then(function (res) {
                    self.$store.commit('user');
                });
            },
            login: function () {
                $(".modal").modal('hide');
                $(".login-dialog").modal('show');
                this.loginForm = {
                    mobile: '',
                    password: '',
                    remember: true
                };
            },
            doLogin: function () {
                let self = this;
                axios.post(api.userLogin, self.loginForm).then(function (res) {
                    self.$message.success('成功');
                    $(".modal").modal('hide');
                    self.$store.commit('user');
                });
            },
            register: function () {
                $(".modal").modal('hide');
                $(".register-dialog").modal('show');
                this.registerForm = {
                    mobile: '',
                    password: '',
                    inviter: '',
                    code: '',
                    remember: true
                };
                this.smsForm = this.registerForm;
                this.smsInit();
            },
            doRegister: function () {
                let self = this;
                axios.post(api.userRegister, self.registerForm).then(function (res) {
                    self.$message.success('成功');
                    $(".modal").modal('hide');
                    self.$store.commit('user');
                });
            },
            forgetPassword: function () {
                $(".modal").modal('hide');
                $(".forget-password-dialog").modal('show');
                this.forgetPasswordForm = {
                    mobile: '',
                    password: '',
                    code: '',
                    remember: true
                };
                this.smsForm = this.forgetPasswordForm;
                this.smsInit();
            },
            doForgetPassword: function () {
                let self = this;
                axios.post(api.userForgetPassword, self.forgetPasswordForm).then(function (res) {
                    self.$message.success('成功');
                    $(".modal").modal('hide');
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
                axios.post(api.userSms, {mobile: self.smsForm.mobile}).then(function (res) {
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
            userModify: function () {
                this.initCities(this.user._profile.province);
                this.userModifyForm = _.cloneDeep(this.$store.state.user);
                $(".user-modify-dialog").modal('show');
            },
            doUserModify: function () {
                let self = this;
                axios.post(api.userModify, self.userModifyForm).then(function (res) {
                    self.$message.success('成功');
                    self.$store.commit('user');
                    $(".user-modify-dialog").modal('hide');
                });
            },
            userMerchantModify: function () {
                this.userMerchantForm = _.assign({}, _.cloneDeep(this.$store.state.user._merchant));
                $(".user-merchant-modify-dialog").modal('show');
            },
            doUserMerchantModify: function () {
                let self = this;
                axios.post(api.userMerchantModify, self.userMerchantForm).then(function (res) {
                    self.$message.success('成功');
                    self.$store.commit('user');
                    $(".user-merchant-modify-dialog").modal('hide');
                });
            }
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