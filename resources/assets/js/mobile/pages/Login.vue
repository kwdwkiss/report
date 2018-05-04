<template>
    <div>
        <mt-header title="登录">
            <router-link to="/" slot="left">
                <mt-button icon="back" @click="goBack">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="row">
            <div class="col-xs-12">手机号</div>
            <div class="col-xs-12">
                <input class="form-control" type="text" placeholder="请输入手机号" v-model="loginForm.mobile">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">密码</div>
            <div class="col-xs-12">
                <input class="form-control" type="password" placeholder="请输入密码" v-model="loginForm.password">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <label>
                    <input type="checkbox" v-model="loginForm.remember"> 记住我
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <button class="btn btn-success col-xs-12" @click="login">登录</button>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12" style="text-align: center">
                <a href="javascript:" @click="forgetPassword">忘记密码</a>
                |
                <a href="javascript:" @click="register">注册</a>
            </div>
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
        methods: {
            goBack: function () {
                this.$router.go(-1);
            },
            login: function () {
                let self = this;
                axios.post(api.userLogin, self.loginForm).then(function (res) {
                    self.$store.commit('user', {
                        callback: function () {
                            self.$router.push('/user');
                        }
                    });
                });
            },
            forgetPassword: function () {
                this.$router.push('/forget/password');
            },
            register: function () {
                this.$router.push('/register');
            }
        }
    }
</script>

<style scoped>

</style>