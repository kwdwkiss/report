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
                               aria-haspopup="true" aria-expanded="false">恶意查询<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a class="collapse-hide" href="javascript:" @click="go('userList')">用户资料</a>
                                <li><a class="collapse-hide" href="javascript:" @click="go('reportList')">举报信息</a></li>
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
                axios.get(api.agentAgentLogout).then(function () {
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