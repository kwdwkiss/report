<template>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">微信清粉工具——任务管理</div>
            <div class="panel-body">

                <div class="row">
                    <button class="btn btn-warning" @click="doReturn">返回任务列表</button>
                    <button class="btn btn-primary" @click="doRun" v-show="scanBtnShow">扫码登录</button>
                    <button class="btn btn-success" @click="doSend" v-show="sendBtnShow">开始清粉</button>
                    <button class="btn btn-danger" @click="doStop" v-show="stopBtnShow">停止任务</button>
                </div>
                <div class="row text-success">
                    <p class="col-xs-12 text-danger">提示：10分钟不操作（发送信息时除外），系统自动停止任务</p>
                    <p class="col-xs-12 text-danger" v-if="vbotJob.error_msg">错误信息：{{vbotJob.error_msg}}</p>
                    <p class="col-xs-6">任务状态：{{statusLabel}}</p>
                    <p class="col-xs-6">获取二维码：{{data.uuid_status?'成功':''}}</p>
                    <p class="col-xs-6">扫描二维码：{{data.scan_status?'成功':''}}</p>
                    <p class="col-xs-6">登录状态：{{data.login_status?'成功':''}}</p>
                    <p class="col-xs-6">初始化用户：{{data.init_status?'成功':''}}</p>
                    <p class="col-xs-6">加载联系人：{{data.contacts_status?'成功':''}}</p>
                    <p class="col-xs-6">接收消息：{{data.message_status?'成功':''}}</p>
                    <p class="col-xs-6">发送状态：{{data.send_status?'进行中':'未开始'}}</p>
                </div>
                <div class="row" v-show="qrcodeShow">
                    <div class="hidden qrcode"></div>
                    <p style="font-size: 16px;font-weight: bold;color: red">
                        注意：只能用微信扫一扫来扫码。图片识别二维码无效！
                    </p>
                    <img :src="qrcode" alt="">
                </div>

                <ul class="nav nav-tabs" id="myTabs">
                    <li class="active"><a href="#friends-admin" data-toggle="tab">好友列表</a></li>
                    <li><a href="#user-clear" data-toggle="tab">清粉设置</a></li>
                    <li><a href="#send-list" data-toggle="tab">发送列表</a></li>
                    <li><a href="#sent-list" data-toggle="tab">已发送</a></li>
                    <!--<li><a href="#unsent-list" data-toggle="tab">未发送</a></li>-->
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="friends-admin">
                        <div>
                            <button class="btn btn-primary" @click="doAddSend" v-show="addSendBtnShow">添加发送列表</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" v-model="checkAll">全选</th>
                                    <th>昵称</th>
                                    <th>备注</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in data.friends">
                                    <td><input type="checkbox" v-model="sendList" :value="item.UserName"></td>
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
                        <form class="form-horizontal row">
                            <div class="form-group">
                                <label class="col-md-2 control-label">发送消息</label>
                                <div class="col-md-8">
                                    <input class="form-control" type="text" v-model="sendText">
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane" id="send-list">
                        <div>
                            <button class="btn btn-danger" @click="doDeleteSend" v-show="deleteSendBtnShow">删除发送列表
                            </button>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th><input type="checkbox" v-model="deleteCheckAll">全选</th>
                                    <th>昵称</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in vbotJob.send_list">
                                    <td><input type="checkbox" v-model="deleteSendList" :value="item"></td>
                                    <td>{{item}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="sent-list">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>昵称</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in sentList">
                                    <td>{{item}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="unsent-list">
                        <div class="table-responsive">
                            <table>
                                <thead>
                                <tr>
                                    <th>昵称</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="item in unsentList">
                                    <td>{{item}}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
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
                vbotJob: {},
                data: {
                    qrcode: '',
                    uuid_status: '',
                    scan_status: '',
                    login_status: '',
                    init_status: '',
                    contacts_status: '',
                    message_status: '',
                    send_status: '',
                    friends: {},
                    groups: {},
                    members: {},
                    officials: {},
                    specials: {},
                    myself: {},
                    sent_list: [],
                },
                handle: null,
                interval: 10000,//更新状态10秒一次
                checkAll: 0,
                sendText: '由于微信好友太多，我正在使用宏海清粉软件，如有打扰请包涵。',
                deleteCheckAll: 0,
                deleteSendList: [],
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
                return !([-1, 1].indexOf(this.vbotJob.status) > -1);
            },
            addSendBtnShow: function () {
                return this.data.contacts_status && !this.data.send_status;
            },
            deleteSendBtnShow: function () {
                return this.vbotJob.send_list.length > 0 && !this.data.send_status;
            },
            sendBtnShow: function () {
                return this.vbotJob.send_list.length > 0 && this.data.message_status;
            },
            stopBtnShow: function () {
                return this.vbotJob.status === 1;
            },
            qrcodeShow: function () {
                return this.data.qrcode && this.data.uuid_status && !this.data.login_status;
            },
            sentList: function () {
                if (this.vbotJob !== 1) {
                    return this.vbotJob.sent_list;
                }
                return this.data.sent_list;
            },
            unsentList: function () {

            }
        },
        watch: {
            'checkAll': function () {
                if (this.checkAll) {
                    this.sendList = _.keys(this.data.friends);
                } else {
                    this.sendList = [];
                }
            },
            'deleteCheckAll': function () {
                if (this.deleteCheckAll) {
                    this.deleteSendList = this.vbotJob.send_list;
                } else {
                    this.deleteSendList = [];
                }
            }
        },
        mounted: function () {
            this.init();
            this.loadData();

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
                    send_status: '',
                    friends: {},
                    groups: {},
                    members: {},
                    officials: {},
                    specials: {},
                    myself: {},
                    sent_list: [],
                };
                this.handle = null;
                this.interval = 10000;
                this.checkAll = 0;
                this.sendList = [];
                this.deleteCheckAll = 0;
                this.deleteSendList = [];
            },
            loadData: function () {
                let self = this;
                let id = self.$route.params.id;
                axios.get(api.userVbotDetail, {params: {id: id}}).then(function (res) {
                    self.vbotJob = res.data;
                    if (self.vbotJob.status === 1) {
                        self.loop();
                    }
                });
            },
            doReturn: function () {
                this.$router.push({name: 'vbotIndex'});
            },
            doRun: function () {
                let self = this;
                axios.post(api.userVbotRun, {id: self.vbotJob.id}).then(function (res) {
                    self.$message.success('任务已运行');
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
                axios.get(api.userVbotStatus, {params: {id: self.vbotJob.id}}).then(function (res) {
                    self.vbotJob = res.data.vbotJob;
                    self.data = res.data.data;
                    if (self.vbotJob.status !== 1) {
                        self.init();
                        self.doStop();
                    }
                });
            },
            doAddSend: function () {
                let self = this;
                let sendList = [];
                let pick = _.pick(self.data.friends, self.sendList);
                for (let index in pick) {
                    sendList.push(pick[index]['NickName']);
                }
                axios.post(api.userVbotAddSend, {
                    send_list: sendList,
                    id: self.vbotJob.id,
                }).then(function (res) {
                    self.checkAll = 0;
                    self.sendList = [];
                    self.$message.success('添加发送列表成功，10秒后刷新数据');
                });
            },
            doDeleteSend: function () {
                let self = this;
                axios.post(api.userVbotDeleteSend, {
                    send_list: self.deleteSendList,
                    id: self.vbotJob.id,
                }).then(function (res) {
                    self.deleteCheckAll = 0;
                    self.deleteSendList = [];
                    self.$message.success('删除发送列表成功，10秒后刷新数据');
                });
            },
            doStop: function () {
                let self = this;
                axios.post(api.userVbotStop, {id: self.vbotJob.id}).then(function (res) {
                    self.$message.success('请等待任务停止,可能需要30秒左右的时间');
                });
            },
            doSend: function () {
                let self = this;
                axios.post(api.userVbotSend, {
                    id: self.vbotJob.id,
                    send_list: self.sendList,
                    send_text: self.sendText,
                }).then(function (res) {
                    self.$message.success('发送状态就绪后，任务自动发送信息');
                });
            }
        }
    }
</script>

<style scoped>

</style>