<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">微信清粉</div>
            <div class="panel-body">
                <div class="row">
                    <button class="btn btn-primary" @click="doCreate">创建任务</button>
                </div>
                <div class="row" v-show="vbotJob">
                    <p>任务状态：{{status_label}}</p>
                </div>
                <div class="row" v-show="vbotJob">
                    <div class="hidden qrcode"></div>
                    <img :src="qrcode_url" alt="">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "VbotIndex",
        data: function () {
            return {
                qrcode_url: '',
                handle: null,
                vbotJob: null,
                interval: 10000,//更新状态10秒一次
                status_label: '',
            }
        },
        computed: {
            user: function () {
                return this.$store.state.user;
            },
        },
        watch: {
            '$route': function (to, from) {
                if (this.$route.name !== 'vbotIndex') {
                    clearInterval(self.handle);
                }
            }
        },
        created: function () {
            this.init();
            this.loop();
        },
        methods: {
            init: function () {
                this.qrcode_url = '';
                this.handle = null;
                this.vbotJob = null;
                this.interval = 10000;
            },
            doCreate: function () {
                let self = this;
                if (!self.user) {
                    self.$message.error('请登录后再使用此功能');
                    return;
                }
                self.init();
                axios.post(api.userVbotCreate).then(function (res) {
                    self.$message.success('任务已创建，请等待执行完毕后再创建新任务');
                    self.loop();
                })
            },
            loop: function () {
                let self = this;
                self.doStatus();
                self.handle = setInterval(function () {
                    self.doStatus();
                }, self.interval);
            },
            doStatus: function () {
                let self = this;
                axios.get(api.userVbotStatus).then(function (res) {
                    self.vbotJob = res.data.data;
                    self.status_label = self.vbotJob.status_label;
                    if (self.vbotJob) {
                        switch (self.vbotJob.status) {
                            case -2:
                                clearInterval(self.handle);
                                self.$message.success('任务异常退出，请联系客服');
                                self.init();
                                break;
                            case -1:
                                clearInterval(self.handle);
                                self.$message.success('任务已完成');
                                self.init();
                                break;
                            case 0:
                                break;
                            case 1:
                                let url = self.vbotJob.qrcode_url;
                                $('.qrcode').empty();
                                let canvas = $('.qrcode').qrcode({width: 128, height: 128, text: url}).find('canvas');
                                self.qrcode_url = canvas.get(0).toDataURL('image/jpg');
                                break;
                            case 2:
                                self.qrcode_url = '';
                                break;
                            case 3:
                                break;
                        }
                    }
                })
            }
        }
    }
</script>

<style scoped>

</style>