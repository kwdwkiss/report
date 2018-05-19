<template>
    <button type="button" class="btn btn-success" @click="sendSms" v-bind:disabled="smsDisable">{{smsText}}</button>
</template>

<script>
    export default {
        name: "Sms",
        data: function () {
            return {
                smsText: "发送短信",
                smsDisable: false,
                smsTimer: 60,
                smsHandle: null,
            }
        },
        props: ['mobile'],
        methods: {
            smsInit: function () {
                this.smsText = "发送短信";
                this.smsTimer = 60;
                this.smsDisable = false;
                clearInterval(this.smsHandle);
            },
            sendSms: function () {
                let self = this;
                axios.post(api.userSms, {mobile: self.mobile})
                    .then(function (res) {
                        self.$message.success("短信发送成功，请耐心等候，如有疑问请联系客服");
                        self.smsDisable = true;
                        self.smsHandle = setInterval(function () {
                            if (self.smsTimer > 0) {
                                self.smsText = "还有" + self.smsTimer + "秒";
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