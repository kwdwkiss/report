<template>
    <div class="row">
        <div class="form-horizontal">
            <div class="form-group">
                <label class="col-md-3 control-label">手机</label>
                <div class="col-md-6">
                    <input class="form-control" type="text" v-model="mobile">
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
                    <button class="btn btn-primary" @click="login">登录</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "EmulatorTb",
        data: function () {
            return {
                mobile: '',
                code: ''
            }
        },
        mounted: function () {
            this.login();
        },
        methods: {
            login: function () {
                axios.get(api.emulatorPddLogin, {
                    mobile: this.mobile,
                    code: this.code
                }).then(function (res) {
                });
            },
            sms: function () {
                axios.post(api.emulatorPddSms, {mobile: this.mobile}).then(function (res) {
                });
            }
        }
    }
</script>

<style scoped>

</style>