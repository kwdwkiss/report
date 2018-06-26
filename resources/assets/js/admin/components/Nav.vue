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
                    <a class="navbar-brand collapse-hide" href="javascript:" @click="index">管理后台</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="my-nav">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">数据统计<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="collapse-hide" href="javascript:" @click="index">概况</a>
                                <li><a class="collapse-hide" href="javascript:" @click="statementList">报表</a></li>
                                <li><a class="collapse-hide" href="javascript:" @click="behaviorLogList">用户行为日志</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">恶意查询<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="collapse-hide" href="javascript:" @click="userList">用户资料</a>
                                <li><a class="collapse-hide" href="javascript:" @click="accountList">账号信息</a></li>
                                <li><a class="collapse-hide" href="javascript:" @click="reportList">举报信息</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">财务信息<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="collapse-hide" href="javascript:" @click="rechargeList">充值记录</a>
                                <li><a class="collapse-hide" href="javascript:" @click="amountBillList">积分记录</a></li>
                                <li><a class="collapse-hide" href="javascript:" @click="searchBillList">查询汇总</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">页面设置<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="collapse-hide" href="javascript:" @click="siteIndexPage">首页设置</a>
                                <li><a class="collapse-hide" href="javascript:" @click="sitePopWindow">弹窗设置</a></li>
                                <li><a class="collapse-hide" href="javascript:" @click="messageList">系统通知</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">系统设置<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="collapse-hide" href="javascript:" @click="systemSite">站点设置</a></li>
                                <li><a class="collapse-hide" href="javascript:" @click="systemAdmin">管理员设置</a></li>
                                <li><a class="collapse-hide" href="javascript:" @click="taxonomyList">分类管理</a></li>
                                <li><a class="collapse-hide" href="javascript:" @click="articleList">文章管理</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">微信设置<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="collapse-hide" href="javascript:" @click="wechatServer">服务器配置</a></li>
                                <li><a class="collapse-hide" href="javascript:" @click="wechatMenu">菜单管理</a></li>
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
            index: function () {
                this.$router.push({name: 'index'});
            },
            statementList: function () {
                this.$router.push({name: 'statementList'});
            },
            userList: function () {
                this.$router.push({name: 'userList'});
            },
            accountList: function () {
                this.$router.push({name: 'accountList'});
            },
            reportList: function () {
                this.$router.push({name: 'reportList'});
            },
            rechargeList: function () {
                this.$router.push({name: 'rechargeList'});
            },
            amountBillList: function () {
                this.$router.push({name: 'amountBillList'});
            },
            searchBillList: function () {
                this.$router.push({name: 'searchBillList'});
            },
            siteIndexPage: function () {
                this.$router.push({name: 'siteIndexPage'});
            },
            sitePopWindow: function () {
                this.$router.push({name: 'sitePopWindow'});
            },
            messageList: function () {
                this.$router.push({name: 'messageList'});
            },
            systemSite: function () {
                this.$router.push({name: 'systemSite'});
            },
            systemAdmin: function () {
                this.$router.push({name: 'systemAdmin'});
            },
            taxonomyList: function () {
                this.$router.push({name: 'taxonomyList'});
            },
            articleList: function () {
                this.$router.push({name: 'articleList'});
            },
            wechatServer: function () {
                this.$router.push({name: 'wechatServer'});
            },
            wechatMenu: function () {
                this.$router.push({name: 'wechatMenu'});
            },
            behaviorLogList: function () {
                this.$router.push({name: 'behaviorLogList'});
            }
        }
    };
</script>

<style scoped>
    #my-nav {
        max-height: 100%;
    }
</style>