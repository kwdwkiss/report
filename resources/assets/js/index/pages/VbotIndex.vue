<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">微信工具</div>
            <div class="panel-body">

                <div class="row">
                    <button class="btn btn-primary" @click="doCreate" v-show="scanBtnShow">扫码登录</button>
                    <button class="btn btn-success" @click="doSend" v-show="sendBtnShow">开始清粉</button>
                    <button class="btn btn-danger" @click="doStop" v-show="stopBtnShow">终止任务</button>
                </div>
                <div class="row" v-show="statusShow">
                    <p class="col-xs-12">任务状态：{{statusLabel}}</p>
                    <p class="col-xs-6">获取二维码：{{data.uuid_status?'成功':''}}</p>
                    <p class="col-xs-6">扫描二维码：{{data.scan_status?'成功':''}}</p>
                    <p class="col-xs-6">登录状态：{{data.login_status?'成功':''}}</p>
                    <p class="col-xs-6">初始化用户：{{data.init_status?'成功':''}}</p>
                    <p class="col-xs-6">加载联系人：{{data.contacts_status?'成功':''}}</p>
                    <p class="col-xs-6">接收消息：{{data.message_status?'成功':''}}</p>
                </div>
                <div class="row" v-show="qrcodeShow">
                    <div class="hidden qrcode"></div>
                    <img :src="qrcode" alt="">
                    <p class="text-danger">注意：只能用微信扫一扫，通过摄像头扫码。通过二维码识别无效！</p>
                </div>

                <ul class="nav nav-tabs" id="myTabs">
                    <li class="active"><a href="#friends-admin" data-toggle="tab">好友列表</a></li>
                    <!--<li><a href="#multiple-send" data-toggle="tab">群发</a></li>-->
                    <!--<li><a href="#user-clear" data-toggle="tab">清粉</a></li>-->
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="friends-admin">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" v-model="checkAll">全选</th>
                                    <th>头像</th>
                                    <th>昵称</th>
                                    <th>备注</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in data.friends">
                                    <td><input type="checkbox" v-model="sendList" :value="item.UserName"></td>
                                    <td></td>
                                    <td>{{item.NickName}}</td>
                                    <td>{{item.RemarkName}}</td>
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
                data: {
                    qrcode: '',
                    uuid_status: '',
                    scan_status: '',
                    login_status: '',
                    init_status: '',
                    contacts_status: '',
                    message_status: '',
                    friends: {},
                    groups: {},
                    members: {},
                    officials: {},
                    specials: {},
                    myself: {},
                },
                handle: null,
                vbotJob: null,
                interval: 10000,//更新状态10秒一次
                checkAll: 0,
                sendList: [],
                sendText: '',
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
            },
            statusLabel: function () {
                if (!this.vbotJob) {
                    return '';
                }
                return this.vbotJob.status_label;
            },
            qrcode: function () {
                if (!this.data.qrcode) {
                    return '';
                }
                return $('.qrcode').empty()
                    .qrcode({width: 128, height: 128, text: this.data.qrcode})
                    .find('canvas').get(0).toDataURL('image/jpg');
            },
            scanBtnShow: function () {
                return !this.vbotJob;
            },
            sendBtnShow: function () {
                return this.vbotJob && this.data.message_status;
            },
            stopBtnShow: function () {
                return this.vbotJob && this.vbotJob.status !== 0;
            },
            statusShow: function () {
                return this.vbotJob;
            },
            qrcodeShow: function () {
                return this.data.qrcode && this.data.uuid_status && !this.data.login_status;
            },
            vbot: function () {
                return this.$store.state.vbot;
            }
        },
        watch: {
            'checkAll': function () {
                if (this.checkAll) {
                    this.sendList = _.keys(this.data.friends);
                } else {
                    this.sendList = [];
                }
            }
        },
        mounted: function () {
            this.init();
            this.loop();

            $('#myTabs a').click(function (e) {
                e.preventDefault();
                $(this).tab('show')
            })
        },
        methods: {
            init: function () {
                clearInterval(this.handle);
                this.$store.commit('vbot', {stop: 0});
                this.data = {
                    qrcode: '',
                    uuid_status: '',
                    scan_status: '',
                    login_status: '',
                    init_status: '',
                    contacts_status: '',
                    message_status: '',
                    friends: {},
                    groups: {},
                    members: {},
                    officials: {},
                    specials: {},
                    myself: {},
                };
                this.handle = null;
                this.vbotJob = null;
                this.interval = 10000;
                this.checkAll = 0;
                this.sendList = [];
                this.sendText = '';
            },
            doCreate: function () {
                let self = this;
                if (!self.user) {
                    self.$message.error('请登录后再使用此功能');
                    return;
                }
                axios.post(api.userVbotCreate).then(function (res) {
                    self.$message.success('任务已创建');
                    setTimeout(self.loop, 1000);
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
                if (this.vbot.stop) {
                    self.init();
                    self.stop();
                    return;
                }
                axios.get(api.userVbotStatus).then(function (res) {
                    if (!res.data.vbotJob) {
                        self.init();
                        return;
                    }
                    self.vbotJob = res.data.vbotJob;
                    self.data = res.data.data;
                });
            },
            doStop: function () {
                let self = this;
                axios.post(api.userVbotStop).then(function (res) {
                    self.$message.warning('请等待任务终止,可能需要30秒左右的时间');
                });
            },
            doSend: function () {
                let self = this;
                axios.post(api.userVbotSend, {
                    'send_list': self.sendList,
                    'send_text': self.sendText,
                }).then(function (res) {
                    self.$message.warning('请等待发送信息');
                });
            }
        }
    }
</script>

<style scoped>

</style>