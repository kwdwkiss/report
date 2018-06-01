<template>
    <div class="row">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-md-3 control-label">账号名/手机</label>
                <div class="col-md-6">
                    <input class="form-control" type="text" v-model="username">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">密码</label>
                <div class="col-md-6">
                    <input class="form-control" type="text" v-model="password">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">图形验证码</label>
                <div class="col-md-6">
                    <input class="form-control" type="text" v-model="authCode">
                </div>
                <div class="col-md-2">
                    <img :src="captcha_src" alt="">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-3 control-label">验证码</label>
                <div class="col-md-6">
                    <input class="form-control" type="text" v-model="code">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success" @click="sms">发送短信</button>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-offset-3 col-md-6">
                    <button class="btn btn-primary">登录</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "EmulatorPddMms",
        data: function () {
            return {
                username: '',
                password: '',
                authCode: '',
                sign: '',
                captcha_src: '',
                code: '',
            }
        },
        mounted: function () {
            this.login();
        },
        methods: {
            login: function () {
                let self = this;
                axios.get(api.emulatorPddMmsLogin).then(function (res) {
                    self.captcha();
                });
            },
            doLogin: function () {

            },
            captcha: function () {
                let self = this;
                axios.get(api.emulatorPddMmsCaptcha).then(function (res) {
                    self.sign = res.data.data.result.sign;
                    self.sign = res.data.data.result.picture;
                });
            },
            sms: function () {

            }
        }
    }
</script>

<style scoped>

</style>