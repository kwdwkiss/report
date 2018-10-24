<template>
    <div>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#my-nav" aria-expanded="false">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand collapse-hide" href="javascript:" @click="go('index')">管理后台</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="my-nav">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">数据统计<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="collapse-hide" href="javascript:" @click="go('index')">概况</a>
                                <li><a class="collapse-hide" href="javascript:" @click="go('statementList')">报表</a></li>
                                <li><a class="collapse-hide" href="javascript:"
                                       @click="go('behaviorLogList')">用户行为日志</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">恶意查询<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="collapse-hide" href="javascript:" @click="go('userList')">用户资料</a>
                                <li><a class="collapse-hide" href="javascript:" @click="go('accountList')">账号信息</a></li>
                                <li><a class="collapse-hide" href="javascript:" @click="go('reportList')">举报信息</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">财务信息<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="collapse-hide" href="javascript:" @click="go('rechargeList')">充值记录</a>
                                <li><a class="collapse-hide" href="javascript:" @click="go('amountBillList')">积分记录</a>
                                </li>
                                <li><a class="collapse-hide" href="javascript:" @click="go('searchBillList')">查询汇总</a>
                                </li>
                                <li><a class="collapse-hide" href="javascript:" @click="go('userAuthBillList')">认证记录</a>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">页面设置<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="collapse-hide" href="javascript:" @click="go('siteIndexPage')">首页设置</a>
                                <li><a class="collapse-hide" href="javascript:" @click="go('sitePopWindow')">弹窗设置</a>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">电商工具<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="collapse-hide" href="javascript:" @click="go('vbotJobIndex')">微信清粉</a>
                                </li>
                                <li><a class="collapse-hide" href="javascript:" @click="go('excelIndex')">EXCEL</a>
                                </li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">系统设置<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="collapse-hide" href="javascript:" @click="go('systemSite')">站点设置</a></li>
                                <li><a class="collapse-hide" href="javascript:" @click="go('systemAdmin')">管理员设置</a>
                                </li>
                                <li><a class="collapse-hide" href="javascript:" @click="go('taxonomyList')">分类管理</a>
                                </li>
                                <li><a class="collapse-hide" href="javascript:" @click="go('articleList')">文章管理</a></li>
                                <li><a class="collapse-hide" href="javascript:" @click="go('messageList')">系统通知</a></li>
                                <li><a class="collapse-hide" href="javascript:" @click="go('queueStatus')">队列状态</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">微信设置<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="collapse-hide" href="javascript:" @click="go('wechatServer')">服务器配置</a>
                                </li>
                                <li><a class="collapse-hide" href="javascript:" @click="go('wechatMenu')">菜单管理</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">产品<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="collapse-hide" href="javascript:" @click="go('productIndex')">产品列表</a>
                                </li>
                                <li><a class="collapse-hide" href="javascript:" @click="go('userProductIndex')">用户产品</a></li>
                                <li><a class="collapse-hide" href="javascript:" @click="go('productBillIndex')">产品订单</a></li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">个人中心<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:">账号：{{user.name}}</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a class="collapse-hide" href="javascript:" @click="doLogout">退出</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</template>

<script>
    export default {
        name: "my-nav",
        data: function () {
            return {
                collapseIn: false
            };
        },
        computed: {
            user: function () {
                return this.$store.state.user;
            },
            page: function () {
                return this.$store.state.page;
            },
            unreadNotification: function () {
                return this.$store.state.unreadNotification;
            },
        },
        created: function () {
            if (this.unreadNotification > 0) {
                self.notificationList();
            }
        },
        mounted: function () {
            $('.navbar-collapse').on('click', '.collapse-hide', function () {
                $('.navbar-collapse').collapse('hide');
            });
        },
        methods: {
            doLogout: function () {
                let self = this;
                axios.get(api.logout).then(function () {
                    self.$router.push('/login');
                });
            },
            go: function (name) {
                this.$router.push({name: name});
            },
        }
    };
</script>

<style scoped>
    #my-nav {
        max-height: 100%;
    }
</style>