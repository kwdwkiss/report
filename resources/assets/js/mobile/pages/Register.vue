<template>
    <div>
        <mt-header title="注册">
            <router-link to="/" slot="left">
                <mt-button icon="back" @click="goBack">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="row">
            <div class="col-xs-12">手机号</div>
            <div class="col-xs-12">
                <input class="form-control" type="text" placeholder="请输入手机号" v-model="form.mobile">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">新密码</div>
            <div class="col-xs-12">
                <input class="form-control" type="password" placeholder="请输入密码" v-model="form.password">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">邀请人</div>
            <div class="col-xs-12">
                <input class="form-control" type="text" placeholder="请输入邀请人手机号" v-model="form.inviter">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">短信验证码</div>
            <div class="col-xs-12">
                <input style="" class="form-control" type="text" placeholder="请输入短信验证码" v-model="form.code">
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <button class="btn btn-warning col-xs-12" @click="sendSms"
                        v-bind:disabled="smsDisable">{{smsText}}
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <label>
                    <input type="checkbox" v-model="form.remember"> 记住我
                </label>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <button class="btn btn-success col-xs-12" @click="register">注册</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Register",
        data: function () {
            return {
                form: {
                    remember: true
                },
                smsDisable: false,
                smsText: '发送短信',
                smsTimer: 60,
            }
        },
        methods: {
            goBack: function () {
                this.$router.go(-1);
            },
            register: function () {
                let self = this;
                axios.post(api.userRegister, self.form).then(function (res) {
                    self.$store.commit('user', {
                        callback: function () {
                            self.$router.push('/user');
                        }
                    });
                });
            },
            smsInit: function () {
                clearInterval(this.smsHandle);
                this.smsText = '发送短信';
                this.smsTimer = 60;
                this.smsDisable = false;
            },
            sendSms: function () {
                let self = this;
                axios.post(api.userSms, self.form).then(function (res) {
                    self.$message.success('短信发送成功，请耐心等候，如有疑问请联系客服');
                    self.smsDisable = true;
                    self.smsHandle = setInterval(function () {
                        if (self.smsTimer > 0) {
                            self.smsText = '还有' + self.smsTimer + '秒';
                            self.smsTimer--;
                        } else {
                            self.smsInit();
                        }
                    }, 1000);
                });
            },
        }
    }
</script>

<style scoped>

</style>