<template>
    <el-form :model="form" :rules="rules" ref="form" class="form-container">
        <h3 class="title">系统登录</h3>
        <el-form-item prop="username">
            <el-input type="text" name="username" v-model="form.username" placeholder="账号"></el-input>
        </el-form-item>
        <el-form-item prop="password">
            <el-input type="password" name="password" v-model="form.password" placeholder="密码"></el-input>
        </el-form-item>
        <el-form-item prop="auth_code">
            <el-input type="text" v-model="form.auth_code" placeholder="授权码"></el-input>
        </el-form-item>
        <!--<el-checkbox v-model="form.remember" class="remember">记住密码</el-checkbox>-->
        <el-form-item>
            <el-button type="primary" @click.native.prevent="login" :loading="loading">登录</el-button>
        </el-form-item>
    </el-form>
</template>

<script>
    export default {
        data() {
            return {
                loading: false,
                form: {
                    username: '',
                    password: '',
                    auth_code: '',
                    remember: true,
                },
                rules: {
                    username: [
                        {required: true, message: '请输入账号', trigger: 'blur'},
                    ],
                    password: [
                        {required: true, message: '请输入密码', trigger: 'blur'},
                    ]
                },
                checked: true
            };
        },
        methods: {
            login() {
                let self = this;
                this.$refs.form.validate((valid) => {
                    if (valid) {
                        self.loading = true;
                        axios.post(api.login, self.form).then(function (res) {
                            self.loading = false;
                            self.$store.commit('user', {
                                callback: function () {
                                    self.$router.push('/');
                                }
                            });
                        }).catch(function () {
                            self.loading = false;
                        });
                    }
                });
            }
        }
    }
</script>

<style lang="scss" scoped>
    .form-container {
        /*box-shadow: 0 0px 8px 0 rgba(0, 0, 0, 0.06), 0 1px 0px 0 rgba(0, 0, 0, 0.02);*/
        -webkit-border-radius: 5px;
        border-radius: 5px;
        -moz-border-radius: 5px;
        background-clip: padding-box;
        margin: 180px auto;
        width: 350px;
        padding: 35px 35px 15px 35px;
        background: #fff;
        border: 1px solid #eaeaea;
        box-shadow: 0 0 25px #cac6c6;
        .title {
            margin: 0 auto 40px;
            text-align: center;
            color: #505458;
        }
        .remember {
            margin-bottom: 22px;
        }
        .el-button {
            width: 100%;
        }
    }
</style>