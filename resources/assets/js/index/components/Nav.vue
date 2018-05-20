<template>
    <div>
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                            data-target="#my-nav" aria-expanded="false">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand collapse-hide" href="javascript:" @click="index">宏海网络</a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="my-nav">
                    <ul class="nav navbar-nav">
                        <li><a data-toggle="collapse" data-target="#my-nav" href="javascript:" @click="index">账号查询</a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                               aria-haspopup="true" aria-expanded="false">文章资讯<span
                                    class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li v-if="item.name" v-for="(item,index) in page.menu" :key="index">
                                    <a data-toggle="collapse" data-target="#my-nav" :href="item.url">{{item.name}}</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <template v-if="!user">
                            <li><a data-toggle="collapse" data-target="#my-nav" href="javascript:" @click="login">登录</a>
                            </li>
                            <li><a data-toggle="collapse" data-target="#my-nav" href="javascript:"
                                   @click="register">注册</a></li>
                        </template>
                        <template v-if="user">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                   aria-haspopup="true" aria-expanded="false">
                                    积分:{{user._profile.amount}}<span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a data-toggle="collapse" data-target="#my-nav" href="javascript:"
                                           @click="recharge">充值</a></li>
                                    <li><a data-toggle="collapse" data-target="#my-nav" href="javascript:"
                                           @click="rechargeList">充值记录</a></li>
                                    <li><a data-toggle="collapse" data-target="#my-nav" href="javascript:"
                                           @click="inviterLink">推广赚积分</a></li>
                                </ul>
                            </li>
                            <li><a data-toggle="collapse" data-target="#my-nav" href="javascript:"
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
                                    <!--<li role="separator" class="divider"></li>-->
                                    <li><a data-toggle="collapse" data-target="#my-nav" href="javascript:"
                                           @click="userModify">用户资料</a></li>
                                    <li><a data-toggle="collapse" data-target="#my-nav" href="javascript:"
                                           @click="userMerchant">店铺资料</a></li>
                                    <li><a data-toggle="collapse" data-target="#my-nav" href="javascript:"
                                           @click="doLogout">退出</a></li>
                                </ul>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</template>

<script>
    export default {
        name: "nav",
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
            let self = this;
            this.$store.commit("unreadNotification", {
                callback: function () {
                    self.notificationList();
                }
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
            }
        }
    };
</script>

<style scoped>
</style>