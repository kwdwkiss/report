<template>
    <div>
        <div class="row">
            <div class="panel panel-default">
                <div class="panel-heading">登录</div>
                <div class="panel-body">
                    <div class="col-md-offset-2 col-md-8">
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
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row hidden-md hidden-lg hidden-sm">
            <a :href="page.mobile_ad.login.url">
                <img :src="page.mobile_ad.login.img_src" style="width: 100%;max-height: 100px">
            </a>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Login",
        data: function () {
            return {
                loginForm: {
                    remember: true
                }
            }
        },
        computed: {
            page: function () {
                return this.$store.state.page;
            },
        },
        methods: {
            redirect: function () {
                if (this.$route.query.redirect_url) {
                    this.$router.push(this.$route.query.redirect_url);
                } else {
                    this.$router.push({name: 'index'});
                }
            },
            doLogin: function () {
                let self = this;
                axios.post(api.userLogin, self.loginForm).then(function (res) {
                    self.$store.commit('user', {
                        callback: function () {
                            self.redirect();
                        }
                    });
                });
            },
            forgetPassword: function () {
                this.$router.push({name: 'forgetPassword'});
            },
        }
    }
</script>

<style scoped>

</style>