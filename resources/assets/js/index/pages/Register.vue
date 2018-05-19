<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">注册</div>
            <div class="panel-body">
                <div class="col-md-offset-2 col-md-8">
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
                                <input class="form-control" placeholder="请输入短信验证码" v-model="registerForm.code">
                            </div>
                            <span class="col-sm-3">
                                <my-sms :mobile="registerForm.mobile"></my-sms>
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
                                <a class="btn btn-primary" @click="doRegister">注册</a>
                                &nbsp;
                                <a class="btn btn-success" @click="login">登录</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Register",
        data: function () {
            return {
                registerForm: {
                    remember: true
                }
            }
        },
        methods: {
            redirect: function () {
                if (this.$route.query.redirect_url) {
                    this.$router.push(this.$route.query.redirect_url);
                } else {
                    this.$router.push({name: 'index'});
                }
            },
            doRegister: function () {
                let self = this;
                axios.post(api.userRegister, self.registerForm).then(function (res) {
                    self.$store.commit('user', {
                        callback: function () {
                            self.redirect();
                        }
                    });
                });
            },
            login: function () {
                this.$router.push({name: 'login'});
            }
        }
    }
</script>

<style scoped>

</style>