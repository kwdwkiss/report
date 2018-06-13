<template>
    <div>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#my-nav" aria-expanded="false" style="padding: 5px 10px">
                        <span style="color: #fff;">菜单</span>
                        <!--<span class="icon-bar"></span>-->
                        <!--<span class="icon-bar"></span>-->
                        <!--<span class="icon-bar"></span>-->
                    </button>
                    <a class="navbar-brand collapse-hide" href="javascript:" @click="index">宏海网络</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="my-nav">
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">电商工具<span
                                    class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="collapse-hide" href="javascript:" @click="index">恶意账号查询</a>
                                <li><a class="collapse-hide" href="javascript:" @click="checkTb">淘宝验号</a></li>
                                <li><a class="collapse-hide" href="javascript:" @click="checkPdd">拼多多验号</a></li>
                                <!--<li><a class="collapse-hide" href="javascript:" @click="checkJd">京东验号</a></li>-->
                                <li><a class="collapse-hide" href="javascript:" @click="searchOrder">淘宝信用查询</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">文章资讯<span
                                    class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li v-if="item.name" v-for="(item,index) in page.menu" :key="index">
                                    <a class="collapse-hide" :href="item.url">{{item.name}}</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <template v-if="!user">
                            <li><a class="collapse-hide" href="javascript:" @click="login">登录</a></li>
                            <li><a class="collapse-hide" href="javascript:" @click="register">注册</a></li>
                        </template>
                        <template v-if="user">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">积分充值<span
                                        class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:">积分:{{user._profile.amount}}</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a class="collapse-hide" href="javascript:" @click="recharge">积分充值</a></li>
                                    <li><a class="collapse-hide" href="javascript:" @click="rechargeList">充值记录</a></li>
                                    <li><a class="collapse-hide" href="javascript:" @click="amountList">积分记录</a></li>
                                    <li><a class="collapse-hide" href="javascript:" @click="inviterLink">推广赚积分</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">个人中心<span
                                        class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="javascript:">账号：{{user.mobile}}</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a class="collapse-hide" href="javascript:" @click="userModify">用户资料</a></li>
                                    <li><a class="collapse-hide" href="javascript:" @click="userMerchant">店铺资料</a></li>
                                    <li>
                                        <a class="collapse-hide" href="javascript:" @click="notificationList">
                                            系统通知
                                            <template v-if="unreadNotification>0">
                                                （{{unreadNotification}}）
                                            </template>
                                        </a>
                                    </li>
                                    <li><a class="collapse-hide" href="javascript:" @click="doLogout">退出</a></li>
                                </ul>
                            </li>
                        </template>
                        <li><a class="collapse-hide" href="javascript:" @click="customerService">联系客服</a></li>
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
                axios.get(api.userLogout).then(function (res) {
                    self.$store.commit('user', {
                        callback: function () {
                            self.$router.go(0);
                        }
                    });
                });
            },
            index: function () {
                this.$router.push({name: 'index'});
            },
            login: function () {
                this.$router.push({name: 'login'});
            },
            register: function () {
                this.$router.push({name: 'register'});
            },
            notificationList: function () {
                this.$router.push({name: 'notificationList'});
            },
            rechargeList: function () {
                this.$router.push({name: 'rechargeList'});
            },
            amountList: function () {
                this.$router.push({name: 'amountList'});
            },
            recharge: function () {
                this.$router.push({name: 'recharge'});
            },
            userModify: function () {
                this.$router.push({name: 'userProfile'});
            },
            userMerchant: function () {
                this.$router.push({name: 'userMerchant'});
            },
            inviterLink: function () {
                this.$router.push({name: 'inviterLink'});
            },
            checkTb: function () {
                this.$router.push({name: 'checkTb'});
            },
            checkPdd: function () {
                this.$router.push({name: 'checkPdd'});
            },
            checkJd: function () {
                this.$router.push({name: 'checkJd'});
            },
            customerService: function () {
                this.$router.push({name: 'customerService'});
            },
            searchOrder: function () {
                this.$router.push({name: 'searchOrder'});
            }
        }
    };
</script>

<style scoped>
    #my-nav {
        max-height: 100%;
    }
</style>