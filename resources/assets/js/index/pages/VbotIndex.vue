<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">微信工具</div>
            <div class="panel-body">

                <div class="row">
                    <button class="btn btn-primary" @click="doCreate" v-show="!vbotJob">扫码登录</button>
                    <button class="btn btn-danger" @click="doStop" v-show="status===1">终止任务</button>
                </div>
                <div class="row" v-show="vbotJob">
                    <p>任务状态：{{status_label}}</p>
                </div>
                <div class="row" v-show="vbotJob">
                    <div class="hidden qrcode"></div>
                    <img :src="qrcdoe" alt="">
                </div>

                <ul class="nav nav-tabs" id="myTabs">
                    <li class="active"><a href="#friends-admin" data-toggle="tab">好友管理</a></li>
                    <li><a href="#multiple-send" data-toggle="tab">群发</a></li>
                    <li><a href="#user-clear" data-toggle="tab">清粉</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="friends-admin">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>昵称</th>
                                    <th>备注</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="multiple-send">

                    </div>
                    <div class="tab-pane" id="user-clear">

                    </div>
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
                qrcdoe: '',
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
            status: function () {
                if (!this.vbotJob) {
                    return 0;
                }
                return this.vbotJob.status;
            }
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

            $('#myTabs a').click(function (e) {
                e.preventDefault()
                $(this).tab('show')
            })
        },
        methods: {
            init: function () {
                this.qrcdoe = '';
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
                    self.$message.success('任务已创建');
                    self.loop();
                })
            },
            loop: function () {
                let self = this;
                setTimeout(self.doStatus, 1000);
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
                                break;
                        }
                        if (self.vbotJob.qrcode) {
                            let url = self.vbotJob.qrcode;
                            $('.qrcode').empty();
                            let canvas = $('.qrcode')
                                .qrcode({width: 128, height: 128, text: url})
                                .find('canvas');
                            self.qrcdoe = canvas.get(0).toDataURL('image/jpg');
                        }
                    } else {
                        clearInterval(self.handle);
                        self.init();
                    }
                })
            },
            doStop: function () {
                let self = this;
                axios.get(api.userVbotStop).then(function (res) {
                    self.$message.success('请等待任务终止');
                });
            }
        }
    }
</script>

<style scoped>

</style>