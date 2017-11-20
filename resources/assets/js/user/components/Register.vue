<template>
    <el-form class="form-container">
        <h3 class="title">系统注册</h3>
        <el-form-item prop="username">
            <el-input type="text" v-model="form.username" placeholder="账号"></el-input>
        </el-form-item>
        <el-form-item prop="password">
            <el-input type="password" v-model="form.password" placeholder="密码"></el-input>
        </el-form-item>
        <el-form-item prop="passwordConfirm">
            <el-input type="password" v-model="form.passwordConfirm" placeholder="确认密码"></el-input>
        </el-form-item>
        <el-form-item>
            <el-button style="width:100%;" @click.native.prevent="register" :loading="loading">注册</el-button>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" style="width:100%;" @click.native.prevent="login">登录
            </el-button>
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
                    passwordConfirm: ''
                }
            };
        },
        methods: {
            register: function () {

            },
            login: function () {
                this.$router.push({path: '/login'})
            }
        }
    }
</script>

<style lang="scss" scoped>
    .form-container {
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
    }
</style>